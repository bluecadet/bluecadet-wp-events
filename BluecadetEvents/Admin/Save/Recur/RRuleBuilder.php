<?php

namespace BluecadetEvents\Admin\Save\Recur;
use BluecadetEvents\Admin\Utils\Logger;


class RRuleBuilder {

  /**
   * Hard upper bound on the number of generated occurrences.
   *
   * Safety net: an open-ended or misconfigured rule (no `until`/`count`) must
   * never iterate unbounded, which would risk a request timeout / OOM. Any
   * path that can't resolve an explicit end falls back to this cap.
   */
  const MAX_OCCURRENCES = 1000;

  private $args;
  private $timezone;


  /**
   * Construct
   *
   * @param array $frequency_args
   * 
   * $args should include:
   * - frequency : string (daily, weekly, monthly, yearly)
   * - weekly_days : array (for weekly frequency: monday, tuesday, wednesday, thursday, friday, saturday, sunday)
   * - month_schedule : string (for monthly frequency: first, second, third, fourth, last, every_other, date)
   * - month_day : string (for monthly frequency where month_schedule is not date: monday, tuesday, wednesday, thursday, friday, saturday, sunday)
   * - month_date : string (for monthly frequency with where month_schedule = date)
   * - end_type : string (on_date or after_x)
   * - end_date : string (date string for end date if end_type is on_date)
   * - after_x : int (if end_type is after_x: number of occurrences)
   * - start_date_timestamp : string (date string for start date of the recurring event)
   * - end_date_timestamp : string (date string for end date of the recurring event)
   * 
   */
  public function __construct(array $frequency_args) {
    $this->args = $frequency_args;
    $this->timezone = \wp_timezone();
  }


  /**
   * Get recurring dates from RRule
   *
   */
  public function get_recurring_date_period() {
    $rrule_conditional_args = [];
    $begin                  = new \DateTime('now', $this->timezone);

    if ( !is_numeric($this->args['start_date_timestamp']) ) {
      Logger::log('RRuleBuilder: missing or non-numeric start_date_timestamp; cannot build recurrence.');
      return [];
    }

    $begin->setTimestamp((int) $this->args['start_date_timestamp']);

    switch ($this->args['frequency']) {
      case 'weekly':
        $by_day_str = '';

        if ( !empty($this->args['weekly_days']) && is_array($this->args['weekly_days']) ) {
          $sep = '';
          foreach ($this->args['weekly_days'] as $day_str) {
            $by_day_str .= $sep . $this->full_day_to_rrule($day_str);
            $sep         = ',';
          }
        } else {
          $by_day_str = 'SU';
        }

        $rrule_conditional_args = [
          'freq'    => 'weekly',
          'byday'   => $by_day_str,
          'dtstart' => $begin,
        ];

        break;

      case 'monthly':
        $args = [
          'freq'    => 'monthly',
          'dtstart' => $begin,
        ];

        $schedule_args = $this->monthly_schedule_to_rrule();
        $rrule_conditional_args = array_merge($args, $schedule_args);

        break;

      case 'consecutive':
        // Back-to-back sessions: each one starts an event-length (plus the
        // buffer) after the last, so the interval is the event's own duration.
        // An empty end timestamp would TypeError on setTimestamp(), and a
        // zero-length event resolves to INTERVAL 0, which RRULE rejects with an
        // uncaught exception — a white screen on save. Bail on both.
        if ( !is_numeric($this->args['end_date_timestamp']) ) {
          Logger::log('RRuleBuilder: consecutive recurrence needs a numeric end_date_timestamp; cannot build recurrence.');
          return [];
        }

        $end = new \DateTime('now', $this->timezone);
        $end->setTimestamp((int) $this->args['end_date_timestamp']);
        $interval = (int) round(($end->getTimestamp() - $begin->getTimestamp()) / 60);
        $interval = $interval + (int) $this->args['consecutive_buffer'];

        if ( $interval < 1 ) {
          Logger::log('RRuleBuilder: consecutive recurrence resolved to a ' . $interval . '-minute interval; give the event a duration or a buffer.');
          return [];
        }

        $rrule_conditional_args = [
          'freq'    => 'minutely',
          'dtstart' => $begin,
          'interval'  => $interval,
        ];

        break;

      // case 'yearly':
      //   $rrule_conditional_args = [
      //     'freq'    => 'yearly',
      //     'dtstart' => $begin,
      //   ];

      //   break;

      default:
        // DEFAULTS TO DAILY
        $rrule_conditional_args = [
          'freq'    => 'daily',
          'dtstart' => $begin,
        ];

        break;
    }

    $end_args   = $this->handle_recurring_ends_setting();
    $rrule_args = array_merge($rrule_conditional_args, $end_args);

    return new \RRule\RRule($rrule_args);

  }


  /**
   * Convert full day option values to an RRule day pattern
   *
   * @param string $day
   * @return string
   */
  private function full_day_to_rrule($day) : string {
    switch ($day) {
      case 'monday'   : $d = 'MO'; break;
      case 'tuesday'  : $d = 'TU'; break;
      case 'wednesday': $d = 'WE'; break;
      case 'thursday' : $d = 'TH'; break;
      case 'friday'   : $d = 'FR'; break;
      case 'saturday' : $d = 'SA'; break;
      case 'sunday'   : $d = 'SU'; break;
      default         : $d = 'SU'; break;
    }

    return $d;
  }



  /**
   * Get RRule args based on schedule option
   *
   * @return array
   */
  private function monthly_schedule_to_rrule() : array {

    switch($this->args['month_schedule']) {

      case 'first':
        $args['byday'] = '1' . $this->full_day_to_rrule($this->args['month_day']);
        break;

      case 'last':
        $args['byday'] = '-1' . $this->full_day_to_rrule($this->args['month_day']);
        break;

      case 'second':
        $args['byday'] = '2' . $this->full_day_to_rrule($this->args['month_day']);
        break;

      case 'third':
        $args['byday'] = '3' . $this->full_day_to_rrule($this->args['month_day']);
        break;

      case 'fourth':
        $args['byday'] = '4' . $this->full_day_to_rrule($this->args['month_day']);
        break;

      case 'every_other':
        $args['freq'] = 'weekly';
        $args['interval'] = 2;
        $args['byday'] = $this->full_day_to_rrule($this->args['month_day']);
        break;

      case 'date':
        $args['bymonthday'] = $this->args['on_date'];
        break;

      default:
        $args['byday'] = '1' . $this->full_day_to_rrule($this->args['month_day']);
        break;
    }

    return $args;
  }



  /**
   * Get End value for RRule based on ends option
   *
   * @return array
   */
  private function handle_recurring_ends_setting() : array {
    $args = [];

    if ( $this->args['frequency'] === 'consecutive' ) {
      $count = intval($this->args['consecutive_count']);
      $args['count'] = ( $count > 0 ) ? $count : self::MAX_OCCURRENCES;
      return $args;
    }

    if ( $this->args['end_type'] === 'on_date' ) {
      $until = \DateTime::createFromFormat('Y-m-d', $this->args['end_date'], $this->timezone);
      if ( $until ) {
        $args['until'] = $until;
        return $args;
      }
      Logger::log('RRuleBuilder: invalid end_date "' . $this->args['end_date'] . '"; applying occurrence cap.');
    } else if ( $this->args['end_type'] === 'after_x' ) {
      // "End after X occurrences" means X events in the child set. RRULE COUNT
      // includes dtstart, so COUNT == X yields exactly X rule dates; the master
      // date RecurringEventsArray seeds on top is trimmed back there.
      $count = intval($this->args['end_after_x']);
      if ( $count > 0 ) {
        $args['count'] = $count;
        return $args;
      }
      Logger::log('RRuleBuilder: invalid end_after_x "' . $this->args['end_after_x'] . '"; applying occurrence cap.');
    } else {
      Logger::log('RRuleBuilder: no valid end_type set; applying occurrence cap to prevent an unbounded rule.');
    }

    // Safety net: never return a rule without an upper bound.
    $args['count'] = self::MAX_OCCURRENCES;
    return $args;

  }
}
