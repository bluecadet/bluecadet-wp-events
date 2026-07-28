<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;
use BluecadetEvents\Plugin\Settings;

/**
 * Focused checks on the custom bc_events table: rows are inserted, updated in
 * place (never duplicated), and carry the expected column values.
 *
 * @group db-table
 */
class EventsTableTest extends TestCase {

	private function table(): string {
		global $wpdb;
		return $wpdb->prefix . Settings::$events_table;
	}

	private function rows_for( int $post_id ): int {
		global $wpdb;
		return (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$this->table()} WHERE post_id = %d", $post_id )
		);
	}

	public function test_standalone_insert_writes_expected_columns(): void {
		$id = $this->make_single_event();

		$this->assertSame( 1, $this->rows_for( $id ), 'Exactly one row per event post.' );

		$row = $this->event_row( $id );
		$this->assertSame( $this->site_ts( '2026-09-07 09:00:00' ), (int) $row->event_start );
		$this->assertSame( $this->site_ts( '2026-09-07 11:00:00' ), (int) $row->event_end );
		$this->assertSame( 0, (int) $row->parent_ID );
		$this->assertSame( 0, (int) $row->is_parent );
		$this->assertSame( get_post( $id )->post_name, $row->post_slug );
	}

	public function test_resaving_standalone_updates_the_same_row_in_place(): void {
		$k  = $this->keys();
		$id = $this->make_single_event(); // 2026-09-07
		$original_row_id = (int) $this->event_row( $id )->id;

		// Re-save with a different date/time.
		$this->save_via_post( $id, [
			$k['start_date']      => '2026-10-01',
			$k['start_time']      => '14:00',
			$k['start_timestamp'] => $this->site_ts( '2026-10-01 14:00:00' ),
			$k['end_date']        => '2026-10-01',
			$k['end_time']        => '15:00',
			$k['end_timestamp']   => $this->site_ts( '2026-10-01 15:00:00' ),
		] );

		$this->assertSame( 1, $this->rows_for( $id ), 'Re-save must update in place, not insert a duplicate.' );

		$row = $this->event_row( $id );
		$this->assertSame( $original_row_id, (int) $row->id, 'Same table row is reused.' );
		$this->assertSame( $this->site_ts( '2026-10-01 14:00:00' ), (int) $row->event_start, 'event_start updated.' );
		$this->assertSame( $this->site_ts( '2026-10-01 15:00:00' ), (int) $row->event_end, 'event_end updated.' );
	}

	public function test_recurring_writes_master_row_and_one_row_per_child(): void {
		global $wpdb;
		$master = $this->make_weekly_recurring_event(); // 5 Mondays

		// Master row.
		$m = $this->event_row( $master );
		$this->assertSame( 1, (int) $m->is_parent );
		$this->assertSame( 0, (int) $m->parent_ID );

		// Child rows: exactly 5, correct columns and event_start series.
		$child_rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$this->table()} WHERE parent_ID = %d ORDER BY event_start ASC", $master )
		);
		$this->assertCount( 5, $child_rows );

		$expected = [];
		$d        = new \DateTime( '2026-09-07 09:00:00', wp_timezone() );
		for ( $i = 0; $i < 5; $i++ ) {
			$expected[] = $d->getTimestamp();
			$d->modify( '+1 week' );
		}
		$this->assertSame(
			$expected,
			array_map( fn( $r ) => (int) $r->event_start, $child_rows ),
			'Child row event_start columns match the weekly series.'
		);

		$duration = $this->site_ts( '2026-09-07 11:00:00' ) - $this->site_ts( '2026-09-07 09:00:00' );
		foreach ( $child_rows as $r ) {
			$this->assertSame( $master, (int) $r->parent_ID );
			$this->assertSame( 0, (int) $r->is_parent );
			$this->assertNotEmpty( $r->date_slug, 'Child row carries its date_slug.' );
			$this->assertSame( $duration, (int) $r->event_end - (int) $r->event_start, 'Duration preserved per child.' );
		}

		// The whole set = 1 master + 5 children.
		$total = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$this->table()} WHERE post_id = %d OR parent_ID = %d", $master, $master )
		);
		$this->assertSame( 6, $total );
	}
}
