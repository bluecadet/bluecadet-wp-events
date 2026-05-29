<?php

namespace BluecadetEvents\Admin\Save;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Meta\MetaKeys;
use BluecadetEvents\Admin\Save\Recur\RecurringDate;
use BluecadetEvents\Admin\Save\Recur\RecurringDatesArrayBuilder;
use BluecadetEvents\Admin\Utils\Logger;

/**
 * Handle recurring events
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class EventsSaveAction {
  // private $saved_post_id;
  // private $saved_post;
  // private $saved_update;
  // private $timezone;
  // private $keys = [];
  
  private ?DatabaseHelpers $DB_HELPERS;
  // private bool|array $is_parent;

  // private array $all_meta;
  // private array $event_meta = [];
  // private array $recurring_dates = [];
  // private array $omit_dates = [];
  // private array $meta_updates = [];
  
  // private array $freq_args;
  // private array $recur_strategy_was = [];

  // private bool $is_recurring;
  // private bool $is_recurring_was;



  private RecurringDate $RDATE;

  

  public function __construct(int $post_id, int|\WP_Post $post, bool $update, null|\WP_Post $post_before) {
    $this->RDATE = new RecurringDate($post_id, $post, $update);
    // $this->saved_post_id = $post_id;
    // $this->saved_post = $post;
    // $this->saved_update = $update;
    // $this->timezone = wp_timezone();
    // $this->RDATE->keys = MetaKeys::get_keys();
    // $this->is_recurring = get_post_meta($this->saved_post_id, $this->RDATE->keys['is_recurring'], true);
    // $this->is_recurring_was = get_post_meta($this->saved_post_id, $this->RDATE->keys['is_recurring_was'], true);
  
  }

  public function run() : bool|\WP_Error {

    if ( $this->RDATE->is_recurring || (!$this->RDATE->is_recurring && $this->RDATE->is_recurring_was) ) {
      $this->DB_HELPERS = DatabaseHelpers::get_instance();
      $this->RDATE->is_parent = !$this->RDATE->parent_update ? false : $this->DB_HELPERS->is_recurring_parent($this->RDATE->parent_post_id);

      $this->compile_event_meta();
      $this->handle_is_recurring();
      $this->handle_always();
    }

    $this->handle_always();
    $this->run_meta_updates();

    // TODO - Handle Errors
    return true;
  }


  /**
   * Get all bc_events meta
   *
   * @return void
   */
  private function compile_event_meta() : void {
    $this->RDATE->all_meta = get_post_meta($this->RDATE->parent_post_id, '', true);
    $meta_ns = Settings::$events_meta_ns;

    foreach ($this->RDATE->all_meta as $key => $value) { 
      if ( str_starts_with($key, $meta_ns) ) {
        if ( is_array($value) ) {
          $value = maybe_unserialize($value[0]);
        }
        $this->RDATE->event_meta[$key] = $value;
      }
    }
  }



  private function handle_is_recurring() : void {

    // MAKE SURE ANY META ALTERATIONS TO THE CURRENT POST ARE DONE BEFORE SENDING THIS.
    // BUILDER/UPDATER WILL USE CURRENT $all_meta & $event_meta VALUES TO CLONE POST.
    
    if ( $this->RDATE->is_recurring ) {

      $this->handle_recurring_checks();

    } elseif ( $this->RDATE->is_parent && (!$this->RDATE->is_recurring && $this->RDATE->is_recurring_was) ) {

      // This is no longer a recurring event, but was before, so handle child events
      if ( $this->RDATE->get_meta('remove_recurring') && $this->RDATE->get_meta('remove_recurring' === 'to_posts') ) {
        $this->handle_child_events_to_posts();
      } else {
        $this->handle_child_events_delete();
      }

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
  private function handle_recurring_checks() : void {

    // Always set the parent
    $this->set_meta_update('is_parent', true);

    // +===============================================================+
    //  Build arrays for RecurringDatesBuilder and `recur_strategy_was`
    // +===============================================================+

    // Setup frequency args array for RRuleBuilder and recur_strategy_was meta value
    $this->RDATE->freq_args = [
      'use_frequency'         => (bool) ($this->RDATE->get_meta('use_frequency') ?: false),
      'frequency'             => (string) ($this->RDATE->get_meta('freq') ?: ''),
      'weekly_days'           => (array)  ($this->RDATE->get_meta('freq_days') ?: []),
      'month_schedule'        => (string) ($this->RDATE->get_meta('freq_mo_schedule') ?: ''),
      'month_day'             => (string) ($this->RDATE->get_meta('freq_mo_day') ?: ''),
      'month_date'            => (string) ($this->RDATE->get_meta('freq_mo_date') ?: ''),
      'end_type'              => (string) ($this->RDATE->get_meta('freq_end_type') ?: ''),
      'end_date'              => (string) ($this->RDATE->get_meta('freq_end_date') ?: ''),
      'end_after_x'           => (string) ($this->RDATE->get_meta('freq_end_after_x') ?: ''),
      'start_date_timestamp'  => (string) ($this->RDATE->get_meta('start_timestamp') ?: ''),
    ];

    // Set recur_strategy_was for comparison on next save
    $this->RDATE->recur_strategy_was = [
      'omissions' => (array) ($this->RDATE->get_meta('omissions') ?: []),
      'occurences' => (array) ($this->RDATE->get_meta('custom_occurrences') ?: []),
      ...$this->RDATE->freq_args,
    ];

    $this->set_meta_update('recur_strategy_was', $this->RDATE->recur_strategy_was);

    

    // +===============================================================+
    //  Update Child Event Content or Build Events
    // +===============================================================+

    $diff = array_map('unserialize', 
      array_diff(array_map('serialize', $this->RDATE->get_meta('recur_strategy_was')), array_map('serialize', $this->RDATE->recur_strategy_was))
    );

    // if ( empty($diff) ) {
    //   $this->handle_update_existing_events_only();
    // } else {
      $this->handle_build_recurring_events();
      
    // }

    
  }


  private function handle_build_recurring_events() {
    $builder = new RecurringDatesArrayBuilder($this->RDATE);
    $builder->run();
    
    Logger::log(['RECUR BUILDER OUTPUT', $this->RDATE->recurring_dates]);
    // $this->RDATE->recurring_dates = $builder->recurring_dates;


    /**
     * Todo:
     * Create events
     * Save update_check, date slug
     * check against existing slugs, maybe update?
     * make all posts, clear all without update_check
     * clear update_check
     * 
     */

  }



  private function handle_update_existing_events_only() {
    /**
     * 
     * 
     * 
     * TODO: HANDLE UPDATING POST CONTENT ONLY
     * 
     * 
     * 
     * 
     */
  }






  private function handle_child_events_to_posts() {
    /**
     * 
     * 
     * 
     * TODO: HANDLE POSTING CHILD EVENTS AS INDIVIDUAL POSTS
     * 
     * 
     * 
     * 
     */
  }
  
  
  private function handle_child_events_delete() {
    /**
     * 
     * 
     * 
     * TODO: HANDLE DELETING CHILD EVENTS
     * 
     * 
     * 
     * 
     */
  }



  

  /**
   * Handle stuff that should always fire
   *
   * @return void
   */
  private function handle_always() : void {

    // Set recurring was meta value
    $this->set_meta_update($this->RDATE->keys['is_recurring_was'], $this->RDATE->is_recurring);

    // Set start month year for sorting
    $start_timestamp = get_post_meta($this->RDATE->parent_post_id, $this->RDATE->keys['start_timestamp'], true);
    $d = new \DateTime();
    $d->setTimestamp($start_timestamp);
    $this->set_meta_update($this->RDATE->keys['start_month_year'], $d->format('F Y'));


    /**
     * 
     * 
     * 
     * TODO: WRITE TO EVENTS TABLE
     * 
     * 
     * 
     * 
     */

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
    $meta_key = isset($this->RDATE->keys[$key]) ? $this->RDATE->keys[$key] : $key;

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
      update_post_meta($this->RDATE->parent_post_id, $key, $value);
    }
  }


  /**
   * Log if you can
   *
   * @param mixed $data
   * @return void
   */
  // private function log(mixed $data) : void {
  //   if ( $this->allow_log ) {
  //     error_log(print_r($data, true));
  //   }
  // }
}