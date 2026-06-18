<?php

namespace BluecadetEvents\Admin\Editor;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Plugin\Hooks;

class Gutenberg {

  public function __construct() {

    add_action( 'init', [$this, 'register_blocks'] );

  }


  public function register_blocks() {

    $build_dir = \trailingslashit(Settings::$plugin_dir) . 'editor/blocks/dist';

    register_block_type( $build_dir . '/event-dates/block.json' );

    if ( Hooks::hook_filter_use_event_locations() ) {
      register_block_type( $build_dir . '/location/block.json' );
    }

    if ( Hooks::hook_filter_use_event_series() ) {
      register_block_type( $build_dir . '/series/block.json' );
    }
  }

}
