<?php

namespace BluecadetEvents\Tests\Unit;

use WP_UnitTestCase;
use BluecadetEvents\Admin\Save\Recur\RRuleBuilder;

/**
 * Pure-logic checks for RRULE generation. These hit no database (RRuleBuilder
 * only touches wp_timezone()/Logger, provided by the loaded WP environment).
 *
 * @group unit
 * @group rrule
 */
class RRuleBuilderTest extends WP_UnitTestCase {

	/** Build a full frequency-args array with sane defaults, overridden per test. */
	private function args( array $overrides ): array {
		return array_merge(
			[
				'use_frequency'        => true,
				'frequency'            => 'daily',
				'weekly_days'          => [],
				'month_schedule'       => '',
				'month_day'            => '',
				'month_date'           => '',
				'on_date'              => '',
				'end_type'             => '',
				'end_date'             => '',
				'end_after_x'          => 0,
				'consecutive_buffer'   => 0,
				'consecutive_count'    => 0,
				'start_date_timestamp' => '1789840800',
				'end_date_timestamp'   => '',
			],
			$overrides
		);
	}

	public function test_consecutive_frequency_uses_the_configured_count(): void {
		$rrule = ( new RRuleBuilder(
			$this->args(
				[
					'frequency'          => 'consecutive',
					'consecutive_count'  => 3,
					'consecutive_buffer' => 0,
					'end_date_timestamp' => (string) ( 1789840800 + 3600 ),
				]
			)
		) )->get_recurring_date_period();

		$this->assertCount( 3, $rrule );
	}

	public function test_unbounded_rule_is_capped_as_a_safety_net(): void {
		// No valid end_type -> handle_recurring_ends_setting() must fall back to
		// the MAX_OCCURRENCES cap rather than producing an unbounded rule.
		$rrule = ( new RRuleBuilder(
			$this->args( [ 'frequency' => 'daily', 'end_type' => '' ] )
		) )->get_recurring_date_period();

		$this->assertCount( RRuleBuilder::MAX_OCCURRENCES, $rrule );
	}

	public function test_non_numeric_start_timestamp_yields_no_dates(): void {
		// The guard we added bails cleanly (returns []) instead of fatal-ing.
		$result = ( new RRuleBuilder(
			$this->args( [ 'start_date_timestamp' => '' ] )
		) )->get_recurring_date_period();

		$this->assertSame( [], $result );
	}
}
