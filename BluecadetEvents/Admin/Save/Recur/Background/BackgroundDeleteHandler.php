<?php

namespace BluecadetEvents\Admin\Save\Recur\Background;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Utils\Logger;

/**
 * Events and Event Locations forms
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class BackgroundDeleteHandler extends \WP_Background_Process {
  protected $action = 'bc_events_recur_event_delete_handler';

  /**
   * Task
   *
   * Override this method to perform any actions required on each queue item.
   * Return the modified item for further processing, or false to remove it from the queue.
   *
   * @param mixed $post_id Queue item to iterate over
   * @return mixed
   */
  protected function task(mixed $post_id) {

    Logger::log('DELETING ITEM: ' . $post_id);

    $DB_HELPERS = DatabaseHelpers::get_instance();
    $result = wp_delete_post( $post_id, true );

    if ( $result === false ) {
      Logger::log('ERROR DELETING POST ID: ' . $post_id);
    } else {
      Logger::log('DELETED POST ID: ' . $post_id);
    }
    
    $DB_HELPERS->delete_event($post_id);

    return false;
  }

  /**
   * Complete
   *
   * Override this method to perform any actions required once processing has been completed.
   * This could be used for sending a notification email, or cleaning up any necessary data.
   */
  protected function complete() {

    Logger::log("DELETE COMPLETE");
    
  }
}