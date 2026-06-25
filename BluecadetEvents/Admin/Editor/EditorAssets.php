<?php

namespace BluecadetEvents\Admin\Editor;
use BluecadetEvents\Plugin\Settings;

class EditorAssets {

  public function __construct() {
    add_action( 'enqueue_block_editor_assets', [$this, 'enqueue'] );
    add_action( 'admin_enqueue_scripts', [$this, 'enqueue_classic'] );
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

  public function enqueue_classic() {
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

    if ( ! file_exists( $css_file ) ) {
      return;
    }

    wp_enqueue_style(
      'bc-events-classic-editor',
      \trailingslashit(Settings::$plugin_url) . 'assets/classicEditor/dist/events.css',
      [],
      filemtime( $css_file )
    );
  }

}
