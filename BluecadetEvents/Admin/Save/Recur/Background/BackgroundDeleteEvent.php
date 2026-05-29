<?php

namespace BluecadetEvents\Admin\Save\Recur\Background;

/**
 * Events and Event Locations forms
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class BackgroundDeleteEvent extends \WP_Background_Process {

  protected $action = 'delete_bc_event';
  private $deleted_ids = [];


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

    \wp_delete_post($item['id']);

    $db_helpers = \BluecadetEvents\Admin\Admin_Utils\Database_Helpers::get_instance();
    $db_helpers->delete_recurring_child($item['parent_id'], $item['id']);
    $db_helpers->delete_event($item['id']);

    $location = \get_post_meta($item['id'], 'bc_events_location', true);

    if ( $location ) {
      foreach ($location as $id) {
        $db_helpers->delete_meta_type_event($id, $item['id']);
      }
    }

    $contact = \get_post_meta($item['id'], 'bc_events_contact', true);

    if ( $contact ) {
      foreach ($contact as $id) {
        $db_helpers->delete_meta_type_event($id, $item['id']);
      }
    }

    $series = \get_post_meta($item['id'], 'bc_events_series', true);

    if ( $series ) {
      foreach ($series as $id) {
        $db_helpers->delete_meta_type_event($id, $item['id']);
      }
    }

    do_action('bc_events_after_delete_event', $item['id'], $item['parent_id']);

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
