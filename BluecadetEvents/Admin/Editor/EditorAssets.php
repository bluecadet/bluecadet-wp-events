<?php

namespace BluecadetEvents\Admin\Editor;
use BluecadetEvents\Admin\Utils\AbstractService;
use BluecadetEvents\Plugin\Settings;

class EditorAssets extends AbstractService {

  public function boot() : void {
    add_action( 'enqueue_block_editor_assets', [$this, 'enqueue'] );
    add_action( 'admin_enqueue_scripts', [$this, 'enqueue_classic'] );
  }

  public function enqueue() : void {
    $screen = get_current_screen();

    $event_post_types = [
      Settings::$events_machine_name,
      Settings::$locations_machine_name,
      Settings::$series_machine_name,
    ];

    if ( ! $screen || ! in_array( $screen->post_type, $event_post_types, true ) ) {
      return;
    }

    $dist_url = \trailingslashit( Settings::$plugin_url ) . 'editor/scripts/dist/';
    $dist_dir = \trailingslashit( Settings::$plugin_dir ) . 'editor/scripts/dist/';

    // Bundles to load in the block editor: the legacy script bundle (index) and the
    // always-available settings panel. Each has its own generated asset manifest.
    $bundles = [
      'bc-events-editor-plugin'  => 'eventsEditor',
      'bc-events-settings-panel' => 'eventsPluginPanel',
    ];

    foreach ( $bundles as $handle => $name ) {
      $asset_file = $dist_dir . $name . '.asset.php';

      if ( ! file_exists( $asset_file ) ) {
        continue;
      }

      $asset = include $asset_file;

      wp_enqueue_script(
        $handle,
        $dist_url . $name . '.js',
        $asset['dependencies'],
        $asset['version'],
        true
      );

      $css_file = $dist_dir . $name . '.css';

      if ( file_exists( $css_file ) ) {
        wp_enqueue_style(
          $handle,
          $dist_url . $name . '.css',
          [],
          $asset['version']
        );
      }
    }
  }

  public function enqueue_classic() : void {
    $screen = get_current_screen();

    if ( ! $screen ) {
      return;
    }

    // Only on the post edit screen, and only when NOT using the block editor.
    if ( $screen->base !== 'post' || $screen->is_block_editor() ) {
      return;
    }

    if ( !in_array( $screen->post_type, [ Settings::$events_machine_name, Settings::$series_machine_name, Settings::$locations_machine_name ] ) ) {
      return;
    }
  
    $css_file = \trailingslashit(Settings::$plugin_dir) . 'assets/classicEditor/dist/events.css';

    if ( file_exists( $css_file ) ) {
      wp_enqueue_style(
        'bc-events-classic-editor',
        \trailingslashit(Settings::$plugin_url) . 'assets/classicEditor/dist/events.css',
        [],
        filemtime( $css_file )
      );
    }

    $js_file = \trailingslashit(Settings::$plugin_dir) . 'assets/classicEditor/dist/events.js';

    if ( file_exists( $js_file ) ) {
      wp_enqueue_script(
        'bc-events-classic-editor',
        \trailingslashit(Settings::$plugin_url) . 'assets/classicEditor/dist/events.js',
        [],
        filemtime( $js_file ),
        true
      );

      // REST root + nonce so the post pickers can search posts via the API
      // (mirrors the block editor's core-data search).
      wp_localize_script(
        'bc-events-classic-editor',
        'bcEventsClassic',
        [
          'restUrl'   => \esc_url_raw( \rest_url() ),
          'namespace' => Settings::$rest_namespace,
          'nonce'     => \wp_create_nonce( 'wp_rest' ),
        ]
      );
    }
  }

}
