<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;
use BluecadetEvents\Templates\QuerySetters;
use BluecadetEvents\Helpers\TemplateHelpers;
use BluecadetEvents\Plugin\Hooks;

/**
 * The past/upcoming view resolves from the past_parameter query string, and a
 * theme can layer its own source (a rewrite tag, a path segment) on top via
 * the bc_events/events/archive/is_past filter.
 *
 * @group query
 */
class ViewFilterTest extends TestCase {

	public function tear_down(): void {
		global $wp, $wp_query;

		unset( $_GET['is-past'], $_GET['week-of'], $_GET['tag'] );
		$wp->request = '';
		unset( $wp_query->bce_view_wrapping_dates );

		parent::tear_down();
	}

	/** Put a week view's pagination state on the global query. */
	private function stage_week_pagination( string $request_path = '' ): void {
		global $wp, $wp_query;

		$wp->request = $request_path;

		$wp_query->bce_view_wrapping_dates = [
			'pagination_param' => 'week-of',
			'current_string'   => '2026-09-21',
			'current_date'     => new \DateTime( '2026-09-21', wp_timezone() ),
			'next_string'      => '2026-09-28',
			'next_date'        => new \DateTime( '2026-09-28', wp_timezone() ),
			'prev_string'      => '2026-09-14',
			'prev_date'        => new \DateTime( '2026-09-14', wp_timezone() ),
		];
	}

	public function test_pagination_links_keep_a_rewritten_view_path(): void {
		// Pretty permalinks: the real shape of a rewrite-served view.
		$this->set_permalink_structure( '/%postname%/' );
		$this->stage_week_pagination( 'events/past' );

		$links = TemplateHelpers::getInstance()->get_view_pagination_links();

		$this->assertIsArray( $links );
		$this->assertStringContainsString( '/events/past/?week-of=2026-09-14', $links['prev'] );
		$this->assertStringContainsString( '/events/past/?week-of=2026-09-28', $links['next'] );

		// The home path is not doubled on the way through home_url().
		$this->assertSame( 1, substr_count( $links['next'], '/events/past' ) );
	}

	public function test_pagination_links_follow_a_plain_permalink_structure(): void {
		$this->set_permalink_structure( '' );
		$this->stage_week_pagination( 'events/past' );

		$links = TemplateHelpers::getInstance()->get_view_pagination_links();

		// No trailing slash when the site has no permalink structure.
		$this->assertStringContainsString( '/events/past?week-of=2026-09-28', $links['next'] );
	}

	public function test_pagination_links_carry_other_params_and_drop_stale_view_dates(): void {
		$this->stage_week_pagination( 'events' );

		$_GET['tag']     = 'family';
		$_GET['week-of'] = '2026-09-21';

		$links = TemplateHelpers::getInstance()->get_view_pagination_links();

		$this->assertStringContainsString( 'tag=family', $links['next'], 'Other query args are carried.' );
		// The param being paged is replaced, never duplicated.
		$this->assertSame( 1, substr_count( $links['next'], 'week-of=' ) );
		$this->assertStringContainsString( 'week-of=2026-09-28', $links['next'] );
	}

	public function test_pagination_links_fall_back_to_the_archive_without_a_request(): void {
		$this->stage_week_pagination( '' );

		$links = TemplateHelpers::getInstance()->get_view_pagination_links();

		$this->assertStringContainsString(
			get_post_type_archive_link( \BluecadetEvents\Plugin\Settings::$events_machine_name ),
			$links['prev']
		);
	}

	/** The rewrite-tag pattern from the hook docs: resolve the view from a query var. */
	private function add_rewrite_tag_filter(): void {
		add_filter(
			'bc_events/events/archive/is_past',
			static function ( $is_past, $query ) {
				$view = $query ? $query->get( 'event_view_filter' ) : get_query_var( 'event_view_filter' );

				if ( $view === 'past' ) {
					return true;
				}
				if ( $view === 'upcoming' ) {
					return false;
				}

				return $is_past;
			},
			10,
			2
		);
	}

	/** Run the list view against a query and hand back the query. */
	private function list_view( \WP_Query $query ): \WP_Query {
		( new QuerySetters( $query ) )->set_query( 'list' );
		return $query;
	}

	public function test_past_parameter_still_drives_the_view(): void {
		$_GET['is-past'] = '1';

		$query = $this->list_view( new \WP_Query() );

		$this->assertSame( 'past', $query->get( 'bc_events_query' ) );
		$this->assertSame( 'DESC', $query->get( 'order' ) );
		$this->assertSame( 'past', $query->bce_status );
	}

	public function test_no_parameter_is_the_upcoming_view(): void {
		$query = $this->list_view( new \WP_Query() );

		$this->assertSame( 'upcoming', $query->get( 'bc_events_query' ) );
		$this->assertSame( 'ASC', $query->get( 'order' ) );
		$this->assertSame( 'upcoming', $query->bce_status );
	}

	public function test_a_rewrite_tag_can_set_the_past_view_without_the_parameter(): void {
		$this->add_rewrite_tag_filter();

		$query = new \WP_Query();
		$query->set( 'event_view_filter', 'past' );

		$this->list_view( $query );

		$this->assertSame( 'past', $query->get( 'bc_events_query' ) );
		$this->assertSame( 'DESC', $query->get( 'order' ) );
		$this->assertSame( 'past', $query->bce_status );
	}

	public function test_a_rewrite_tag_can_force_upcoming_over_the_parameter(): void {
		$this->add_rewrite_tag_filter();
		$_GET['is-past'] = '1';

		$query = new \WP_Query();
		$query->set( 'event_view_filter', 'upcoming' );

		$this->list_view( $query );

		$this->assertSame( 'upcoming', $query->get( 'bc_events_query' ) );
		$this->assertSame( 'ASC', $query->get( 'order' ) );
	}

	public function test_template_helper_agrees_with_the_query(): void {
		$helpers = TemplateHelpers::getInstance();

		$this->assertFalse( $helpers->is_past(), 'No parameter, no filter: upcoming.' );

		$_GET['is-past'] = '1';
		$this->assertTrue( $helpers->is_past(), 'The parameter still works on its own.' );

		// A theme resolving the view from the global query must move the template
		// helper too, not just the archive query.
		unset( $_GET['is-past'] );
		$this->add_rewrite_tag_filter();

		$GLOBALS['wp_query']->set( 'event_view_filter', 'past' );
		$this->assertTrue( $helpers->is_past(), 'The filter must reach the template helper.' );
	}

	public function test_hook_passes_the_query_through_untouched_by_default(): void {
		$query = new \WP_Query();

		$this->assertFalse( Hooks::hook_filter_is_past( false, $query ) );
		$this->assertTrue( Hooks::hook_filter_is_past( true, $query ) );
		$this->assertFalse( Hooks::hook_filter_is_past( false ) );
	}
}
