<?php

namespace BluecadetEvents\Admin\Save\Recur\Background;
use BluecadetEvents\Admin\Save\Recur\Background\UpdateOrCreateEvent;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Utils\Logger;

/**
 * Events and Event Locations forms
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class BackgroundEventHandler extends \WP_Background_Process {
  protected $action = 'bc_events_recur_event_handler';


  public function set_check_updates(int $parent_id) {
    // Set a transient to indicate that we need to check for updates after processing
    set_transient( $this->action . '_parent_id', $parent_id );
  }


  /**
   * Task
   *
   * Override this method to perform any actions required on each queue item.
   * Return the modified item for further processing, or false to remove it from the queue.
   *
   * @param mixed $item Queue item to iterate over
   * @return mixed
   */
  protected function task($item) {
    
    new UpdateOrCreateEvent($item);

    return false;
  }

  /**
   * Complete
   *
   * Override this method to perform any actions required once processing has been completed.
   * This could be used for sending a notification email, or cleaning up any necessary data.
   */
  protected function complete() {

    $parent_id = get_transient( $this->action . '_parent_id' );
    delete_transient( $this->action . '_parent_id' );

    // After all events have been processed, check for any updates that were not handled and clean up
    if ( $parent_id ) {
      $DB_HELPERS = DatabaseHelpers::get_instance();

      $unused_checks = $DB_HELPERS->check_unused_update_checks($parent_id);

      if ( is_array($unused_checks) && !empty($unused_checks) ) {
        foreach ($unused_checks as $check) {
          $post_id = (int)$check->child_ID;
          $DB_HELPERS->delete_recurring_event($post_id);
          \wp_delete_post( $post_id, true );
        }
      }

      $DB_HELPERS = DatabaseHelpers::get_instance();
      $DB_HELPERS->clear_recurring_update_checks($parent_id);
    }
  }
}