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
      // The batch was already persisted by the preceding save(). handle()
      // processes every batch and calls complete() once the queue is empty.
      return $this->handle();
    }

    return parent::dispatch();
  }
}
