<?php

namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;
use BluecadetEvents\Helpers\TemplateHelpers;

/**
 * TemplateHelpers::get_event_date_objects() must hand back two INDEPENDENT date objects.
 *
 * Regression guard. Both entries used to be derived from a single mutable DateTime via
 * setTimestamp(), which mutates the receiver and returns $this — so 'start' and 'end' were
 * two references to the same instance, and because 'end' is assigned second, both reported
 * the END time. Every event looked zero-length to callers, and a theme computing a duration
 * or a "start – end" range silently rendered one time twice.
 *
 * The object-identity assertion is the one that actually pins the bug: comparing only the
 * timestamps would still pass if a future refactor reintroduced a shared mutable object but
 * happened to read it before the second write.
 *
 * @group helpers
 */
class TemplateHelpersDatesTest extends TestCase {

	/** make_single_event() saves 2026-09-07 09:00 -> 11:00 in the site timezone. */
	private const START = '2026-09-07 09:00:00';
	private const END   = '2026-09-07 11:00:00';

	private function dates( int $post_id ): array {
		$dates = TemplateHelpers::getInstance()->get_event_date_objects( $post_id );

		$this->assertIsArray( $dates, 'Expected a start/end array for a fully-saved event.' );

		return $dates;
	}

	public function test_start_and_end_are_not_the_same_object(): void {
		$dates = $this->dates( $this->make_single_event() );

		$this->assertNotSame(
			$dates['start'],
			$dates['end'],
			"'start' and 'end' must be independent objects; a shared mutable DateTime makes both report the end time."
		);
	}

	public function test_start_and_end_report_their_own_timestamps(): void {
		$dates = $this->dates( $this->make_single_event() );

		$this->assertSame(
			$this->site_ts( self::START ),
			$dates['start']->getTimestamp(),
			"'start' must report the event's start, not the end."
		);
		$this->assertSame(
			$this->site_ts( self::END ),
			$dates['end']->getTimestamp(),
			"'end' must report the event's end."
		);
	}

	/** The whole point of the helper is a non-zero span for a timed event. */
	public function test_span_between_start_and_end_is_preserved(): void {
		$dates = $this->dates( $this->make_single_event() );

		$this->assertSame(
			2 * HOUR_IN_SECONDS,
			$dates['end']->getTimestamp() - $dates['start']->getTimestamp(),
			'A 09:00-11:00 event must span two hours.'
		);
	}

	/** Callers format these directly, so the site timezone has to survive. */
	public function test_objects_carry_the_site_timezone(): void {
		$dates = $this->dates( $this->make_single_event() );
		$tz    = wp_timezone()->getName();

		$this->assertSame( $tz, $dates['start']->getTimezone()->getName() );
		$this->assertSame( $tz, $dates['end']->getTimezone()->getName() );
		$this->assertSame( '09:00', $dates['start']->format( 'H:i' ) );
		$this->assertSame( '11:00', $dates['end']->format( 'H:i' ) );
	}

	public function test_returns_false_when_the_event_has_no_timestamps(): void {
		$this->assertFalse(
			TemplateHelpers::getInstance()->get_event_date_objects( $this->new_event() ),
			'An event with no saved timestamps has no date objects to return.'
		);
	}
}
