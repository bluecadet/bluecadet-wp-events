<?php

namespace BluecadetEvents\Admin\Trash;
use BluecadetEvents\Admin\Utils\AbstractService;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Utils\Logger;

/**
 * Handle recurring events
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class Events extends AbstractService {

  public function boot() : void {
    add_action( 'transition_post_status', [ $this, 'handle_transition_post_status' ], 99, 3 );
    add_action( 'before_delete_post', [ $this, 'handle_before_delete_post' ], 99, 2 );
  }


  /**
   * Cascade a parent's trash/untrash to its child events.
   *
   * Only the trash lane lives here — non-trash status changes (draft/publish/
   * pending/etc.) are carried to children by the recurrence engine via the
   * clone's post_status, so handling them here would double-apply. A directly
   * trashed/untrashed child has no children of its own, so it cascades nowhere:
   * parents are the single source of truth.
   *
   * @param string   $new_status
   * @param string   $old_status
   * @param \WP_Post $post
   * @return void
   */
  public function handle_transition_post_status( string $new_status, string $old_status, \WP_Post $post ) : void {
    if ( $post->post_type !== Settings::$events_machine_name ) {
      return;
    }

    if ( $new_status === $old_status ) {
      return;
    }

    $DB_HELPERS = DatabaseHelpers::get_instance();
    $child_events = $DB_HELPERS->is_recurring_parent( $post->ID );

    if ( ! $child_events ) {
      return;
    }

    if ( 'trash' === $new_status ) {
      // Parent trashed -> trash the children.
      foreach ( $child_events as $child_event ) {
        wp_trash_post( (int) $child_event );
      }
    } elseif ( 'trash' === $old_status ) {
      // Parent untrashed -> untrash the children.
      foreach ( $child_events as $child_event ) {
        wp_untrash_post( (int) $child_event );
      }
    }

    // Any other transition is owned by the recurrence engine; do nothing.
  }



  /**
    * Handle before delete post hook
    *
    * @param integer $post_id
    * @param \WP_Post $post
    * @return void
    */
  public function handle_before_delete_post( int $post_id, \WP_Post $post ) : void {

    if ( $post->post_type !== Settings::$events_machine_name ) {
      return;
    }

    $DB_HELPERS = DatabaseHelpers::get_instance();
    $child_events = $DB_HELPERS->is_recurring_parent( $post_id );

    // Delete Event from DB
    $DB_HELPERS->delete_event( $post_id );

    if ( $child_events ) {
      // Permanently Delete Child Events when Parent Event is Permanently Deleted
      foreach ( $child_events as &$child_event ) {
        $child_id = (int) $child_event;
        $DB_HELPERS->delete_event( $child_id );
        wp_delete_post( $child_id, true );
        Logger::log( 'Child Event Deleted: ' . $child_id );
      }
    }
    
  }

}