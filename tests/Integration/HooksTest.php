<?php

namespace BluecadetEvents\Tests\Integration;

use WP_UnitTestCase;
use BluecadetEvents\Plugin\Hooks;

/**
 * The filter surface must be defensive: a user filter returning the wrong type
 * or garbage must never fatal or return an invalid value — it falls back to the
 * documented default. Valid overrides must still be honored.
 *
 * @group hooks
 */
class HooksTest extends WP_UnitTestCase {

	/** Add $return as the filter for $tag, run $run(), then clean up. */
	private function feed( string $tag, mixed $return, callable $run ): mixed {
		$cb = static fn() => $return;
		add_filter( $tag, $cb );
		try {
			return $run();
		} finally {
			remove_filter( $tag, $cb );
		}
	}

	/** Garbage returns that must never survive a string/bool/array guard. */
	private function junk(): array {
		return [ null, 123, 3.14, [ 'x' ], new \stdClass() ];
	}

	// ---- string hooks (non-empty) -----------------------------------------

	public function test_nonempty_string_hooks_fall_back_to_default(): void {
		$cases = [
			[ 'bc_events/events/post_type/rewrite_slug', [ Hooks::class, 'hook_filter_events_rewrite_slug' ], 'events' ],
			[ 'bc_events/events/display/date_format', [ Hooks::class, 'hook_filter_date_display_format' ], 'F j, Y' ],
			[ 'bc_events/events/display/time_format', [ Hooks::class, 'hook_filter_time_display_format' ], 'g:ia' ],
			[ 'bc_events/locations/post_type/rewrite_slug', [ Hooks::class, 'hook_filter_locations_rewrite_slug' ], 'event-locations' ],
			[ 'bc_events/series/post_type/rewrite_slug', [ Hooks::class, 'hook_filter_event_series_rewrite_slug' ], 'event-series' ],
			[ 'bc_events/query_filters/taxonomy_query_field', [ Hooks::class, 'hook_filter_taxonomy_query_field' ], 'slug' ],
		];

		foreach ( $cases as [ $tag, $hook, $default ] ) {
			foreach ( array_merge( $this->junk(), [ '' ] ) as $bad ) {
				$val = $this->feed( $tag, $bad, $hook );
				$this->assertIsString( $val, "$tag must return a string" );
				$this->assertSame( $default, $val, "$tag must fall back to '$default'" );
			}
		}
	}

	public function test_nonempty_string_hooks_honor_valid_overrides(): void {
		$val = $this->feed(
			'bc_events/events/post_type/rewrite_slug',
			'happenings',
			[ Hooks::class, 'hook_filter_events_rewrite_slug' ]
		);
		$this->assertSame( 'happenings', $val );
	}

	// ---- string hooks that may legitimately be empty ----------------------

	public function test_empty_allowed_string_hooks_reject_nonstrings_but_keep_empty(): void {
		$cases = [
			'bc_events/events/display/date_time_sep_format'          => [ Hooks::class, 'hook_filter_date_time_sep_format' ],
			'bc_events/events/display/recurring_description_helper_text' => [ Hooks::class, 'hook_filter_recurring_description_helper_text' ],
			'bc_events/events/ics/default_organizer'                 => [ Hooks::class, 'hook_filter_ics_default_organizer' ],
		];

		foreach ( $cases as $tag => $hook ) {
			foreach ( $this->junk() as $bad ) {
				$this->assertIsString( $this->feed( $tag, $bad, $hook ), "$tag must return a string" );
			}
			// An empty string is a valid, intentional value here.
			$this->assertSame( '', $this->feed( $tag, '', $hook ), "$tag should keep an empty string" );
		}
	}

	// ---- string hooks that take arguments ---------------------------------

	public function test_view_title_hooks_fall_back_to_computed_default(): void {
		$date = new \DateTime( '2026-09-07 09:00:00', wp_timezone() );

		$cases = [
			[ 'bc_events/events/display/day_view_title', [ Hooks::class, 'hook_filter_day_view_title' ], $date->format( 'l, F jS' ) ],
			[ 'bc_events/events/display/week_of_view_title', [ Hooks::class, 'hook_filter_week_of_view_title' ], 'Week of ' . $date->format( 'F jS, Y' ) ],
			[ 'bc_events/events/display/month_of_view_title', [ Hooks::class, 'hook_filter_month_of_view_title' ], $date->format( 'F Y' ) ],
		];

		foreach ( $cases as [ $tag, $hook, $default ] ) {
			foreach ( $this->junk() as $bad ) {
				$val = $this->feed( $tag, $bad, static fn() => $hook( $date ) );
				$this->assertSame( $default, $val, "$tag must fall back to the computed title" );
			}
		}
	}

	public function test_ics_event_title_falls_back_to_passed_title(): void {
		$val = $this->feed(
			'bc_events/events/ics/event_title',
			[ 'not', 'a', 'string' ],
			static fn() => Hooks::hook_filter_ics_event_title( 'My Event', 42 )
		);
		$this->assertSame( 'My Event', $val );
	}

	// ---- bool hooks --------------------------------------------------------

	public function test_bool_hooks_fall_back_to_default(): void {
		$cases = [
			[ 'bc_events/locations/post_type/use_event_locations', [ Hooks::class, 'hook_filter_use_event_locations' ], true ],
			[ 'bc_events/locations/post_type/set_locations_public', [ Hooks::class, 'hook_filter_set_locations_public' ], true ],
			[ 'bc_events/series/post_type/use_event_series', [ Hooks::class, 'hook_filter_use_event_series' ], true ],
			[ 'bc_events/series/post_type/set_series_public', [ Hooks::class, 'hook_filter_set_series_public' ], false ],
			[ 'bc_events/events/display/use_event_recurring_description', [ Hooks::class, 'hook_filter_use_event_recurring_description' ], true ],
			[ 'bc_events/events/schema/include_schema', [ Hooks::class, 'hook_filter_include_schema' ], true ],
		];

		foreach ( $cases as [ $tag, $hook, $default ] ) {
			// Non-bool returns (including the classic "false"/1/0 footguns) fall back.
			foreach ( [ 'yes', 'false', 1, 0, null, [ 'x' ] ] as $bad ) {
				$val = $this->feed( $tag, $bad, $hook );
				$this->assertIsBool( $val, "$tag must return a bool" );
				$this->assertSame( $default, $val, "$tag must fall back to its default" );
			}
		}
	}

	public function test_bool_hooks_honor_real_boolean_overrides(): void {
		$this->assertFalse(
			$this->feed( 'bc_events/locations/post_type/use_event_locations', false, [ Hooks::class, 'hook_filter_use_event_locations' ] )
		);
		$this->assertTrue(
			$this->feed( 'bc_events/series/post_type/set_series_public', true, [ Hooks::class, 'hook_filter_set_series_public' ] )
		);
	}

	// ---- array hooks -------------------------------------------------------

	public function test_array_hooks_fall_back_to_empty_default(): void {
		$cases = [
			[ 'bc_events/events/save/exclude_copy_meta_keys', [ Hooks::class, 'hook_filter_exclude_meta_keys' ] ],
			[ 'bc_events/query_filters/taxonomies', [ Hooks::class, 'hook_filter_filter_taxonomies' ] ],
			[ 'bc_events/query_filters/apply_taxonomy_term_pages', [ Hooks::class, 'hook_apply_taxonomy_term_pages' ] ],
		];

		foreach ( $cases as [ $tag, $hook ] ) {
			foreach ( [ 'notarray', 123, null, new \stdClass() ] as $bad ) {
				$val = $this->feed( $tag, $bad, $hook );
				$this->assertIsArray( $val, "$tag must return an array" );
				$this->assertSame( [], $val, "$tag must fall back to []" );
			}
		}
	}

	public function test_array_hooks_honor_valid_overrides(): void {
		$val = $this->feed(
			'bc_events/events/save/exclude_copy_meta_keys',
			[ 'my_key', 'other_key' ],
			[ Hooks::class, 'hook_filter_exclude_meta_keys' ]
		);
		$this->assertSame( [ 'my_key', 'other_key' ], $val );
	}

	public function test_supports_hooks_fall_back_to_their_default_array(): void {
		foreach ( [
			'bc_events/locations/post_type/set_locations_supports' => [ Hooks::class, 'hook_filter_locations_supports' ],
			'bc_events/series/post_type/supports'                  => [ Hooks::class, 'hook_filter_series_supports' ],
		] as $tag => $hook ) {
			$val = $this->feed( $tag, 'not-an-array', $hook );
			$this->assertIsArray( $val );
			$this->assertContains( 'title', $val, "$tag default supports should include 'title'" );
		}
	}

	public function test_passthrough_array_hooks_fall_back_to_passed_value(): void {
		$val = $this->feed(
			'bc_events/events/save/finalized_exclude_cloned_meta_keys',
			'garbage',
			static fn() => Hooks::hook_filter_finalized_exclude_cloned_meta_keys( [ '_edit_lock' ] )
		);
		$this->assertSame( [ '_edit_lock' ], $val );
	}

	// ---- enforcement logic -------------------------------------------------

	public function test_archive_settings_is_fully_guarded(): void {
		// Non-array filter -> every default restored.
		$a = $this->feed( 'bc_events/events/settings/archive', 'nope', [ Hooks::class, 'hook_filter_archive_settings' ] );
		$this->assertIsArray( $a );
		$this->assertSame( 'list', $a['layout'] );
		$this->assertSame( 12, $a['per_page'] );
		$this->assertSame( 'is-past', $a['past_parameter'] );
		$this->assertSame( 'starting-on', $a['starting_on_parameter'] );
		$this->assertFalse( $a['dedupe_main_query'] );

		// Invalid layout / per_page get clamped.
		$b = $this->feed( 'bc_events/events/settings/archive', [ 'layout' => 'month', 'per_page' => 'abc' ], [ Hooks::class, 'hook_filter_archive_settings' ] );
		$this->assertSame( 'list', $b['layout'], "layout must clamp to an allowed value" );
		$this->assertSame( 12, $b['per_page'], "non-numeric per_page must reset" );

		$c = $this->feed( 'bc_events/events/settings/archive', [ 'per_page' => -5 ], [ Hooks::class, 'hook_filter_archive_settings' ] );
		$this->assertSame( 12, $c['per_page'], "non-positive per_page must reset" );

		// Valid overrides respected.
		$d = $this->feed( 'bc_events/events/settings/archive', [ 'layout' => 'week', 'per_page' => 25, 'dedupe_main_query' => true ], [ Hooks::class, 'hook_filter_archive_settings' ] );
		$this->assertSame( 'week', $d['layout'] );
		$this->assertSame( 25, $d['per_page'] );
		$this->assertTrue( $d['dedupe_main_query'] );
	}

	public function test_frequency_options_drops_unknown_keys_and_guards_empty(): void {
		$default_keys = [ 'daily', 'weekly', 'monthly', 'consecutive' ];

		// Non-array -> full allowed set.
		$this->assertSame(
			$default_keys,
			array_keys( $this->feed( 'bc_events/events/settings/frequency_options', 'x', [ Hooks::class, 'hook_filter_frequency_options' ] ) )
		);

		// Unknown keys dropped; known kept.
		$opts = $this->feed(
			'bc_events/events/settings/frequency_options',
			[ 'daily' => 'Daily', 'bogus' => 'Nope' ],
			[ Hooks::class, 'hook_filter_frequency_options' ]
		);
		$this->assertSame( [ 'daily' ], array_keys( $opts ) );

		// Empty -> full allowed set restored.
		$this->assertSame(
			$default_keys,
			array_keys( $this->feed( 'bc_events/events/settings/frequency_options', [], [ Hooks::class, 'hook_filter_frequency_options' ] ) )
		);
	}

	public function test_taxonomy_query_field_clamps_to_allowed(): void {
		$this->assertSame(
			'slug',
			$this->feed( 'bc_events/query_filters/taxonomy_query_field', 'garbage', [ Hooks::class, 'hook_filter_taxonomy_query_field' ] )
		);
		$this->assertSame(
			'term_id',
			$this->feed( 'bc_events/query_filters/taxonomy_query_field', 'term_id', [ Hooks::class, 'hook_filter_taxonomy_query_field' ] )
		);
	}
}
