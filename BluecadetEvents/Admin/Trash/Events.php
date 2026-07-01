<?php

namespace BluecadetEvents\Admin\Trash;
use BluecadetEvents\Admin\Meta\MetaKeys;
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
class Events {

  public function __construct() {
    add_action( 'trashed_post', [ $this, 'handle_trashed_post' ], 99, 1 );
    add_action( 'untrashed_post', [ $this, 'handle_untrashed_post' ], 99, 1 );
    add_action( 'before_delete_post', [ $this, 'handle_before_delete_post' ], 99, 2 );
  }


  /**
   * Handle Trashed Post hook
   *
   * @param integer $post_id
   * @return void
   */
  public function handle_trashed_post( int $post_id ) : void {
    if ( \get_post_type( $post_id ) !== Settings::$events_machine_name ) {
      return;
    }

    $DB_HELPERS = DatabaseHelpers::get_instance();
    $child_events = $DB_HELPERS->is_recurring_parent( $post_id );

    if ( $child_events ) {
      // Move Child Events to Trash when Parent Event is Trashed
      foreach ( $child_events as &$child_event ) {
        $child_id = (int) $child_event;
        wp_trash_post( $child_id );
      }
    }
  }



  /**
   * Handle untrashed post hook
   *
   * @param integer $post_id
   * @return void
   */
  public function handle_untrashed_post( int $post_id ) : void {
    if ( \get_post_type( $post_id ) !== Settings::$events_machine_name ) {
      return;
    }

    $DB_HELPERS = DatabaseHelpers::get_instance();
    $child_events = $DB_HELPERS->is_recurring_parent( $post_id );

    if ( $child_events ) {
      // Move Child Events to Trash when Parent Event is Trashed
      foreach ( $child_events as &$child_event ) {
        $child_id = (int) $child_event;
        wp_untrash_post( $child_id );
      }
    }
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