<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;

/**
 * Recurring events: the engine turns a master event + frequency rule into a set
 * of child occurrences (each its own post + bc_events row), while the master
 * carries is_parent = 1.
 *
 * @group recurring
 */
class RecurringEventTest extends TestCase {

	/**
	 * Build a recurring event meta payload (full meta keys).
	 *
	 * Defaults to a weekly-on-Mondays rule; override per test.
	 */
	private function recurring_payload( array $overrides = [] ): array {
		$k = $this->keys();

		$start = $this->ts( '2026-09-07 09:00:00' ); // Monday
		$end   = $this->ts( '2026-09-07 11:00:00' ); // 2h duration

		$defaults = [
			$k['start_date']      => '2026-09-07',
			$k['start_time']      => '09:00',
			$k['start_timestamp'] => $start,
			$k['end_date']        => '2026-09-07',
			$k['end_time']        => '11:00',
			$k['end_timestamp']   => $end,
			$k['is_recurring']    => '1',
			$k['use_frequency']   => '1',
			$k['freq']            => 'weekly',
			$k['freq_days']       => [ 'monday' ],
			$k['freq_end_type']   => 'on_date',
			$k['freq_end_date']   => '2026-10-05', // 4 weeks later (Monday)
		];

		return array_merge( $defaults, $overrides );
	}

	/** Epoch for a Y-m-d H:i:s string interpreted in the site timezone. */
	private function ts( string $datetime ): int {
		return ( new \DateTime( $datetime, wp_timezone() ) )->getTimestamp();
	}

	/** Expected weekly occurrence start timestamps, inclusive of both ends. */
	private function expected_weekly_starts( string $first = '2026-09-07 09:00:00', int $weeks = 5 ): array {
		$d   = new \DateTime( $first, wp_timezone() );
		$out = [];
		for ( $i = 0; $i < $weeks; $i++ ) {
			$out[] = $d->getTimestamp();
			$d->modify( '+1 week' );
		}
		return $out;
	}

	public function test_weekly_on_date_creates_a_child_per_occurrence(): void {
		$master = $this->new_event();
		$this->save_via_post( $master, $this->recurring_payload() );

		// Master row: flagged as a parent, not itself a child.
		$row = $this->event_row( $master );
		$this->assertNotNull( $row );
		$this->assertSame( 1, (int) $row->is_parent, 'Master should be flagged is_parent.' );
		$this->assertSame( 0, (int) $row->parent_ID );

		// One child per Monday, 2026-09-07 .. 2026-10-05 inclusive = 5.
		$children = $this->child_event_ids( $master );
		$this->assertCount( 5, $children, 'Expected 5 weekly occurrences.' );

		// Each child is a proper occurrence of this master.
		foreach ( $children as $cid ) {
			$this->assertSame( $master, $this->db()->is_recurring_child( $cid ), 'Child should point back to master.' );
			$this->assertSame( '1', (string) get_post_meta( $cid, $this->keys()['is_child'], true ) );
			$this->assertNotEmpty( get_post_meta( $cid, $this->keys()['date_slug'], true ), 'Child should have a date_slug.' );
		}

		// Child start timestamps match the expected weekly series exactly.
		$actual_starts = array_map(
			fn( $cid ) => (int) get_post_meta( $cid, $this->keys()['start_timestamp'], true ),
			$children
		);
		sort( $actual_starts );
		$this->assertSame( $this->expected_weekly_starts(), $actual_starts );

		$this->assertQueuesDrained();
	}

	public function test_recurring_editor_parity_classic_vs_rest(): void {
		$classic = $this->new_event();
		$this->save_via_post( $classic, $this->recurring_payload() );

		$rest = $this->new_event();
		$this->save_via_rest( $rest, $this->recurring_payload() );

		$classic_starts = $this->child_start_set( $classic );
		$rest_starts    = $this->child_start_set( $rest );

		$this->assertNotEmpty( $classic_starts );
		$this->assertSame(
			$classic_starts,
			$rest_starts,
			'Classic and Gutenberg saves must produce the same recurring occurrences.'
		);
	}

	public function test_daily_on_date(): void {
		$master = $this->new_event();
		$this->save_via_post( $master, $this->recurring_payload( [
			$this->keys()['freq']          => 'daily',
			$this->keys()['freq_end_date'] => '2026-09-11', // Mon..Fri inclusive
		] ) );

		// 2026-09-07 .. 2026-09-11 inclusive = 5 days.
		$this->assertCount( 5, $this->child_event_ids( $master ) );
	}

	public function test_after_x_end_type_occurrence_count(): void {
		$master = $this->new_event();
		$this->save_via_post( $master, $this->recurring_payload( [
			$this->keys()['freq']            => 'daily',
			$this->keys()['freq_end_type']   => 'after_x',
			$this->keys()['freq_end_after_x'] => '5',
		] ) );

		// "End after 5 occurrences" should yield 5 children.
		$this->assertCount( 5, $this->child_event_ids( $master ) );
	}

	public function test_monthly_first_weekday(): void {
		$master = $this->new_event();
		$this->save_via_post( $master, $this->recurring_payload( [
			$this->keys()['freq']             => 'monthly',
			$this->keys()['freq_mo_schedule'] => 'first',
			$this->keys()['freq_mo_day']      => 'monday',
			$this->keys()['freq_end_date']    => '2026-12-31',
		] ) );

		// First Monday of Sep, Oct, Nov, Dec 2026 = 4 occurrences.
		$children = $this->child_event_ids( $master );
		$this->assertCount( 4, $children );

		// Every occurrence falls on a Monday.
		foreach ( $children as $cid ) {
			$ts = (int) get_post_meta( $cid, $this->keys()['start_timestamp'], true );
			$d  = ( new \DateTime( '@' . $ts ) )->setTimezone( wp_timezone() );
			$this->assertSame( 'Monday', $d->format( 'l' ), 'Monthly first-Monday occurrence should be a Monday.' );
		}
	}

	public function test_consecutive_uses_the_count(): void {
		$master = $this->new_event();
		$this->save_via_post( $master, $this->recurring_payload( [
			$this->keys()['freq']                   => 'consecutive',
			$this->keys()['freq_consecutive_count'] => '3',
			$this->keys()['freq_consecutive_buffer'] => '0',
		] ) );

		// Consecutive uses the count directly (no end date).
		$this->assertCount( 3, $this->child_event_ids( $master ) );
	}

	public function test_long_parent_slug_is_trimmed_but_keeps_the_date_suffix(): void {
		$k = $this->keys();

		// Master with a very long slug; children are named "<slug>--<date_slug>".
		$master = $this->new_event( [ 'post_name' => str_repeat( 'a', 210 ) ] );
		$this->save_via_post( $master, $this->recurring_payload() );

		$children = $this->child_event_ids( $master );
		$this->assertNotEmpty( $children );

		foreach ( $children as $cid ) {
			$slug      = get_post( $cid )->post_name;
			$date_slug = get_post_meta( $cid, $k['date_slug'], true );

			$this->assertLessThanOrEqual( 200, strlen( $slug ), 'Child slug must stay within 200 chars.' );
			$this->assertNotEmpty( $date_slug );
			// WP collapses the "--" in the date_slug when sanitizing the post slug,
			// so compare against the sanitized form. The point is the date suffix
			// is not truncated away by the length cap.
			$this->assertStringEndsWith( sanitize_title( $date_slug ), $slug, 'The date suffix must survive the trim.' );
		}
	}

	/** Sorted set of child start timestamps for a master. */
	private function child_start_set( int $master ): array {
		$starts = array_map(
			fn( $cid ) => (int) get_post_meta( $cid, $this->keys()['start_timestamp'], true ),
			$this->child_event_ids( $master )
		);
		sort( $starts );
		return $starts;
	}
}
