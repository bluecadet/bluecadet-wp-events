<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;

/**
 * Trashing / restoring / deleting events, single and recurring.
 *
 * Parent trash & untrash cascade to children; permanent parent deletion removes
 * every child post + custom-table row; a trash→untrash round trip must not
 * duplicate children (the historical "trash duping" regression).
 *
 * @group trash-delete
 */
class TrashDeleteTest extends TestCase {

	public function test_trashing_parent_cascades_trash_to_children(): void {
		$master   = $this->make_weekly_recurring_event();
		$children = $this->child_event_ids( $master );
		$this->assertCount( 5, $children );

		wp_trash_post( $master );

		$this->assertSame( 'trash', get_post_status( $master ) );
		foreach ( $children as $cid ) {
			$this->assertSame( 'trash', get_post_status( $cid ), 'Child should be trashed with its parent.' );
		}
	}

	public function test_untrashing_parent_restores_children_without_duplicating(): void {
		$master   = $this->make_weekly_recurring_event();
		$children = $this->child_event_ids( $master );
		sort( $children );
		$this->assertCount( 5, $children );

		wp_trash_post( $master );
		wp_untrash_post( $master );

		// Same five children, none left trashed, and crucially NO new ones.
		$after = $this->child_event_ids( $master );
		sort( $after );
		$this->assertSame( $children, $after, 'Untrash must not regenerate/duplicate children.' );

		foreach ( $after as $cid ) {
			$this->assertNotSame( 'trash', get_post_status( $cid ), 'Child should be restored.' );
		}
	}

	public function test_permanently_deleting_parent_removes_all_posts_and_rows(): void {
		$master   = $this->make_weekly_recurring_event();
		$children = $this->child_event_ids( $master );
		$this->assertCount( 5, $children );

		wp_delete_post( $master, true );

		$this->assertNull( get_post( $master ), 'Master post should be gone.' );
		$this->assertNull( $this->event_row( $master ), 'Master row should be gone.' );

		foreach ( $children as $cid ) {
			$this->assertNull( get_post( $cid ), 'Child post should be gone.' );
			$this->assertNull( $this->event_row( $cid ), 'Child row should be gone.' );
		}

		$this->assertSame( [], $this->child_event_ids( $master ) );
	}

	public function test_permanently_deleting_single_event_removes_its_row(): void {
		$id = $this->make_single_event();
		$this->assertNotNull( $this->event_row( $id ) );

		wp_delete_post( $id, true );

		$this->assertNull( get_post( $id ) );
		$this->assertNull( $this->event_row( $id ), 'Standalone event row should be removed on delete.' );
	}

	public function test_trashing_a_single_child_does_not_affect_siblings_or_parent(): void {
		$master   = $this->make_weekly_recurring_event();
		$children = $this->child_event_ids( $master );
		$victim   = $children[0];
		$siblings = array_slice( $children, 1 );

		wp_trash_post( $victim );

		$this->assertSame( 'trash', get_post_status( $victim ) );
		$this->assertSame( 'publish', get_post_status( $master ), 'Parent must be unaffected.' );
		foreach ( $siblings as $cid ) {
			$this->assertSame( 'publish', get_post_status( $cid ), 'Siblings must be unaffected.' );
		}
	}
}
