<?php

namespace BluecadetEvents\Admin\Save;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Meta\MetaKeys;

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
  private $all_meta;
  private $event_meta = [];
  private $recurring_dates = [];
  private $omit_dates = [];
  private ?DatabaseHelpers $DB_HELPERS;

  public function __construct($post_id, $post, $update) {
    $this->saved_post_id = $post_id;
    $this->saved_post = $post;
    $this->saved_update = $update;
    $this->timezone = wp_timezone();
    $this->DB_HELPERS = DatabaseHelpers::get_instance();
    $this->keys = MetaKeys::get_keys();
  }

  public function run_all() {
    $this->compile_event_meta();
    $this->handle_recurrence();
    $this->handle_always();
  }


  /**
   * Get all bc_events meta
   *
   * @return void
   */
  private function compile_event_meta() {
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



  private function handle_recurrence() {

    if ( $this->get_meta('is_recurring') ) {

      if ( $this->get_meta('omissions') ) {
        $this->handle_omit_dates();
      }

      if ( $this->get_meta('use_frequency') ) {
        $this->handle_frequency();
      }

      if ( $this->get_meta('custom_occurrences') ) {
        $this->handle_custom_occurences();
      }

    } elseif ( $this->DB_HELPERS->is_recurring_parent($this->saved_post_id) ) {
      // TODO
      // REMOVE all existing recurring events
      // UPDATE post meta to set is_recurring to false
    }
  }


  private function handle_omit_dates() {
    error_log(print_r($this->event_meta, true));
  }



  private function handle_frequency() {
    $freq = $this->get_meta('freq');
  }

  private function handle_custom_occurences() {

  }


  private function get_meta(string $key): string|array|bool {
    // Check if using a short key
    $meta_key = isset($this->keys[$key]) ? $this->keys[$key] : $key;

    if ( isset($this->event_meta[$meta_key]) ) {
      
      if ( is_array($this->event_meta[$meta_key]) && empty($this->event_meta[$meta_key]) ) {
        return false;
      }

      if ( $this->event_meta[$meta_key] == 1 ) {
        return true;
      }

      return $this->event_meta[$meta_key];
    }

    return false;
  }







  private function handle_always() {
    // Set recurring was meta value
    update_post_meta($this->saved_post_id, 'bc_events_is_recurring_was', $this->get_meta('bc_events_recurrence'));
  }
}