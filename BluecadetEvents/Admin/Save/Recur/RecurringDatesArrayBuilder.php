<?php

namespace BluecadetEvents\Admin\Save\Recur;
use BluecadetEvents\Admin\Save\Recur\RRuleBuilder;
use BluecadetEvents\Admin\Utils\Logger;


/**
 * Creates all new events
 * 
 */
class RecurringDatesArrayBuilder {
  private RecurringDate $RDATE;

  public function __construct(RecurringDate $RDATE) {
    $this->RDATE = $RDATE;
  }

  public function run() : void {

    $this->RDATE->recurring_dates = [];

    // Handle frequency-based recurrence
    if ( $this->RDATE->get_meta('use_frequency') ) {
      $this->handle_frequency();
    }

    // Handle custom occurences
    if ( $this->RDATE->get_meta('custom_occurrences') ) {
      $this->handle_custom_occurences();
    }
  }


  /**
   * Build RRule Dates from frequency settings
   *
   * @return void
   */
  private function handle_frequency() : void {
    
    $rrule_dates = new RRuleBuilder($this->RDATE->freq_args);

    if ( $rrule_dates ) {
      $date_period = $rrule_dates->get_recurring_date_period();

      foreach ($date_period as $date) {
        $date_ymd = $date->format('Y-m-d');

        if ( !$this->check_against_omission_array($date_ymd) ) {
          $this->RDATE->recurring_dates[] = [
            'date' => $date,
            'custom' => false,
          ];
        }
      }
    }

  }



  /**
   * Add Custom Occurence dates to recurring dates array
   *
   * @return void
   */
  private function handle_custom_occurences() : void {
    
    if ( is_array($this->RDATE->get_meta('custom_occurrences')) ) {
      foreach ($this->RDATE->get_meta('custom_occurrences') as $key => $values) {

        if ( !isset($values['start_date']) || empty($values['start_date']) ) {
          continue;
        }

        if ( $this->check_against_omission_array($values['start_date']) ) {
          continue;
        }

        $sd = \DateTime::createFromFormat('Y-m-d', $values['start_date'], $this->RDATE->timezone);
        
        if ( !$sd ) {
          continue;
        }

        $is_customized = isset($values['customize']) && $values['customize'];

        if ( $is_customized && isset($values['start_time']) && !empty($values['start_time']) ) {
          list($hour, $minute) = explode(':', $values['start_time']);
          $sd->setTime((int)$hour, (int)$minute);
        }

        $date_object = [
          'date' => $sd,
          'custom' => true,
        ];

        if ( $is_customized && isset($values['end_date']) && !empty($values['end_date']) ) {
          $ed = \DateTime::createFromFormat('Y-m-d', $values['end_date'], $this->RDATE->timezone);

          if ( $ed ) {

            if ( isset($values['end_time']) && !empty($values['end_time']) ) {
              list($hour, $minute) = explode(':', $values['end_time']);
              $ed->setTime((int)$hour, (int)$minute);
            }

            $date_object['end_date'] = $ed;
          }
        }

        $this->RDATE->recurring_dates[] = $date_object;
        
      }
    }
  }



  /**
   * Check to see if Y-m-d date string is in omission array
   *
   * @param string $date_str
   * @return boolean
   */
  private function check_against_omission_array(string $date_str) : bool {
    $omit_dates = $this->RDATE->get_meta('omissions');
    if ( is_array($omit_dates) && in_array($date_str, $omit_dates) ) {
      return true;
    }
    return false;
  }


  /**
   * Create clone, add to Background Creator
   */
  // private function build_events() {

  //   Logger::log('BUILD_EVENTS');

  //   $this->clone = new DataCloner($this->parent_post_id, $this->parent_post, $this->all_meta);
  //   $this->clone->clear_recurring();
  //   $this->clone->set_parent_id();

  //   $parent_start = new \DateTime('now', $this->timezone);
  //   $parent_start->setTimestamp($this->event_meta[$this->keys['start_timestamp']]);
    
  //   $parent_end = new \DateTime('now', $this->timezone);
  //   $parent_end->setTimestamp($this->event_meta[$this->keys['end_timestamp']]);

  //   $parent_date_diff = $parent_start->diff($parent_end);

  //   foreach ($this->recurring_dates as $date) {

  //     if ( isset($date['end_date']) && $date['end_date'] instanceof \DateTime ) {
  //       $end_date = $date['end_date'];
  //     } else {
  //       $end_date = clone $parent_start;
  //       $end_date->add($parent_date_diff);
  //     }

  //     $this->clone->set_dates($date['date'], $end_date);
  //     // BackgroundCreateEvent::dispatch($this->clone->clone, $this->parent_post_id);
  //   }


  //   Logger::log([
  //     'CLONE' => $this->clone,
  //     'RECUR DATES' => $this->recurring_dates,
  //   ]);

  // }




}