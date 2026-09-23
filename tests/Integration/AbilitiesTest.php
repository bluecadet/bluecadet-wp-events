<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;
use BluecadetEvents\Abilities\Abilities;
use BluecadetEvents\Plugin\Settings;

/**
 * Abilities API: the bc-events/* abilities create and update events through the
 * same save path as the editor, and only expose what the site's filters enable.
 *
 * @group abilities
 */
class AbilitiesTest extends TestCase {

	public function set_up(): void {
		parent::set_up();

		if ( ! function_exists( 'wp_get_ability' ) ) {
			$this->markTestSkipped( 'Abilities API requires WordPress 6.9+.' );
		}

		register_taxonomy( 'event_type', Settings::$events_machine_name, [ 'show_ui' => true ] );
	}

	private function run_ability( string $name, $input = null ) {
		$ability = wp_get_ability( 'bc-events/' . $name );
		$this->assertNotNull( $ability, "bc-events/{$name} is not registered" );
		return $ability->execute( $input );
	}

	private function single_event_input( array $overrides = [] ): array {
		return array_merge( [
			'title'      => 'Ability Event',
			'status'     => 'publish',
			'start_date' => '2026-09-07',
			'start_time' => '09:00',
			'end_date'   => '2026-09-07',
			'end_time'   => '11:00',
		], $overrides );
	}

	public function test_create_derives_timestamps_and_writes_the_table_row(): void {
		$input = $this->single_event_input();
		unset( $input['status'] );

		$event = $this->run_ability( 'create-event', $input );

		$this->assertIsArray( $event, is_wp_error( $event ) ? $event->get_error_message() : '' );
		$this->assertSame( 'draft', $event['status'] );

		$k = $this->keys();
		$this->assertSame( $this->site_ts( '2026-09-07 09:00:00' ), (int) get_post_meta( $event['id'], $k['start_timestamp'], true ) );
		$this->assertSame( $this->site_ts( '2026-09-07 11:00:00' ), (int) get_post_meta( $event['id'], $k['end_timestamp'], true ) );
		$this->assertNotNull( $this->event_row( $event['id'] ) );
	}

	public function test_create_recurring_generates_occurrences_with_the_parents_terms(): void {
		$term = self::factory()->term->create( [ 'taxonomy' => 'event_type', 'slug' => 'lecture' ] );

		$event = $this->run_ability( 'create-event', $this->single_event_input( [
			'is_recurring'  => true,
			'use_frequency' => true,
			'freq'          => 'weekly',
			'freq_days'     => [ 'monday' ],
			'freq_end_type' => 'on_date',
			'freq_end_date' => '2026-10-05',
			'terms'         => [ 'event_type' => [ 'lecture' ] ],
		] ) );

		$this->assertIsArray( $event, is_wp_error( $event ) ? $event->get_error_message() : '' );

		$children = $this->child_event_ids( $event['id'] );
		$this->assertCount( 5, $children );

		foreach ( $children as $child_id ) {
			$this->assertSame( [ $term ], wp_get_object_terms( $child_id, 'event_type', [ 'fields' => 'ids' ] ) );
		}
	}

	public function test_create_rejects_what_the_editor_rejects(): void {
		$backwards = $this->run_ability( 'create-event', $this->single_event_input( [ 'end_time' => '08:00' ] ) );
		$this->assertWPError( $backwards );
		$this->assertSame( 'bc_events_invalid_range', $backwards->get_error_code() );

		$no_days = $this->run_ability( 'create-event', $this->single_event_input( [
			'is_recurring'  => true,
			'use_frequency' => true,
			'freq'          => 'weekly',
			'freq_end_date' => '2026-10-05',
		] ) );
		$this->assertWPError( $no_days );
		$this->assertStringContainsString( 'freq_days', $no_days->get_error_message() );

		$no_strategy = $this->run_ability( 'create-event', $this->single_event_input( [ 'is_recurring' => true ] ) );
		$this->assertSame( 'bc_events_missing_recurrence', $no_strategy->get_error_code() );
	}

	public function test_terms_are_never_created_and_must_be_on_an_event_taxonomy(): void {
		$missing = $this->run_ability( 'create-event', $this->single_event_input( [ 'terms' => [ 'event_type' => [ 'nope' ] ] ] ) );
		$this->assertSame( 'bc_events_term_not_found', $missing->get_error_code() );
		$this->assertEmpty( term_exists( 'nope', 'event_type' ) );

		$wrong_tax = $this->run_ability( 'create-event', $this->single_event_input( [ 'terms' => [ 'category' => [ 1 ] ] ] ) );
		$this->assertSame( 'bc_events_invalid_taxonomy', $wrong_tax->get_error_code() );
	}

	public function test_update_recalculates_timestamps_from_merged_dates(): void {
		$event   = $this->run_ability( 'create-event', $this->single_event_input() );
		$updated = $this->run_ability( 'update-event', [ 'id' => $event['id'], 'end_time' => '12:30' ] );

		$this->assertIsArray( $updated, is_wp_error( $updated ) ? $updated->get_error_message() : '' );
		$this->assertSame(
			$this->site_ts( '2026-09-07 12:30:00' ),
			(int) get_post_meta( $event['id'], $this->keys()['end_timestamp'], true )
		);
		$this->assertSame( '09:00', $updated['fields']['start_time'] );
	}

	public function test_update_refuses_parent_fields_on_an_occurrence(): void {
		$parent = $this->make_weekly_recurring_event();
		$child  = $this->child_event_ids( $parent )[1];

		$result = $this->run_ability( 'update-event', [ 'id' => $child, 'start_time' => '10:00' ] );
		$this->assertSame( 'bc_events_child_field', $result->get_error_code() );

		$ok = $this->run_ability( 'update-event', [ 'id' => $child, 'child_deny_override' => true ] );
		$this->assertIsArray( $ok, is_wp_error( $ok ) ? $ok->get_error_message() : '' );
		$this->assertTrue( $ok['fields']['child_deny_override'] );
	}

	public function test_get_settings_reports_runtime_taxonomies(): void {
		$settings = $this->run_ability( 'get-settings', [] );

		$this->assertContains( 'event_type', wp_list_pluck( $settings['taxonomies'], 'slug' ) );
		$this->assertArrayHasKey( 'weekly', $settings['frequency_options'] );
	}

	public function test_schema_follows_the_site_filters(): void {
		$abilities = new Abilities();

		$fields = $abilities->event_field_schemas();
		$this->assertArrayNotHasKey( 'virtual_event', $fields );
		$this->assertArrayNotHasKey( 'virtual_url', $fields );
		$this->assertArrayHasKey( 'location_ids', $fields );

		add_filter( 'bc_events/locations/post_type/use_event_locations', '__return_false' );
		add_filter( 'bc_events/series/post_type/use_event_series', '__return_false' );
		add_filter( 'bc_events/events/display/use_event_recurring_description', '__return_false' );
		add_filter( 'bc_events/events/settings/frequency_options', fn() => [ 'weekly' => 'Weekly' ] );

		$fields = $abilities->event_field_schemas();
		$this->assertArrayNotHasKey( 'location_ids', $fields );
		$this->assertArrayNotHasKey( 'series_ids', $fields );
		$this->assertArrayNotHasKey( 'recur_desc', $fields );
		$this->assertSame( [ 'weekly' ], $fields['freq']['enum'] );
	}
}
