<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;
use BluecadetEvents\Admin\Save\Recur\Objects\RecurringEvent;
use BluecadetEvents\Admin\Save\Recur\Objects\RecurringEventDate;
use BluecadetEvents\Admin\Save\Recur\RRuleBuilder;

class RecurringEventsArray {

  private RecurringEvent $RDATE;
  public array $events_array = [];
  private \DateTime $parent_start;
  private \DateTime $parent_end;
  private \DateInterval $parent_date_diff;



  public function __construct(RecurringEvent $RDATE) {
    $this->RDATE = $RDATE;
    
    $this->parent_start = new \DateTime('now', $this->RDATE->timezone);
    $this->parent_start->setTimestamp($this->RDATE->event_meta[$this->RDATE->keys['start_timestamp']]);

    $this->parent_end = new \DateTime('now', $this->RDATE->timezone);
    $this->parent_end->setTimestamp($this->RDATE->event_meta[$this->RDATE->keys['end_timestamp']]);

    $this->parent_date_diff = $this->parent_start->diff($this->parent_end);
  }

  public function build_array() {
    $this->events_array = [];

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
          $this->set_date($date);
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

        $start_date = \DateTime::createFromFormat('Y-m-d', $values['start_date'], $this->RDATE->timezone);
        
        if ( !$start_date ) {
          continue;
        }

        $start_date->setTime(
          (int)$this->parent_start->format('H'),
          (int)$this->parent_start->format('i')
        );

        $end_date = null;
        $is_customized = isset($values['customize']) && $values['customize'];

        if ( $is_customized ) {
        
          if ( isset($values['start_time']) && !empty($values['start_time']) ) {
            list($hour, $minute) = explode(':', $values['start_time']);
            $start_date->setTime((int)$hour, (int)$minute);
          }

          if ( ( isset($values['end_time']) && !empty($values['end_time']) ) || ( isset($values['end_date']) && !empty($values['end_date']) ) ) {
  
            if ( isset($values['end_date']) && !empty($values['end_date']) ) {
              $end_date = \DateTime::createFromFormat('Y-m-d', $values['end_date'], $this->RDATE->timezone);
            }

            if ( !$end_date ) {
              $end_date = (clone $start_date)->add($this->parent_date_diff);
            }
            
            if ( isset($values['end_time']) && !empty($values['end_time']) ) {
              list($hour, $minute) = explode(':', $values['end_time']);
              $end_date->setTime((int)$hour, (int)$minute);
            }
            
          }
        }

        $this->set_date($start_date, $end_date);
        
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
   * Set Date in array
   *
   * @param \DateTime $start
   * @param \DateTime|null $end
   * @return void
   */
  private function set_date(\DateTime $start, \DateTime|null $end = null) : void {
    
    if ( !$end ) {
      $end = (clone $start)->add($this->parent_date_diff);
    }

    $this->events_array[] = new RecurringEventDate(
      $start,
      $end,
      $start->format('Y-m-d--H-i')
    );
  }



  public function has_events() : bool {
    return !empty($this->events_array);
  }

  
}