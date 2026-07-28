<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;

/**
 * Editing a recurring event: unchanged config updates children in place;
 * changed config regenerates and removes orphaned children; child_deny_override
 * protects a manually-edited child from being overwritten.
 *
 * @group recurring
 * @group recurrence-edit
 */
class RecurrenceEditTest extends TestCase {

	private function ts( string $datetime ): int {
		return ( new \DateTime( $datetime, wp_timezone() ) )->getTimestamp();
	}

	/** Weekly-Mondays payload; default end 2026-10-05 = 5 occurrences. */
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

	/** [ Y-m-d => child_id ] for a master's children. */
	private function children_by_date( int $master ): array {
		$map = [];
		foreach ( $this->child_event_ids( $master ) as $cid ) {
			$ts           = (int) get_post_meta( $cid, $this->keys()['start_timestamp'], true );
			$map[ gmdate( 'Y-m-d', $ts ) ] = $cid;
		}
		ksort( $map );
		return $map;
	}

	public function test_shortening_the_range_regenerates_and_deletes_orphans(): void {
		$master = $this->new_event();
		$this->save_via_post( $master, $this->weekly_payload() ); // 5 Mondays

		$before = $this->children_by_date( $master );
		$this->assertCount( 5, $before );

		// Shorten to 3 Mondays (end 2026-09-21).
		$this->save_via_post( $master, $this->weekly_payload( [
			$this->keys()['freq_end_date'] => '2026-09-21',
		] ) );

		$after = $this->children_by_date( $master );
		$this->assertCount( 3, $after, 'Two occurrences should be dropped.' );
		$this->assertSame(
			[ '2026-09-07', '2026-09-14', '2026-09-21' ],
			array_keys( $after ),
			'Only the in-range Mondays should remain.'
		);

		// Dropped occurrences: their posts AND custom-table rows are cleaned up.
		foreach ( [ '2026-09-28', '2026-10-05' ] as $date ) {
			$orphan = $before[ $date ];
			$this->assertNull( get_post( $orphan ), "Orphaned child {$date} post should be deleted." );
			$this->assertNull( $this->event_row( $orphan ), "Orphaned child {$date} row should be deleted." );
		}

		$this->assertQueuesDrained();
	}

	public function test_resaving_identical_config_updates_children_in_place(): void {
		$master = $this->new_event();
		$this->save_via_post( $master, $this->weekly_payload(), 'Original Title' );

		$before = $this->children_by_date( $master );
		$this->assertCount( 5, $before );

		// Same recurrence config, new title -> update-only path.
		$this->save_via_post( $master, $this->weekly_payload(), 'Updated Title' );

		$after = $this->children_by_date( $master );
		$this->assertSame(
			array_values( $before ),
			array_values( $after ),
			'Identical config must reuse the same child posts (no regeneration).'
		);

		foreach ( $after as $cid ) {
			$this->assertSame( 'Updated Title', get_post( $cid )->post_title, 'Child title should follow the parent.' );
		}
	}

	public function test_child_deny_override_preserves_manually_edited_content(): void {
		$k = $this->keys();

		$master = $this->new_event();
		$this->save_via_post( $master, $this->weekly_payload(), 'Parent', 'Parent content v1' );

		$children = $this->children_by_date( $master );
		$locked   = $children['2026-09-14'];
		$normal   = $children['2026-09-21'];

		// Manually customise one child and lock it against parent overrides.
		wp_update_post( [ 'ID' => $locked, 'post_content' => 'HANDCRAFTED CHILD COPY' ] );
		update_post_meta( $locked, $k['child_deny_override'], '1' );

		// Re-save the parent with new content (same recurrence config -> update-only).
		$this->save_via_post( $master, $this->weekly_payload(), 'Parent', 'Parent content v2' );

		$this->assertSame(
			'HANDCRAFTED CHILD COPY',
			get_post( $locked )->post_content,
			'A child with child_deny_override must keep its own content.'
		);
		$this->assertSame(
			'Parent content v2',
			get_post( $normal )->post_content,
			'An unlocked child should receive the updated parent content.'
		);
	}
}
