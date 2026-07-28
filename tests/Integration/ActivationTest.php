<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;
use BluecadetEvents\Plugin\Settings;

/**
 * The plugin's custom table is created on activation (run once in bootstrap).
 *
 * @group activation
 */
class ActivationTest extends TestCase {

	private function table_name(): string {
		global $wpdb;
		return $wpdb->prefix . Settings::$events_table;
	}

	public function test_events_table_exists(): void {
		global $wpdb;
		$table  = $this->table_name();
		$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );

		$this->assertSame( $table, $exists, 'Expected the bc_events custom table to exist after activation.' );
	}

	public function test_events_table_has_expected_columns(): void {
		global $wpdb;
		$columns = $wpdb->get_col( "SHOW COLUMNS FROM {$this->table_name()}" );

		$expected = [
			'id',
			'modified',
			'post_id',
			'post_slug',
			'event_start',
			'event_end',
			'parent_ID',
			'is_parent',
			'date_slug',
			'update_check',
		];

		foreach ( $expected as $column ) {
			$this->assertContains( $column, $columns, "Missing expected column '{$column}'." );
		}
	}

	public function test_events_table_has_expected_indexes(): void {
		global $wpdb;
		$rows        = $wpdb->get_results( "SHOW INDEX FROM {$this->table_name()}" );
		$index_names = array_unique( wp_list_pluck( $rows, 'Key_name' ) );

		foreach ( [ 'PRIMARY', 'post_id', 'event_start', 'parent_ID', 'event_start_parent', 'parent_update' ] as $index ) {
			$this->assertContains( $index, $index_names, "Missing expected index '{$index}'." );
		}
	}
}
