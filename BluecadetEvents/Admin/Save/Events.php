<?php

namespace BluecadetEvents\Admin\Save;
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
    add_action( 'wp_after_insert_post', [ $this, 'handle_wp_after_insert_post' ], 99, 4 );
  }


  public function handle_wp_after_insert_post(int $post_id, \WP_Post $post, bool $update, null|\WP_Post $post_before) : void {
  
    if ( $post->post_type !== \BluecadetEvents\Plugin\Settings::$events_machine_name ) {
      return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
    }

    if ( !current_user_can( 'edit_post', $post_id ) ) {
      return;
    }

    remove_action( 'wp_after_insert_post', [ $this, 'handle_wp_after_insert_post' ], 99 );

    $SAVE_ACTIONS = new EventsSaveAction($post_id, $post, $update, $post_before);
    $SAVE_ACTIONS->run();

    add_action( 'wp_after_insert_post', [ $this, 'handle_wp_after_insert_post' ], 99, 4 );
  }
}


