<?php

namespace BluecadetEvents\Admin\Settings;

use BluecadetEvents\Admin\Utils\AbstractService;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Plugin\Uninstall;

/**
 * Intercepts the plugin's "Deactivate" link on the Plugins screen and shows a
 * modal asking whether to delete the plugin's data when it is later uninstalled
 * (behavior A: records the preference; never deletes on deactivate).
 *
 * WordPress has no hook to inject UI into the deactivate/delete flow, so this is
 * done with a small admin script scoped to plugins.php that targets only this
 * plugin's row. The server side just persists the choice.
 *
 * @package BluecadetEvents
 */
class DeactivateModal extends AbstractService {

  const AJAX_ACTION = 'bc_events_uninstall_pref';
  const NONCE       = 'bc_events_uninstall_pref';

  public function boot() : void {
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
    add_action( 'admin_footer-plugins.php', [ $this, 'render_modal' ] );
    add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'handle_ajax' ] );
  }

  private function plugin_basename() : string {
    return plugin_basename( Settings::$plugin_dir . 'bluecadet-events.php' );
  }

  /**
   * Enqueue the modal script on the Plugins screen only.
   *
   * @param string $hook
   * @return void
   */
  public function enqueue( string $hook ) : void {
    if ( 'plugins.php' !== $hook ) {
      return;
    }

    wp_enqueue_script(
      'bc-events-deactivate-modal',
      Settings::$plugin_url . 'assets/admin/deactivate-modal.js',
      [],
      Settings::$version,
      true
    );

    wp_localize_script(
      'bc-events-deactivate-modal',
      'bcEventsDeactivate',
      [
        'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
        'action'         => self::AJAX_ACTION,
        'nonce'          => wp_create_nonce( self::NONCE ),
        'pluginBasename' => $this->plugin_basename(),
      ]
    );
  }

  /**
   * Print the hidden modal markup (and its minimal styles) in the footer.
   *
   * @return void
   */
  public function render_modal() : void {
    ?>
    <div id="bc-events-deactivate-modal" class="bc-events-deactivate-modal" hidden role="dialog" aria-modal="true" aria-labelledby="bc-events-deactivate-title">
      <div class="bc-events-deactivate-modal__overlay" data-bc-choice="cancel"></div>
      <div class="bc-events-deactivate-modal__box">
        <h2 id="bc-events-deactivate-title"><?php echo esc_html__( 'Deactivate Bluecadet Events', 'bluecadet-events' ); ?></h2>
        <p><?php echo esc_html__( 'When you eventually delete this plugin, what should happen to its event data (events, locations, series, and the custom table)?', 'bluecadet-events' ); ?></p>
        <p class="bc-events-deactivate-modal__note"><?php echo esc_html__( 'Deactivating never deletes anything — this only sets your preference for a future uninstall.', 'bluecadet-events' ); ?></p>
        <div class="bc-events-deactivate-modal__actions">
          <button type="button" class="button" data-bc-choice="keep"><?php echo esc_html__( 'Keep my data', 'bluecadet-events' ); ?></button>
          <button type="button" class="button button-link-delete" data-bc-choice="delete"><?php echo esc_html__( 'Delete all data on uninstall', 'bluecadet-events' ); ?></button>
        </div>
        <button type="button" class="bc-events-deactivate-modal__cancel" data-bc-choice="cancel"><?php echo esc_html__( 'Cancel', 'bluecadet-events' ); ?></button>
      </div>
    </div>
    <style>
      .bc-events-deactivate-modal[hidden] { display: none; }
      .bc-events-deactivate-modal { position: fixed; inset: 0; z-index: 100000; display: flex; align-items: center; justify-content: center; }
      .bc-events-deactivate-modal__overlay { position: absolute; inset: 0; background: rgba(0,0,0,.5); }
      .bc-events-deactivate-modal__box { position: relative; background: #fff; border-radius: 4px; padding: 24px; max-width: 480px; width: calc(100% - 40px); box-shadow: 0 4px 24px rgba(0,0,0,.3); }
      .bc-events-deactivate-modal__box h2 { margin-top: 0; }
      .bc-events-deactivate-modal__note { color: #646970; font-size: 12px; }
      .bc-events-deactivate-modal__actions { display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap; }
      .bc-events-deactivate-modal__cancel { margin-top: 14px; background: none; border: 0; color: #2271b1; cursor: pointer; padding: 0; text-decoration: underline; }
    </style>
    <?php
  }

  /**
   * Persist the user's choice, then let the browser proceed to deactivation.
   *
   * @return void
   */
  public function handle_ajax() : void {
    check_ajax_referer( self::NONCE, 'nonce' );

    if ( ! current_user_can( 'activate_plugins' ) ) {
      wp_send_json_error( [ 'message' => 'forbidden' ], 403 );
    }

    $delete = isset( $_POST['delete'] ) && '1' === $_POST['delete'];
    Uninstall::save_preference( $delete );

    wp_send_json_success( [ Uninstall::PREF_KEY => $delete ] );
  }
}
