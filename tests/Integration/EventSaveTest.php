<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;

/**
 * A single (non-recurring) event must land the same way whether saved through
 * the Classic editor ($_POST) or Gutenberg (REST): identical meta, identical
 * bc_events custom-table row, no background work.
 *
 * This is the editor-parity walking skeleton — both drivers feed one shared
 * assertion set.
 *
 * @group save
 */
class EventSaveTest extends TestCase {

	private const START_TS = 1789840800; // fixed epoch; value is asserted verbatim
	private const END_TS   = 1789848000;

	/** The event payload, keyed by FULL meta key. */
	private function payload(): array {
		$k = $this->keys();

		return [
			$k['start_date']      => '2026-09-15',
			$k['start_time']      => '18:00',
			$k['start_timestamp'] => self::START_TS,
			$k['end_date']        => '2026-09-15',
			$k['end_time']        => '20:00',
			$k['end_timestamp']   => self::END_TS,
		];
	}

	public function test_classic_editor_save_persists_event(): void {
		$id = $this->new_event();
		$this->save_via_post( $id, $this->payload() );
		$this->assertEventPersisted( $id );
	}

	public function test_gutenberg_rest_save_persists_event(): void {
		$id = $this->new_event();
		$this->save_via_rest( $id, $this->payload() );
		$this->assertEventPersisted( $id );
	}

	/** Shared end-state assertions — must hold identically for both editors. */
	private function assertEventPersisted( int $post_id ): void {
		$k = $this->keys();

		// Meta round-tripped through the editor's save path.
		$this->assertSame( '2026-09-15', get_post_meta( $post_id, $k['start_date'], true ) );
		$this->assertSame( '18:00', get_post_meta( $post_id, $k['start_time'], true ) );
		$this->assertSame( self::START_TS, (int) get_post_meta( $post_id, $k['start_timestamp'], true ) );
		$this->assertSame( self::END_TS, (int) get_post_meta( $post_id, $k['end_timestamp'], true ) );

		// Not recurring -> the engine marks it a non-child, non-parent.
		$this->assertSame( '0', (string) get_post_meta( $post_id, $k['is_child'], true ) );

		// Custom-table row written by the recurrence engine (handle_always).
		$row = $this->event_row( $post_id );
		$this->assertNotNull( $row, 'Expected a bc_events row for the saved event.' );
		$this->assertSame( $post_id, (int) $row->post_id );
		$this->assertSame( self::START_TS, (int) $row->event_start );
		$this->assertSame( self::END_TS, (int) $row->event_end );
		$this->assertSame( 0, (int) $row->parent_ID );
		$this->assertSame( 0, (int) $row->is_parent );

		// A non-recurring save queues no background work.
		$this->assertQueuesDrained();
	}
}
