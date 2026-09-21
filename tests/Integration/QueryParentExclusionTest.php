<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;
use BluecadetEvents\Plugin\Settings;

/**
 * Series masters are single-page canonical anchors: they must never come back
 * from a bc_events_query listing, deduped or not.
 *
 * @group query
 */
class QueryParentExclusionTest extends TestCase {

	/** Post ids returned by a bc_events_query listing. */
	private function query_ids( array $args = [] ): array {
		$q = new \WP_Query( array_merge( [
			'post_type'            => Settings::$events_machine_name,
			'posts_per_page'       => -1,
			'fields'               => 'ids',
			'bc_events_query'      => 'range',
			'bc_events_range_start' => $this->site_ts( '2026-01-01 00:00:00' ),
			'bc_events_range_end'   => $this->site_ts( '2027-01-01 00:00:00' ),
		], $args ) );

		return array_map( 'intval', $q->posts );
	}

	public function test_master_is_excluded_but_every_child_is_listed(): void {
		$master   = $this->make_weekly_recurring_event();
		$children = $this->child_event_ids( $master );

		$ids = $this->query_ids();

		$this->assertNotContains( $master, $ids, 'The series master must not appear in a listing.' );
		$this->assertCount( 5, $children );
		foreach ( $children as $cid ) {
			$this->assertContains( $cid, $ids, 'Every occurrence must appear in a listing.' );
		}
	}

	public function test_standalone_events_are_still_listed(): void {
		$single = $this->make_single_event();
		$this->assertContains( $single, $this->query_ids() );
	}

	public function test_dedupe_collapses_a_series_to_its_earliest_child(): void {
		$master   = $this->make_weekly_recurring_event();
		$single   = $this->make_single_event();
		$children = $this->child_event_ids( $master );

		// Earliest occurrence of the series (2026-09-07).
		$starts = [];
		foreach ( $children as $cid ) {
			$starts[ $cid ] = (int) get_post_meta( $cid, $this->keys()['start_timestamp'], true );
		}
		asort( $starts );
		$earliest = array_key_first( $starts );

		$ids = $this->query_ids( [ 'bc_events_dedupe' => true ] );

		$this->assertNotContains( $master, $ids, 'Dedupe must not fall back to the master.' );
		$this->assertContains( $earliest, $ids, 'Dedupe should keep the earliest occurrence.' );
		$this->assertContains( $single, $ids, 'A standalone event is its own series.' );
		$this->assertCount( 2, $ids, 'One row per series: the recurring one plus the standalone.' );
	}
}
