<?php

namespace BluecadetEvents\Admin\Editor;
use BluecadetEvents\Plugin\Settings;

class EditorAssets {

  public function __construct() {
    add_action( 'enqueue_block_editor_assets', [$this, 'enqueue'] );
  }

  public function enqueue() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== Settings::$events_machine_name ) {
      return;
    }

    $asset_file = \trailingslashit(Settings::$plugin_dir) . 'editor/scripts/dist/index.asset.php';

    if ( ! file_exists( $asset_file ) ) {
      return;
    }

    $asset = include $asset_file;

    wp_enqueue_script(
      'bc-events-editor-plugin',
      \trailingslashit(Settings::$plugin_url) . 'editor/scripts/dist/index.js',
      $asset['dependencies'],
      $asset['version'],
      true
    );

    $css_file = \trailingslashit(Settings::$plugin_dir) . 'editor/scripts/dist/index.css';

    if ( file_exists( $css_file ) ) {
      wp_enqueue_style(
        'bc-events-editor-plugin',
        \trailingslashit(Settings::$plugin_url) . 'editor/scripts/dist/index.css',
        [],
        $asset['version']
      );
    }
  }

}
