<?php

namespace BluecadetEvents\Tests;

use WP_UnitTestCase;
use WP_REST_Request;
use WP_REST_Server;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Plugin\BackgroundProcesses;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;
use BluecadetEvents\Admin\PostTypes\PostTypes;
use BluecadetEvents\Admin\Meta\Register\RegisterEventMeta;
use BluecadetEvents\Admin\Meta\Register\RegisterLocationMeta;
use BluecadetEvents\Admin\Meta\Register\RegisterSeriesMeta;

/**
 * Base test case for Bluecadet Events integration tests.
 *
 * Provides:
 *  - synchronous background processing (recurring children exist right after a save)
 *  - an authenticated administrator (both save paths require caps)
 *  - two save "drivers" (classic $_POST + Gutenberg REST) sharing one signature
 *  - helpers to read the custom table / child events / queue state
 */
abstract class TestCase extends WP_UnitTestCase {

	protected int $admin_id;

	public function set_up(): void {
		parent::set_up();

		Settings::init();

		// WP_UnitTestCase resets global registration (post types, registered
		// meta) between tests, but the plugin only registers on 'init', which
		// fires once at bootstrap. Re-register the post types and meta each test
		// relies on — critically the show_in_rest meta the REST/Gutenberg save
		// path depends on. (The classic path writes meta directly and would
		// otherwise mask this.) We call the registrars directly rather than
		// re-firing 'init' so we don't re-register Gutenberg blocks, which is not
		// idempotent.
		$this->register_plugin();

		// Drain the recurrence background queue inline instead of via loopback.
		add_filter( 'bc_events/background/sync', '__return_true' );

		// Both save paths gate on capabilities.
		$this->admin_id = self::factory()->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $this->admin_id );
	}

	public function tear_down(): void {
		$_POST = [];
		remove_filter( 'bc_events/background/sync', '__return_true' );
		parent::tear_down();
	}

	/**
	 * Re-run the plugin's post-type and meta registration (normally done once on
	 * 'init'), so each isolated test has the CPTs + show_in_rest meta available.
	 * Idempotent: register_post_type / register_post_meta overwrite on re-run.
	 */
	private function register_plugin(): void {
		$post_types = new PostTypes();
		$post_types->register();
		$post_types->register_custom_post_types();

		foreach ( [ RegisterEventMeta::class, RegisterLocationMeta::class, RegisterSeriesMeta::class ] as $registrar ) {
			$reg = new $registrar();
			$reg->register();
			$reg->register_meta();
		}
	}

	/** Logical-name => full meta key map for events. */
	protected function keys(): array {
		return EventsMetaKeys::get_keys();
	}

	/** Epoch for a Y-m-d H:i:s string interpreted in the site timezone. */
	protected function site_ts( string $datetime ): int {
		return ( new \DateTime( $datetime, wp_timezone() ) )->getTimestamp();
	}

	/** Create and save a single (non-recurring) event; returns the post id. */
	protected function make_single_event(): int {
		$k  = $this->keys();
		$id = $this->new_event();
		$this->save_via_post( $id, [
			$k['start_date']      => '2026-09-07',
			$k['start_time']      => '09:00',
			$k['start_timestamp'] => $this->site_ts( '2026-09-07 09:00:00' ),
			$k['end_date']        => '2026-09-07',
			$k['end_time']        => '11:00',
			$k['end_timestamp']   => $this->site_ts( '2026-09-07 11:00:00' ),
		] );
		return $id;
	}

	/**
	 * Create and save a weekly-on-Mondays recurring master (5 occurrences by
	 * default: 2026-09-07 .. 2026-10-05). Returns the master post id.
	 */
	protected function make_weekly_recurring_event( array $overrides = [] ): int {
		$k       = $this->keys();
		$id      = $this->new_event();
		$payload = array_merge( [
			$k['start_date']      => '2026-09-07',
			$k['start_time']      => '09:00',
			$k['start_timestamp'] => $this->site_ts( '2026-09-07 09:00:00' ),
			$k['end_date']        => '2026-09-07',
			$k['end_time']        => '11:00',
			$k['end_timestamp']   => $this->site_ts( '2026-09-07 11:00:00' ),
			$k['is_recurring']    => '1',
			$k['use_frequency']   => '1',
			$k['freq']            => 'weekly',
			$k['freq_days']       => [ 'monday' ],
			$k['freq_end_type']   => 'on_date',
			$k['freq_end_date']   => '2026-10-05',
		], $overrides );

		$this->save_via_post( $id, $payload );
		return $id;
	}

	/** Create a published bc-events post with no event meta yet. */
	protected function new_event( array $postarr = [] ): int {
		return self::factory()->post->create(
			array_merge(
				[
					'post_type'   => Settings::$events_machine_name,
					'post_title'  => 'Test Event',
					'post_status' => 'publish',
				],
				$postarr
			)
		);
	}

	/**
	 * Classic-editor save path: populate $_POST (nonce + managed keys) and fire
	 * save_post, which writes meta from $_POST, then wp_after_insert_post runs
	 * the recurrence engine.
	 *
	 * @param array<string,mixed> $meta Keyed by FULL meta key.
	 */
	protected function save_via_post( int $post_id, array $meta, string $title = 'Test Event', string $content = '' ): void {
		$_POST                    = [];
		$_POST['post_title']      = $title;
		$_POST['bc_meta_nonce']   = wp_create_nonce( 'bc_save_meta' );
		$_POST['bc_managed_keys'] = array_keys( $meta );

		foreach ( $meta as $key => $value ) {
			$_POST[ $key ] = $value;
		}

		// Fires save_post (meta write) then wp_after_insert_post (engine).
		wp_update_post( [ 'ID' => $post_id, 'post_title' => $title, 'post_content' => $content ] );

		$_POST = [];
	}

	/**
	 * Gutenberg save path: write meta through the REST API (exercising the
	 * register_post_meta sanitize_callbacks), which triggers wp_after_insert_post.
	 *
	 * @param array<string,mixed> $meta Keyed by FULL meta key.
	 */
	protected function save_via_rest( int $post_id, array $meta, string $title = 'Test Event' ): void {
		$this->ensure_rest_server();

		$request = new WP_REST_Request( 'POST', '/wp/v2/' . Settings::$events_machine_name . '/' . $post_id );
		$request->set_body_params(
			[
				'title' => $title,
				'meta'  => $meta,
			]
		);

		$response = rest_do_request( $request );

		$this->assertLessThan(
			300,
			$response->get_status(),
			'REST save failed: ' . wp_json_encode( $response->get_data() )
		);
	}

	private function ensure_rest_server(): void {
		global $wp_rest_server;

		if ( ! ( $wp_rest_server instanceof WP_REST_Server ) ) {
			$wp_rest_server = new WP_REST_Server();
			do_action( 'rest_api_init', $wp_rest_server );
		}
	}

	/** The DatabaseHelpers singleton. */
	protected function db(): \BluecadetEvents\Admin\Utils\DatabaseHelpers {
		return \BluecadetEvents\Admin\Utils\DatabaseHelpers::get_instance();
	}

	/** Custom-table row for a post, or null. */
	protected function event_row( int $post_id ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . Settings::$events_table;

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE post_id = %d", $post_id )
		) ?: null;
	}

	/** Child event post IDs for a parent, ordered by start (from the custom table). */
	protected function child_event_ids( int $parent_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . Settings::$events_table;

		return array_map(
			'intval',
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT post_id FROM {$table} WHERE parent_ID = %d ORDER BY event_start ASC",
					$parent_id
				)
			)
		);
	}

	/** Assert both background queues are empty (proves the inline drain ran). */
	protected function assertQueuesDrained(): void {
		$this->assertFalse(
			BackgroundProcesses::get_event_handler()->is_active(),
			'Recurring event background queue was not drained.'
		);
		$this->assertFalse(
			BackgroundProcesses::get_event_delete_handler()->is_active(),
			'Recurring delete background queue was not drained.'
		);
	}
}
