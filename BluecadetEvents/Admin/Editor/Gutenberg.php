<?php

namespace BluecadetEvents\Admin\Editor;
use BluecadetEvents\Admin\Utils\AbstractService;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Plugin\Hooks;

class Gutenberg extends AbstractService {

  public function boot() : void {

    add_action( 'init', [$this, 'register_blocks'] );
    add_filter( 'block_categories_all', [$this, 'add_block_category'], 10, 1 );
    add_filter( 'allowed_block_types_all', [$this, 'filter_allowed_block_types'], 10, 2 );

  }


  public function register_blocks() : void {

    $build_dir = \trailingslashit(Settings::$plugin_dir) . 'editor/blocks/dist';

    register_block_type( $build_dir . '/event-dates/block.json' );

    if ( Hooks::hook_filter_use_event_locations() ) {
      register_block_type( $build_dir . '/location/block.json' );
    }

    if ( Hooks::hook_filter_use_event_series() ) {
      register_block_type( $build_dir . '/series/block.json' );
    }
  }


  public function add_block_category( array $categories ) : array {
    $icon = '<svg width="36" height="34" viewBox="0 0 36 34" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 11.2656V30.6454C1 31.9495 2.05049 33 3.35456 33H33.0582C34.3622 33 35.4127 31.9495 35.4127 30.6454V11.2656H1ZM11.9034 27.4215C11.9034 27.82 11.5774 28.1822 11.1427 28.1822H7.33919C6.94072 28.1822 6.57848 27.8562 6.57848 27.4215V24.7409C6.57848 24.3425 6.9045 23.9802 7.33919 23.9802H11.1789C11.5774 23.9802 11.9396 24.3063 11.9396 24.7409V27.4215H11.9034ZM11.9034 19.5971C11.9034 19.9956 11.5774 20.3578 11.1427 20.3578H7.33919C6.94072 20.3578 6.57848 20.0318 6.57848 19.5971V16.8804C6.57848 16.4819 6.9045 16.1196 7.33919 16.1196H11.1789C11.5774 16.1196 11.9396 16.4457 11.9396 16.8804V19.5971H11.9034ZM20.8507 27.4215C20.8507 27.82 20.5247 28.1822 20.09 28.1822H16.2865C15.888 28.1822 15.5258 27.8562 15.5258 27.4215V24.7409C15.5258 24.3425 15.8518 23.9802 16.2865 23.9802H20.1262C20.5247 23.9802 20.8869 24.3063 20.8869 24.7409V27.4215H20.8507ZM20.8507 19.5971C20.8507 19.9956 20.5247 20.3578 20.09 20.3578H16.2865C15.888 20.3578 15.5258 20.0318 15.5258 19.5971V16.8804C15.5258 16.4819 15.8518 16.1196 16.2865 16.1196H20.1262C20.5247 16.1196 20.8869 16.4457 20.8869 16.8804V19.5971H20.8507ZM29.8342 27.4215C29.8342 27.82 29.5082 28.1822 29.0735 28.1822H25.2338C24.8353 28.1822 24.4731 27.8562 24.4731 27.4215V24.7409C24.4731 24.3425 24.7991 23.9802 25.2338 23.9802H29.0735C29.472 23.9802 29.8342 24.3063 29.8342 24.7409V27.4215ZM29.8342 19.5971C29.8342 19.9956 29.5082 20.3578 29.0735 20.3578H25.2338C24.8353 20.3578 24.4731 20.0318 24.4731 19.5971V16.8804C24.4731 16.4819 24.7991 16.1196 25.2338 16.1196H29.0735C29.472 16.1196 29.8342 16.4457 29.8342 16.8804V19.5971Z" fill="#a7aaad"/>
        <path d="M35.4127 5.43359C35.4127 4.12953 34.3622 3.07903 33.0582 3.07903H31.3194V2.06476C31.3194 0.941823 30.4138 4.76837e-07 29.2547 4.76837e-07C28.1317 4.76837e-07 27.1899 0.905599 27.1899 2.06476V3.07903H9.22283V2.06476C9.22283 0.941823 8.31723 4.76837e-07 7.15807 4.76837e-07C5.9989 4.76837e-07 5.0933 0.905599 5.0933 2.06476V3.07903H3.35456C2.05049 3.07903 1 4.12953 1 5.43359V9.2371H35.4127V5.43359Z" fill="#a7aaad"/>
    </svg>';

    return array_merge(
      [
        [
          'slug'  => 'bluecadetEvents',
          'title' => 'BC Events',
          'icon'  => $icon, // SVG string or dashicon name
        ],
      ],
      $categories
    );
  }



  /**
   * Restrict the plugin's meta blocks to their owning post type.
   *
   * Each block is insertable only on its post type; everywhere else (other event
   * types, pages, posts) it is removed from the inserter. All non-plugin blocks
   * are left untouched.
   *
   * Note: this filter is all-or-nothing — returning an array makes *only* those
   * blocks allowed. So we start from the full registered set and subtract, rather
   * than build a small allow-list (which would disable every core block).
   *
   * @param bool|string[]            $allowed_blocks
   * @param \WP_Block_Editor_Context $editor_context
   * @return bool|string[]
   */
  public function filter_allowed_block_types( bool|array $allowed_blocks, \WP_Block_Editor_Context $editor_context ) : bool|array {

    // No post context (e.g. site/widget editor) — nothing to scope.
    if ( empty( $editor_context->post ) ) {
      return $allowed_blocks;
    }

    $post_type = $editor_context->post->post_type;

    // Block name => the only post type it may appear in.
    $owned_by = [
      'bc-events/event-dates' => Settings::$events_machine_name,
      'bc-events/location'    => Settings::$locations_machine_name,
      'bc-events/series'      => Settings::$series_machine_name,
    ];

    // If everything is currently allowed, expand to the full registered set so we
    // can subtract from it without dropping core/third-party blocks.
    if ( ! is_array( $allowed_blocks ) ) {
      $allowed_blocks = array_keys(
        \WP_Block_Type_Registry::get_instance()->get_all_registered()
      );
    }

    $allowed_blocks = array_filter(
      $allowed_blocks,
      static function ( $name ) use ( $owned_by, $post_type ) {
        // Drop a plugin block when we're not on its owning post type.
        return ! isset( $owned_by[ $name ] ) || $owned_by[ $name ] === $post_type;
      }
    );

    return array_values( $allowed_blocks );
  }

}
