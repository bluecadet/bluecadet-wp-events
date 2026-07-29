<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;
use BluecadetEvents\Plugin\Uninstall;
use BluecadetEvents\Plugin\Activate;
use BluecadetEvents\Plugin\Settings;

/**
 * Uninstall preference resolution and the data-clearing routine.
 *
 * @group uninstall
 */
class UninstallTest extends TestCase {

	public function tear_down(): void {
		// The WP test harness rewrites CREATE/DROP TABLE to their TEMPORARY forms.
		// Disable that so we can guarantee a REAL bc_events table exists again for
		// subsequent tests (the drop test removes the real one).
		remove_filter( 'query', [ $this, '_create_temporary_tables' ] );
		remove_filter( 'query', [ $this, '_drop_temporary_tables' ] );
		Activate::activate();

		delete_option( Uninstall::OPTION );
		remove_all_filters( 'bc_events/uninstall/delete_data' );
		parent::tear_down();
	}

	/** Let real DDL through (harness otherwise rewrites DROP -> DROP TEMPORARY). */
	private function allow_real_ddl(): void {
		remove_filter( 'query', [ $this, '_create_temporary_tables' ] );
		remove_filter( 'query', [ $this, '_drop_temporary_tables' ] );
	}

	private function events_table(): string {
		global $wpdb;
		return $wpdb->prefix . Settings::$events_table;
	}

	private function table_exists(): bool {
		global $wpdb;
		$t = $this->events_table();
		return $t === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $t ) );
	}

	// ---- preference resolution --------------------------------------------

	public function test_default_is_keep_data(): void {
		delete_option( Uninstall::OPTION );
		$this->assertFalse( Uninstall::should_delete_data() );
	}

	public function test_saved_preference_drives_the_decision(): void {
		Uninstall::save_preference( true );
		$this->assertTrue( Uninstall::should_delete_data() );

		Uninstall::save_preference( false );
		$this->assertFalse( Uninstall::should_delete_data() );
	}

	public function test_filter_overrides_saved_preference_both_ways(): void {
		Uninstall::save_preference( false );
		add_filter( 'bc_events/uninstall/delete_data', '__return_true' );
		$this->assertTrue( Uninstall::should_delete_data() );
		remove_all_filters( 'bc_events/uninstall/delete_data' );

		Uninstall::save_preference( true );
		add_filter( 'bc_events/uninstall/delete_data', '__return_false' );
		$this->assertFalse( Uninstall::should_delete_data() );
	}

	public function test_bad_filter_return_cannot_trigger_deletion(): void {
		Uninstall::save_preference( false );
		add_filter( 'bc_events/uninstall/delete_data', static fn() => 'garbage' );
		$this->assertFalse(
			Uninstall::should_delete_data(),
			'A non-bool filter return must fall back to the saved preference, never flip it on.'
		);
	}

	// ---- the clearing routine ---------------------------------------------

	public function test_delete_all_data_removes_posts_table_and_option(): void {
		$master = $this->make_weekly_recurring_event(); // master + 5 children
		$single = $this->make_single_event();
		Uninstall::save_preference( true );

		$this->assertNotEmpty( $this->child_event_ids( $master ) );
		$this->assertTrue( $this->table_exists() );

		$this->allow_real_ddl(); // so the DROP actually hits the real table
		Uninstall::delete_all_data();

		$this->assertFalse( $this->table_exists(), 'Custom table should be dropped.' );
		$this->assertNull( get_post( $master ), 'Master post should be deleted.' );
		$this->assertNull( get_post( $single ), 'Standalone event should be deleted.' );
		$this->assertFalse( get_option( Uninstall::OPTION ), 'Plugin option should be removed.' );
	}

	public function test_run_keeps_everything_when_preference_is_off(): void {
		$master = $this->make_weekly_recurring_event();
		Uninstall::save_preference( false );

		Uninstall::run();

		$this->assertTrue( $this->table_exists(), 'Table must survive when keeping data.' );
		$this->assertNotNull( get_post( $master ), 'Posts must survive when keeping data.' );
	}
}
