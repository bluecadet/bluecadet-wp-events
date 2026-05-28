<?php

namespace BluecadetEvents\Admin\Editor;
use BluecadetEvents\Plugin\Settings;

class Gutenberg {

  public function __construct() {

    add_action( 'init', [$this, 'register_blocks'] );

  }


  public function register_blocks() {

    $build_dir = \trailingslashit(Settings::$plugin_dir) . 'editor/blocks/dist';

    foreach ( scandir( $build_dir ) as $result ) {
      $block_location = $build_dir . '/' . $result;

      if ( ! is_dir( $block_location ) || '.' === $result || '..' === $result ) {
        continue;
      }

      register_block_type( $block_location . '/block.json' );
    }
  }

}
