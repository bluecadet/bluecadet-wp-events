<?php

namespace BluecadetEvents\Admin\Save;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Save\Recur\Objects\RecurringEvent;
use BluecadetEvents\Admin\Save\Recur\Objects\EventPost;
use BluecadetEvents\Admin\Save\Recur\Objects\FrequencyArgs;
use BluecadetEvents\Admin\Save\Recur\EventCloneBuilder;
use BluecadetEvents\Admin\Save\Recur\Objects\RecurringEventsArray;
use BluecadetEvents\Admin\Utils\Logger;
use BluecadetEvents\Plugin\BackgroundProcesses;

/**
 * Handle recurring events
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class EventsSaveAction {

  /**
   * When true, the wp_after_insert_post save handler bails. Set by the recurrence
   * engine while it creates/updates child posts, so the generic save path never
   * writes child rows — the engine owns them via DatabaseHelpers::upsert_child().
   *
   * @var bool
   */
  public static bool $generating = false;

  private ?DatabaseHelpers $DB_HELPERS;
  private RecurringEvent $RDATE;
  private RecurringEventsArray $recurring_events_array;
  private array $meta_updates = [];
  private ?EventPost $event_post = null;
  
  private mixed $background_event_handler;

  

  public function __construct(int $post_id, int|\WP_Post $post, bool $update, null|\WP_Post $post_before) {
    $this->RDATE = new RecurringEvent($post_id, $post, $update);  
  }

  public function run() : bool|\WP_Error {
    $this->DB_HELPERS = DatabaseHelpers::get_instance();

    if ( $this->RDATE->is_recurring || (!$this->RDATE->is_recurring && $this->RDATE->is_recurring_was) ) {

      if ( !$this->RDATE->parent_update ) {
        $this->RDATE->is_parent = false;
      }
      
      $this->maybe_handle_recurring_events();
      $this->handle_always();
    }

    $this->handle_always();
    $this->run_meta_updates();

    // TODO - Handle Errors
    return true;
  }


  



  /**
   * Handle whether this is a recurring event or not and run the appropriate logic
   *
   * @return void
   */
  private function maybe_handle_recurring_events() : void {
    
    if ( $this->RDATE->is_recurring ) {

      // Setup recurring dates array for comparison and building events
      $this->compile_event_meta();
      $this->run_recurring_checks();

    } elseif ( 
      $this->RDATE->is_parent &&
      (!$this->RDATE->is_recurring && $this->RDATE->is_recurring_was) &&
      $this->RDATE->recurring_delete === 'delete' 
    ) {
      
      // This is no longer a recurring event, but it was before, so handle child events
      $this->handle_child_events_delete();

    } else {
      $this->set_meta_update('is_parent', '0');
    }
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



  /**
   * Event is recurring, do the recurring thing
   *
   * @return void
   */
  private function run_recurring_checks() : void {

    // Always set the parent
    $this->set_meta_update('is_parent', '1');

    // +===============================================================+
    //  Build arrays for RecurringDatesBuilder and `recur_strategy_was`
    // +===============================================================+

    $freq_args = new FrequencyArgs();

    $freq_args->setUseFrequency((bool) ($this->RDATE->get_meta('use_frequency') ?: false));
    $freq_args->setFrequency((string) ($this->RDATE->get_meta('freq') ?: ''));
    $freq_args->setWeeklyDays((array)  ($this->RDATE->get_meta('freq_days') ?: []));
    $freq_args->setMonthSchedule((string) ($this->RDATE->get_meta('freq_mo_schedule') ?: ''));
    $freq_args->setMonthDay((string) ($this->RDATE->get_meta('freq_mo_day') ?: ''));
    $freq_args->setMonthDate((string) ($this->RDATE->get_meta('freq_mo_date') ?: ''));
    $freq_args->setConcurrentOffset((int) ($this->RDATE->get_meta('freq_concurrent_offset') ?: 0));
    $freq_args->setConcurrentCount((int) ($this->RDATE->get_meta('freq_concurrent_count') ?: 0));
    $freq_args->setEndType((string) ($this->RDATE->get_meta('freq_end_type') ?: ''));
    $freq_args->setEndDate((string) ($this->RDATE->get_meta('freq_end_date') ?: ''));
    $freq_args->setEndAfterX((int) ($this->RDATE->get_meta('freq_end_after_x') ?: 0));
    $freq_args->setStartDateTimestamp((string) ($this->RDATE->get_meta('start_timestamp') ?: ''));
    $freq_args->setEndDateTimestamp((string) ($this->RDATE->get_meta('end_timestamp') ?: ''));
    $freq_args_array = $freq_args->to_array();

    // Setup frequency args array for RRuleBuilder and recur_strategy_was meta value
    $this->RDATE->freq_args = $freq_args_array;

    // Set recur_strategy_was for comparison on next save
    $this->RDATE->recur_strategy_was = [
      'primary_start_date'    => (string) ($this->RDATE->get_meta('start_date') ?: ''),
      'primary_start_time'    => (string) ($this->RDATE->get_meta('start_time') ?: ''),
      'primary_end_date'      => (string) ($this->RDATE->get_meta('end_date') ?: ''),
      'primary_end_time'      => (string) ($this->RDATE->get_meta('end_time') ?: ''),
      'omissions' => (array) ($this->RDATE->get_meta('omissions') ?: []),
      'occurences' => (array) ($this->RDATE->get_meta('custom_occurrences') ?: []),
      ...$freq_args_array,
    ];

    Logger::log($this->RDATE->recur_strategy_was);

    $this->set_meta_update('recur_strategy_was', $this->RDATE->recur_strategy_was);

    // +===============================================================+
    //  Update Child Event Content or Build Events
    // +===============================================================+

    if ( !$this->RDATE->is_parent ) {
      $this->handle_build_recurring_events(); 
    } else {
      $recur_was = $this->RDATE->get_meta('recur_strategy_was') ? $this->RDATE->get_meta('recur_strategy_was') : [];

      $diff = array_map('unserialize', 
        array_diff(array_map('serialize', $recur_was), array_map('serialize', $this->RDATE->recur_strategy_was))
      );

      if ( empty($diff) ) {
        $this->update_only();
      } else {
        $this->handle_build_recurring_events(); 
      }
    }

    
  }


  private function handle_build_recurring_events() {

    $this->recurring_events_array = new RecurringEventsArray($this->RDATE);
    $this->recurring_events_array->build_array();

    if (!$this->recurring_events_array->has_events()) {
      return;
    }

    $clone_builder = new EventCloneBuilder($this->RDATE);
    $clone_builder->clear_recurring();
    $clone_builder->set_parent_id_meta();

    $this->background_event_handler = BackgroundProcesses::get_event_handler();

    foreach ( $this->recurring_events_array->events_array as $event ) {
      $clone_builder->set_dates($event->start_date, $event->end_date, $event->slug);
      $clone_data = $clone_builder->get_clone();
      $this->background_event_handler->push_to_queue(clone $clone_data);
    }

    $this->background_event_handler->set_check_updates($this->RDATE->parent_post_id);
    $this->background_event_handler->save()->dispatch();    

  }



  /**
   * Only update content of child events
   * 
   * Frequency hasn't changed, so we don't need to rebuild events, just update 
   * existing ones with new content.
   *
   * @return void
   */
  private function update_only() {

    if ( !is_array($this->RDATE->is_parent) || empty($this->RDATE->is_parent) ) {
      return;
    }

    // Build clone data for updating child events
    $clone_builder = new EventCloneBuilder($this->RDATE);
    $clone_builder->clear_dates_meta();
    $clone_builder->set_parent_id_meta();

    $this->background_event_handler = BackgroundProcesses::get_event_handler();

    // Loop through child events and push updates to the background handler
    foreach ($this->RDATE->is_parent as $cid) {
      $child_id = (int)$cid;
      $clone_builder->set_child_update_meta($child_id);
      $clone_data = $clone_builder->get_clone();
      $this->background_event_handler->push_to_queue(clone $clone_data);
    }

    // Dispatch the background process to handle updates
    $this->background_event_handler->save()->dispatch();
  }

  
  
  private function handle_child_events_delete() {
    if ( !is_array($this->RDATE->is_parent) || empty($this->RDATE->is_parent) ) {
      return;
    }

    $delete_handler = BackgroundProcesses::get_event_delete_handler();

    foreach ($this->RDATE->is_parent as $cid) {
      $child_id = (int)$cid;
      $delete_handler->push_to_queue($child_id);
    }

    $delete_handler->save()->dispatch();

    $recur_keys = [
      'is_recurring',
      'is_recurring_was',
      'use_frequency',
      'freq',
      'freq_days',
      'freq_mo_schedule',
      'freq_mo_day',
      'freq_mo_date',
      'freq_concurrent_offset',
      'freq_concurrent_count',
      'freq_end_type',
      'freq_end_date',
      'freq_end_after_x',
      'custom_occurrences',
      'omissions',
      'remove_recurring',
      'recur_strategy_was',
      'is_parent',
    ];

    foreach ($recur_keys as $key) {
      delete_post_meta($this->RDATE->parent_post_id, $this->RDATE->keys[$key]);
    }
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
    $end_timestamp = get_post_meta($this->RDATE->parent_post_id, $this->RDATE->keys['end_timestamp'], true);
    $d = new \DateTime();
    $d->setTimestamp($start_timestamp);
    $this->set_meta_update($this->RDATE->keys['start_month_year'], $d->format('F Y'));

    if ( $this->RDATE->is_child ) {
      $this->set_meta_update('is_child', '1');
      // Children are written exclusively by the recurrence engine (upsert_child).
      // A manual save (e.g. editing a child's body) must not clobber the
      // recur-owned occurrence/lifecycle columns, so we skip the row write here.
      return;
    }

    $this->set_meta_update('is_child', '0');

    // Add standalone event / series master to DB. is_parent flags a series master
    // (has a recurrence rule); standalone events get 0.
    $this->event_post = new EventPost(
      modified: current_time('Y-m-d H:i:s'),
      post_id: $this->RDATE->parent_post_id,
      post_slug: $this->RDATE->parent_post->post_name,
      event_start: (int) $start_timestamp,
      event_end: (int) $end_timestamp,
      parent_ID: 0,
      is_parent: (bool) $this->RDATE->is_recurring,
    );

    $insert = $this->DB_HELPERS->upsert_event($this->event_post);

  }



  /**
   * Stage a meta value update
   *
   * @param string $key
   * @param string|array|boolean $value
   * @param boolean $override
   * @return void
   */
  private function set_meta_update(string $key, int|string|array|bool $value, bool $override = true) : void {
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

}