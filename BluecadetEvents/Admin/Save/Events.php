<?php

namespace BluecadetEvents\Admin\Save;

/**
 * Handle recurring events
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class Events {
  private $save_post_id;
  private $save_post;
  private $update;

  public function __construct() {
    add_action( 'wp_after_insert_post', [ $this, 'handle_wp_after_insert_post' ], 99, 4 );
  }


  public function handle_wp_after_insert_post($post_id, $post, $update) {
  
    if ( $post->post_type !== \BluecadetEvents\Plugin\Settings::$events_machine_name ) {
      return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
    }

    if ( !current_user_can( 'edit_post', $post_id ) ) {
      return;
    }

    $SAVE_ACTIONS = new RecurringEventsSaveHandler($post_id, $post, $update);
    $SAVE_ACTIONS->run_all();
  }


  private function update_classic_editor_meta($post_id, $post, $update) {
    // TODO
  }


  // public function handle_rest_after_insert( \WP_Post $post ) {
  //   if ( Settings::$events_machine_name !== $post->post_type ) {
  //     return;
  //   }

  //   $SAVE_ACTIONS = new GutenbergEventsSaveHandler($post, false, null);
  //   $SAVE_ACTIONS->save_basic_meta_values();
  // }


  // private function is_classic_editor() {
  //   return isset( $_POST[ 'action' ] ) && 'editpost' !== $_POST[ 'action' ] && empty( $_REQUEST[ 'meta-box-loader' ] );
  // }
}


