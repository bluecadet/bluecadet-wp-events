<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;

/**
 * Specific added dates (custom occurrences) and excluded dates (omissions).
 *
 * @group recurring
 * @group custom-occurrences
 */
class CustomOccurrencesTest extends TestCase {

	private function ts( string $datetime ): int {
		return ( new \DateTime( $datetime, wp_timezone() ) )->getTimestamp();
	}

	/** Base recurring payload (weekly Mondays, 5 occurrences). */
	private function weekly_payload( array $overrides = [] ): array {
		$k = $this->keys();

		return array_merge( [
			$k['start_date']      => '2026-09-07',
			$k['start_time']      => '09:00',
			$k['start_timestamp'] => $this->ts( '2026-09-07 09:00:00' ),
			$k['end_date']        => '2026-09-07',
			$k['end_time']        => '11:00',
			$k['end_timestamp']   => $this->ts( '2026-09-07 11:00:00' ),
			$k['is_recurring']    => '1',
			$k['use_frequency']   => '1',
			$k['freq']            => 'weekly',
			$k['freq_days']       => [ 'monday' ],
			$k['freq_end_type']   => 'on_date',
			$k['freq_end_date']   => '2026-10-05',
		], $overrides );
	}

	public function test_custom_occurrences_add_children_on_specified_dates(): void {
		$k = $this->keys();

		$master = $this->new_event();
		// Custom occurrences only (no frequency): three specific dates.
		$this->save_via_post( $master, $this->weekly_payload( [
			$k['use_frequency']      => '0',
			$k['custom_occurrences'] => [
				[ 'start_date' => '2026-10-20', 'customize' => false ],
				[ 'start_date' => '2026-11-03', 'customize' => false ],
				[ 'start_date' => '2026-12-01', 'customize' => false ],
			],
		] ) );

		$children = $this->child_event_ids( $master );
		$this->assertCount( 4, $children, "The master's own date plus one child per custom occurrence." );

		// The master's own date, then each custom date at its start time (09:00).
		$expected = [
			$this->ts( '2026-09-07 09:00:00' ),
			$this->ts( '2026-10-20 09:00:00' ),
			$this->ts( '2026-11-03 09:00:00' ),
			$this->ts( '2026-12-01 09:00:00' ),
		];
		sort( $expected );

		$actual = array_map(
			fn( $cid ) => (int) get_post_meta( $cid, $k['start_timestamp'], true ),
			$children
		);
		sort( $actual );

		$this->assertSame( $expected, $actual );
	}

	public function test_omissions_exclude_dates_from_the_frequency_set(): void {
		$k = $this->keys();

		$master = $this->new_event();
		// Weekly Mondays (5), omitting two of them -> 3 remain.
		$this->save_via_post( $master, $this->weekly_payload( [
			$k['omissions'] => [ '2026-09-14', '2026-09-21' ],
		] ) );

		$children = $this->child_event_ids( $master );
		$this->assertCount( 3, $children, 'Two Mondays omitted from five.' );

		$dates = array_map(
			fn( $cid ) => gmdate( 'Y-m-d', (int) get_post_meta( $cid, $k['start_timestamp'], true ) ),
			$children
		);

		$this->assertNotContains( '2026-09-14', $dates, 'Omitted date must not produce a child.' );
		$this->assertNotContains( '2026-09-21', $dates, 'Omitted date must not produce a child.' );
	}
}
