<?php

namespace BluecadetEvents\Admin\Save\Recur\Background;

/**
 * Run a WP_Background_Process queue inline instead of via an async loopback
 * request, when the `bc_events/background/sync` filter returns true.
 *
 * Default is OFF, so production behavior is unchanged. The test suite turns it
 * on so recurring child creation/cleanup is deterministic (push -> save ->
 * task -> complete all happen synchronously). Also useful for WP-CLI or any
 * environment where loopback HTTP requests are unreliable.
 *
 * @package BluecadetEvents
 */
trait SynchronousDispatch {

  /**
   * @return mixed The loopback response (async) or handle() result (sync).
   */
  public function dispatch() {
    if ( apply_filters( 'bc_events/background/sync', false ) ) {
      // handle() finishes with wp_die() (correct for the async ajax request it
      // is designed for, but fatal when run inline). Suppress it for the
      // synchronous run. Idempotent, and only active while the sync filter is on.
      add_filter( $this->identifier . '_wp_die', '__return_false' );

      // The batch was already persisted by the preceding save(). handle()
      // processes every batch and calls complete() once the queue is empty.
      return $this->handle();
    }

    return parent::dispatch();
  }

  /**
   * Forward the current request's HTTP Basic Auth credentials to the loopback.
   * Without this, a site behind basic auth (e.g. a password-protected WP Engine
   * environment) answers the admin-ajax loopback with a 401 and the queue never
   * runs.
   *
   * @return array
   */
  protected function get_post_args() {
    $args = parent::get_post_args();

    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ( ! $auth && isset( $_SERVER['PHP_AUTH_USER'] ) ) {
      $auth = 'Basic ' . base64_encode( $_SERVER['PHP_AUTH_USER'] . ':' . ( $_SERVER['PHP_AUTH_PW'] ?? '' ) );
    }

    if ( $auth && empty( $args['headers']['Authorization'] ) ) {
      $args['headers']['Authorization'] = $auth;
    }

    return $args;
  }
}
