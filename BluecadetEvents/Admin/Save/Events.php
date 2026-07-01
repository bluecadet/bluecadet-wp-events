<?php

namespace BluecadetEvents\Admin\Save;
use BluecadetEvents\Admin\Utils\Logger;
use BluecadetEvents\Admin\Meta\MetaKeys;

/**
 * Handle recurring events
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class Events {

  public function __construct() {
    add_action( 'save_post', [$this, 'handle_save_post'], 10, 2 );
    add_action( 'wp_after_insert_post', [ $this, 'handle_wp_after_insert_post' ], 99, 4 );
  }

  public function handle_save_post( int $post_id, \WP_Post $post ) {
    // Bail for autosaves, revisions, wrong post type, or block editor saves
    if (
        defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE
        || wp_is_post_revision( $post_id )
        || $post->post_type !== 'bc-events'
        || ! isset( $_POST['bc_meta_nonce'] )
        || ! wp_verify_nonce( $_POST['bc_meta_nonce'], 'bc_save_meta' )
    ) {
      return;
    }

    // Skip if this is a block editor save (REST API)
    if ( ! isset( $_POST['post_title'] ) ) {
      return;
    }

    // Bail if the post is being trashed or deleted.
    if ( 'trash' === $post->post_status ) {
      return;
    }

    remove_action( 'save_post', [$this, 'handle_save_post'], 10 );

    $save_keys = MetaKeys::get_event_save_keys();
    $keys = MetaKeys::get_keys();

    // Only touch the keys the rendered form declared it manages (see the
    // bc_managed_keys hidden inputs in Event.php / ChildEvent.php). This scopes
    // the save to what was actually on screen — so a child form doesn't wipe
    // parent-only fields, and fields the form never renders (e.g. virtual_*)
    // are left alone. Intersect with the known save keys as a whitelist.
    $managed_keys = array_intersect(
      array_map( 'sanitize_text_field', (array) ( $_POST['bc_managed_keys'] ?? [] ) ),
      array_keys( $save_keys )
    );

    // A managed field that's absent from $_POST means the user removed/unchecked
    // it, so it should be reset to its empty/default value (matching the meta
    // shape Gutenberg saves).
    foreach ( $managed_keys as $key ) {
      $type    = $save_keys[$key];
      $present = isset( $_POST[$key] );

      // remove_recurring is always 'delete' for now. It's stored as a string so
      // it can become an option (e.g. 'to_posts') later.
      if ( $key === $keys['remove_recurring'] ) {
        update_post_meta( $post_id, $key, 'delete' );
        continue;
      }

      // custom_occurrences: array of sanitized occurrence rows, [] when emptied.
      if ( $key === $keys['custom_occurrences'] ) {
        $rows = [];
        if ( $present ) {
          foreach ( (array) $_POST[$key] as $row ) {
            if ( ! is_array( $row ) ) {
              continue;
            }
            $rows[] = [
              'start_date' => isset( $row['start_date'] ) ? sanitize_text_field( $row['start_date'] ) : '',
              'customize'  => isset( $row['customize'] ) ? filter_var( $row['customize'], FILTER_VALIDATE_BOOLEAN ) : false,
              'start_time' => isset( $row['start_time'] ) ? sanitize_text_field( $row['start_time'] ) : '',
              'end_date'   => isset( $row['end_date'] ) ? sanitize_text_field( $row['end_date'] ) : '',
              'end_time'   => isset( $row['end_time'] ) ? sanitize_text_field( $row['end_time'] ) : '',
            ];
          }
        }
        update_post_meta( $post_id, $key, $rows );
        continue;
      }

      switch ( $type ) {
        case 'string':
          update_post_meta( $post_id, $key, $present ? sanitize_text_field( $_POST[$key] ) : '' );
          break;
        case 'boolean':
          update_post_meta( $post_id, $key, $present ? filter_var( $_POST[$key], FILTER_VALIDATE_BOOLEAN ) : false );
          break;
        case 'integer':
          update_post_meta( $post_id, $key, $present ? intval( $_POST[$key] ) : 0 );
          break;
        case 'array':
          update_post_meta( $post_id, $key, $present ? array_map( 'sanitize_text_field', (array) $_POST[$key] ) : [] );
          break;
        case 'object':
          // custom_occurrences is the only object key and is handled above.
          update_post_meta( $post_id, $key, [] );
          break;
      }
    }

    add_action( 'save_post', [$this, 'handle_save_post'], 10, 2 );


  }


  public function handle_wp_after_insert_post(int $post_id, \WP_Post $post, bool $update, null|\WP_Post $post_before) : void {

    // The recurrence engine owns child rows; skip while it is generating them.
    if ( EventsSaveAction::$generating ) {
      return;
    }

    if ( $post->post_type !== \BluecadetEvents\Plugin\Settings::$events_machine_name ) {
      return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
    }

    if ( !current_user_can( 'edit_post', $post_id ) ) {
      return;
    }

    // Never regenerate on trash, editor placeholders, or revisions. Trash/untrash
    // status cascading to children is owned by the transition_post_status handler
    // in Admin\Trash\Events, not the recurrence engine.
    if ( in_array( $post->post_status, [ 'trash', 'auto-draft', 'inherit' ], true ) ) {
      return;
    }

    // ...and skip untrash (status was 'trash' immediately before this save).
    if ( $post_before instanceof \WP_Post && 'trash' === $post_before->post_status ) {
      return;
    }

    remove_action( 'wp_after_insert_post', [ $this, 'handle_wp_after_insert_post' ], 99 );

    $SAVE_ACTIONS = new EventsSaveAction($post_id, $post, $update, $post_before);
    $SAVE_ACTIONS->run();

    add_action( 'wp_after_insert_post', [ $this, 'handle_wp_after_insert_post' ], 99, 4 );
  }
}


