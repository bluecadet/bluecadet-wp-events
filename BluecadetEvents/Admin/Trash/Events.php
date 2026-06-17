<?php

namespace BluecadetEvents\Admin\Trash;
use BluecadetEvents\Admin\Meta\MetaKeys;
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

  /**
   * Meta keys for the event
   *
   * @var array
   */
  private array $keys = [];

  /**
   * Child events for the event
   *
   * @var array|false
   */
  private array|false $child_events = false;

  /**
   * Database Helpers
   *
   * @var DatabaseHelpers
   */
  private DatabaseHelpers $DB_HELPERS;


  public function __construct() {
    add_action( 'trashed_post', [ $this, 'handle_trashed_post' ], 99, 1 );
    add_action( 'untrashed_post', [ $this, 'handle_untrashed_post' ], 99, 1 );
    add_action( 'before_delete_post', [ $this, 'handle_before_delete_post' ], 99, 2 );
  }



  /**
   * Set common vars
   *
   * @param integer $post_id
   * @return void
   */
  private function set_vars( int $post_id ) : void {
    $this->keys = MetaKeys::get_keys();
    $this->DB_HELPERS = DatabaseHelpers::get_instance();
    $this->child_events = $this->DB_HELPERS->is_recurring_parent( $post_id );
  }



  /**
   * Handle Trashed Post hook
   *
   * @param integer $post_id
   * @return void
   */
  public function handle_trashed_post( int $post_id ) : void {
    if ( \get_post_type( $post_id ) !== 'bc_events' ) {
      return;
    }

    $this->set_vars( $post_id );

    if ( $this->child_events ) {
      // Move Child Events to Trash when Parent Event is Trashed
      foreach ( $this->child_events as &$child_event ) {
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
    if ( \get_post_type( $post_id ) !== 'bc_events' ) {
      return;
    }

    $this->set_vars( $post_id );

    if ( $this->child_events ) {
      // Move Child Events to Trash when Parent Event is Trashed
      foreach ( $this->child_events as &$child_event ) {
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
  private function handle_before_delete_post( int $post_id, \WP_Post $post ) : void {

    if ( $post->post_type !== 'bc_events' ) {
      return;
    }

    $this->set_vars( $post_id );

    // Delete Event from DB
    $this->DB_HELPERS->delete_event_row( $post_id );

    if ( $this->child_events ) {
      // Permanently Delete Child Events when Parent Event is Permanently Deleted
      foreach ( $this->child_events as &$child_event ) {
        $child_id = (int) $child_event;
        wp_delete_post( $child_id, true );
        $this->DB_HELPERS->delete_event_row( $child_id );
      }
    }
    
  }

}