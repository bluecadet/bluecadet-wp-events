<?php

namespace BluecadetEvents\Admin\Save\Recur\Background;

/**
 * Events and Event Locations forms
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class BackgroundCreateEvent extends \WP_Background_Process {

  /**
   * @var string
   */
  protected $action = 'create_new_bc_event';

  private $post_id;


  /**
   * Task
   *
   * Override this method to perform any actions required on each
   * queue item. Return the modified item for further processing
   * in the next pass through. Or, return false to remove the
   * item from the queue.
   *
   * @param mixed $item Queue item to iterate over
   *
   * @return mixed
   */
  protected function task( $item ) {

    $new_event_id = wp_insert_post( $item['wp']  );

    Clone_Meta::copy_meta($new_event_id, $item['meta']);

    if ( isset($item['taxonomies']) ) {
      foreach ($item['taxonomies'] as $tax => $terms) {
        $terms = \wp_set_object_terms( $new_event_id, $terms, $tax );
      }
    }


    $db_helpers = \BluecadetEvents\Admin\Admin_Utils\Database_Helpers::get_instance();
    $db_helpers->write_recurring_child($item['parent_id'], $new_event_id);

    $start = isset($item['meta']['_bc_events_start_timestamp']) ? $item['meta']['_bc_events_start_timestamp'] : false;
    $end = isset($item['meta']['_bc_events_end_timestamp']) ? $item['meta']['_bc_events_end_timestamp'] : false;

    if ( $start && $end ) {
      $db_helpers->write_event($new_event_id, intval($start), intval($end), $item['wp']['post_title']);
    }

    if ( isset($item['meta']['bc_events_series']) && !empty($item['meta']['bc_events_series'])) {
      $value = \maybe_unserialize($item['meta']['bc_events_series']);
      foreach ($value as $id) {
        $db_helpers->write_meta_type_event(intval($id), 'series', $new_event_id);
      }
    }

    if ( isset($item['meta']['bc_events_location']) && !empty($item['meta']['bc_events_location']) ) {
      $value = \maybe_unserialize($item['meta']['bc_events_location']);
      foreach ($value as $id) {
        $db_helpers->write_meta_type_event(intval($id), 'location', $new_event_id);
      }
    }

    if ( isset($item['meta']['bc_events_contact']) && !empty($item['meta']['bc_events_contact']) ) {
      $value = \maybe_unserialize($item['meta']['bc_events_contact']);
      foreach ($value as $id) {
        $db_helpers->write_meta_type_event(intval($id), 'contact', $new_event_id);
      }
    }

    if ( class_exists('ACF') && isset($item['acf']) && !empty($item['acf']) ) {
      foreach ($item['acf'] as $key => $value) {
        \update_field($key, $value, $new_event_id);
      }
    }

    do_action('bc_events_after_create_event', $new_event_id, $item['parent_id']);
    do_action('bc_events_after_clone_event', $new_event_id, $item['parent_id']);

    return false;
  }

  /**
   * Complete
   *
   * Override if applicable, but ensure that the below actions are
   * performed, or, call parent::complete().
   */
  protected function complete() {

    parent::complete();

    // Show notice to user or perform some other arbitrary task...
  }

}
