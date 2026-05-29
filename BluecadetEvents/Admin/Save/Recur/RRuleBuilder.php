<?php

namespace BluecadetEvents\Admin\Save\Recur;


class RRuleBuilder {
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
    $begin                  = new \DateTime('@' . $this->args['start_date_timestamp'], $this->timezone);
    
    if ( !$begin ) {
      new \WP_Error('bc-events', 'Invalid begin date for RRule');
    }

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

      case 'yearly':
        $rrule_conditional_args = [
          'freq'    => 'yearly',
          'dtstart' => $begin,
        ];

        break;

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

    if ( $this->args['end_type'] === 'on_date' ) {
      $args['until'] = \DateTime::createFromFormat('Y-m-d', $this->args['end_date'], $this->timezone);
    } else if ( $this->args['end_type'] === 'after_x' ) {
      $args['count'] = intval($this->args['after_x']) - 1;
    } else {
      $error = new \WP_Error('bc-events', 'No End Date value set. Please select a end date or ends after value)');
    }

    return $args;

  }
}
