<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;
use BluecadetEvents\Templates\QuerySetters;
use BluecadetEvents\Plugin\Settings;

/**
 * Ordering direction: a past list reads most-recent-first, upcoming reads
 * soonest-first, and an explicit `order` always wins.
 *
 * @group query
 */
class QueryOrderTest extends TestCase {

	/** Dates used for every test in this class, deliberately out of order. */
	private const DATES = [ '2020-01-01', '2025-12-31', '2023-03-09', '2024-06-15' ];

	private function seed_events(): void {
		$k = $this->keys();

		foreach ( self::DATES as $date ) {
			$id = $this->new_event( [ 'post_title' => $date ] );
			$this->save_via_post( $id, [
				$k['start_date']      => $date,
				$k['start_time']      => '09:00',
				$k['start_timestamp'] => $this->site_ts( $date . ' 09:00:00' ),
				$k['end_date']        => $date,
				$k['end_time']        => '11:00',
				$k['end_timestamp']   => $this->site_ts( $date . ' 11:00:00' ),
			], $date );
		}
	}

	/** Titles (which are the dates) in the order the query returned them. */
	private function ordered_titles( array $args ): array {
		$query = new \WP_Query( array_merge( [
			'post_type'       => Settings::$events_machine_name,
			'posts_per_page'  => -1,
			// Fixed "now" so the suite doesn't drift as these dates age.
			'bc_events_timestamp' => $this->site_ts( '2030-01-01 00:00:00' ),
		], $args ) );

		return wp_list_pluck( $query->posts, 'post_title' );
	}

	public function test_a_past_query_defaults_to_most_recent_first(): void {
		$this->seed_events();

		$this->assertSame(
			[ '2025-12-31', '2024-06-15', '2023-03-09', '2020-01-01' ],
			$this->ordered_titles( [ 'bc_events_query' => 'past' ] ),
			'Past events read from closest-to-now outwards.'
		);
	}

	public function test_an_upcoming_query_defaults_to_soonest_first(): void {
		$this->seed_events();

		$this->assertSame(
			[ '2020-01-01', '2023-03-09', '2024-06-15', '2025-12-31' ],
			$this->ordered_titles( [
				'bc_events_query'     => 'upcoming',
				'bc_events_timestamp' => $this->site_ts( '2019-01-01 00:00:00' ),
			] ),
			"Upcoming defaults to soonest-first even though WP_Query's own default is DESC."
		);
	}

	public function test_an_explicit_order_still_wins(): void {
		$this->seed_events();

		$this->assertSame(
			[ '2020-01-01', '2023-03-09', '2024-06-15', '2025-12-31' ],
			$this->ordered_titles( [ 'bc_events_query' => 'past', 'order' => 'ASC' ] )
		);

		$this->assertSame(
			[ '2025-12-31', '2024-06-15', '2023-03-09', '2020-01-01' ],
			$this->ordered_titles( [ 'bc_events_query' => 'past', 'order' => 'DESC' ] )
		);
	}

	public function test_the_list_view_asks_for_the_right_direction(): void {
		$upcoming = new \WP_Query();
		( new QuerySetters( $upcoming ) )->set_query( 'list' );
		$this->assertSame( 'ASC', $upcoming->get( 'order' ) );

		$_GET['is-past'] = '1';
		$past = new \WP_Query();
		( new QuerySetters( $past ) )->set_query( 'list' );
		unset( $_GET['is-past'] );

		$this->assertSame( 'DESC', $past->get( 'order' ) );
	}

	public function test_dedupe_keeps_the_direction_and_picks_the_nearest_occurrence(): void {
		$this->seed_events();
		$master = $this->make_weekly_recurring_event(); // 2026-09-07 .. 2026-10-05

		$titles = $this->ordered_titles( [ 'bc_events_query' => 'past', 'bc_events_dedupe' => true ] );

		// The series collapses to one row, ordered by its latest occurrence
		// (2026-10-05), which is the closest of them all to the 2030 "now".
		$this->assertSame( get_post( $master )->post_title, $titles[0] );
		$this->assertSame(
			[ '2025-12-31', '2024-06-15', '2023-03-09', '2020-01-01' ],
			array_slice( $titles, 1 )
		);
	}
}
