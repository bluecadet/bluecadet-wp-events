<?php

namespace BluecadetEvents\Admin\Save;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Meta\MetaKeys;
use BluecadetEvents\Admin\Save\Recur\RRuleBuilder;

/**
 * Handle recurring events
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class RecurringEventsSaveHandler {
  private $saved_post_id;
  private $saved_post;
  private $saved_update;
  private $timezone;
  private $keys = [];
  private array $all_meta;
  private array $event_meta = [];
  private array $recurring_dates = [];
  private array $omit_dates = [];
  private array $meta_updates = [];
  private ?DatabaseHelpers $DB_HELPERS;
  private bool $allow_log = true;
  private $freq_args;
  private $recur_strategy_was = [];

  public function __construct($post_id, $post, $update) {
    $this->saved_post_id = $post_id;
    $this->saved_post = $post;
    $this->saved_update = $update;
    $this->timezone = wp_timezone();
    $this->DB_HELPERS = DatabaseHelpers::get_instance();
    $this->keys = MetaKeys::get_keys();
  }

  public function run_all() : void {
    $this->compile_event_meta();
    $this->handle_recurrence();
    $this->handle_always();
    $this->run_meta_updates();
  }


  /**
   * Get all bc_events meta
   *
   * @return void
   */
  private function compile_event_meta() : void {
    $this->all_meta = get_post_meta($this->saved_post_id, '', true);
    $meta_ns = Settings::$events_meta_ns;

    foreach ($this->all_meta as $key => $value) { 
      if ( str_starts_with($key, $meta_ns) ) {
        if ( is_array($value) ) {
          $value = maybe_unserialize($value[0]);
        }
        $this->event_meta[$key] = $value;
      }
    }
  }



  private function handle_recurrence() : void {
    
    if ( $this->get_meta('is_recurring') ) {
      $this->handle_recurring_creation();

    } elseif ( $this->DB_HELPERS->is_recurring_parent($this->saved_post_id) ) {
      // TODO
      // REMOVE all existing recurring events
      $this->set_meta_update('is_parent', false);

    } else {
      $this->set_meta_update('is_parent', false);
    }
  }



  /**
   * Event is recurring, do the recurring thing
   *
   * @return void
   */
  private function handle_recurring_creation() : void {

    $this->set_meta_update('is_parent', true);

    // Setup frequency args array for RRuleBuilder and recur_strategy_was meta value
    $this->freq_args = [
      'use_frequency'         => (bool) ($this->get_meta('use_frequency') ?: false),
      'frequency'             => (string) ($this->get_meta('freq') ?: ''),
      'weekly_days'           => (array)  ($this->get_meta('freq_days') ?: []),
      'month_schedule'        => (string) ($this->get_meta('freq_mo_schedule') ?: ''),
      'month_day'             => (string) ($this->get_meta('freq_mo_day') ?: ''),
      'month_date'            => (string) ($this->get_meta('freq_mo_date') ?: ''),
      'end_type'              => (string) ($this->get_meta('freq_end_type') ?: ''),
      'end_date'              => (string) ($this->get_meta('freq_end_date') ?: ''),
      'end_after_x'           => (string) ($this->get_meta('freq_end_after_x') ?: ''),
      'start_date_timestamp'  => (string) ($this->get_meta('start_timestamp') ?: ''),
    ];

    // Set recur_strategy_was for comparison on next save
    $this->recur_strategy_was = [
      'omissions' => (array) ($this->get_meta('omissions') ?: []),
      'occurences' => (array) ($this->get_meta('custom_occurrences') ?: []),
      ...$this->freq_args,
    ];

    $this->set_meta_update('recur_strategy_was', $this->recur_strategy_was);

    // Set is_parent to true
    $this->set_meta_update('is_parent', true);
  
    // Handle frequency-based recurrence
    if ( $this->get_meta('use_frequency') ) {
      $this->handle_frequency();
    }

    // Handle custom occurences
    if ( $this->get_meta('custom_occurrences') ) {
      $this->handle_custom_occurences();
    }
  }



  private function handle_frequency() : void {
    $this->recur_strategy_was = array_merge($this->recur_strategy_was, $this->freq_args);
    
    $rrule_dates = new RRuleBuilder($this->freq_args);

    if ( $rrule_dates ) {
      $date_period = $rrule_dates->get_recurring_date_period();

      foreach ($date_period as $date) {
        $date_ymd = $date->format('Y-m-d');

        if ( !$this->check_against_omission_array($date_ymd) ) {
          $this->recurring_dates[] = [
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
    
    if ( is_array($this->get_meta('custom_occurrences')) ) {
      foreach ($this->get_meta('custom_occurrences') as $key => $values) {

        if ( !isset($values['start_date']) || empty($values['start_date']) ) {
          continue;
        }

        if ( $this->check_against_omission_array($values['start_date']) ) {
          continue;
        }

        $sd = \DateTime::createFromFormat('Y-m-d', $values['start_date'], $this->timezone);
        
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

        $custom = [];

        if ( $is_customized && isset($values['end_date']) && !empty($values['end_date']) ) {
          $ed = \DateTime::createFromFormat('Y-m-d', $values['end_date'], $this->timezone);

          if ( $ed ) {
            $custom['end_date'] = $ed;

            if ( isset($values['end_time']) && !empty($values['end_time']) ) {
              list($hour, $minute) = explode(':', $values['end_time']);
              $ed->setTime((int)$hour, (int)$minute);
            }

            $custom['end_date'] = $ed;
          }
        }

        if ( !empty($custom) ) {
          $date_object['end_date'] = $custom;
        }

        $this->recurring_dates[] = $date_object;
        
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
    $omit_dates = $this->get_meta('omissions');
    if ( is_array($omit_dates) && in_array($date_str, $omit_dates) ) {
      return true;
    }
    return false;
  }






  /**
   * Get meta value by MetaKeys key name
   *
   * @param string $key
   * @return string|array|boolean
   */
  private function get_meta(string $key): string|array|bool {
    // Check if using a short key
    $meta_key = isset($this->keys[$key]) ? $this->keys[$key] : $key;


    if ( isset($this->event_meta[$meta_key]) ) {
      // No Value or empty  
      if ( is_array($this->event_meta[$meta_key]) && empty($this->event_meta[$meta_key]) ) {
        return false;
      }

      // Boolean
      if ( $this->event_meta[$meta_key] == 1 ) {
        return true;
      }

      // String or array value
      return $this->event_meta[$meta_key];
    }

    return false;
  }




  

  /**
   * Handle stuff that should always fire
   *
   * @return void
   */
  private function handle_always() : void {
    // Set recurring was meta value
    $this->set_meta_update($this->keys['is_recurring_was'], $this->get_meta('is_recurring'));

    // Set start month year for sorting
    $start_timestamp = $this->get_meta('start_timestamp');
    $d = new \DateTime();
    $d->setTimestamp($start_timestamp);
    $this->set_meta_update($this->keys['start_month_year'], $d->format('F Y'));

    

  }



  



  /**
   * Stage a meta value update
   *
   * @param string $key
   * @param string|array|boolean $value
   * @param boolean $override
   * @return void
   */
  private function set_meta_update(string $key, string|array|bool $value, bool $override = true) : void {
    $meta_key = isset($this->keys[$key]) ? $this->keys[$key] : $key;

    if ( !$override && isset($this->meta_updates[$meta_key]) ) {
      return;
    }

    $this->meta_updates[$meta_key] = $value;
  }


  /**
   * Update all values from set_meta_update
   *
   * @return void
   */
  private function run_meta_updates() : void {
    foreach ($this->meta_updates as $key => $value) {
      update_post_meta($this->saved_post_id, $key, $value);
    }
  }


  /**
   * Log if you can
   *
   * @param mixed $data
   * @return void
   */
  private function log(mixed $data) : void {
    if ( $this->allow_log ) {
      error_log(print_r($data, true));
    }
  }
}