<?php

namespace BluecadetEvents\Admin\PostTypes;
use BluecadetEvents\Plugin;

/**
 * Create Custom Post Types
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class PostTypes {
  // private $settings;

  public function __construct() {
    // $this->settings = \get_option('bc_events_options');

    add_action('init', [$this, 'register_custom_post_types']);
    add_action( 'admin_menu', [$this, 'change_sidebar_menu_label'] );
  }

  /**
   * Register Custom Post Types
   * --------------------------
   *
   */
  public function register_custom_post_types() {

    Plugin\Settings::__init();

    $events_slug = Plugin\Hooks::hook_filter_events_rewrite_slug();
    $events_machine_name = Plugin\Settings::$events_machine_name;
    $template = Plugin\Hooks::hook_filter_events_gutenberg_template([
      ['bc-events/event-dates', [
        'lock' => [
          'move'   => true,  // prevents reordering
          'remove' => true,  // prevents deletion
        ]
      ]]
    ]);

    $icon = '<svg width="36" height="34" viewBox="0 0 36 34" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 11.2656V30.6454C1 31.9495 2.05049 33 3.35456 33H33.0582C34.3622 33 35.4127 31.9495 35.4127 30.6454V11.2656H1ZM11.9034 27.4215C11.9034 27.82 11.5774 28.1822 11.1427 28.1822H7.33919C6.94072 28.1822 6.57848 27.8562 6.57848 27.4215V24.7409C6.57848 24.3425 6.9045 23.9802 7.33919 23.9802H11.1789C11.5774 23.9802 11.9396 24.3063 11.9396 24.7409V27.4215H11.9034ZM11.9034 19.5971C11.9034 19.9956 11.5774 20.3578 11.1427 20.3578H7.33919C6.94072 20.3578 6.57848 20.0318 6.57848 19.5971V16.8804C6.57848 16.4819 6.9045 16.1196 7.33919 16.1196H11.1789C11.5774 16.1196 11.9396 16.4457 11.9396 16.8804V19.5971H11.9034ZM20.8507 27.4215C20.8507 27.82 20.5247 28.1822 20.09 28.1822H16.2865C15.888 28.1822 15.5258 27.8562 15.5258 27.4215V24.7409C15.5258 24.3425 15.8518 23.9802 16.2865 23.9802H20.1262C20.5247 23.9802 20.8869 24.3063 20.8869 24.7409V27.4215H20.8507ZM20.8507 19.5971C20.8507 19.9956 20.5247 20.3578 20.09 20.3578H16.2865C15.888 20.3578 15.5258 20.0318 15.5258 19.5971V16.8804C15.5258 16.4819 15.8518 16.1196 16.2865 16.1196H20.1262C20.5247 16.1196 20.8869 16.4457 20.8869 16.8804V19.5971H20.8507ZM29.8342 27.4215C29.8342 27.82 29.5082 28.1822 29.0735 28.1822H25.2338C24.8353 28.1822 24.4731 27.8562 24.4731 27.4215V24.7409C24.4731 24.3425 24.7991 23.9802 25.2338 23.9802H29.0735C29.472 23.9802 29.8342 24.3063 29.8342 24.7409V27.4215ZM29.8342 19.5971C29.8342 19.9956 29.5082 20.3578 29.0735 20.3578H25.2338C24.8353 20.3578 24.4731 20.0318 24.4731 19.5971V16.8804C24.4731 16.4819 24.7991 16.1196 25.2338 16.1196H29.0735C29.472 16.1196 29.8342 16.4457 29.8342 16.8804V19.5971Z" fill="#a7aaad"/>
        <path d="M35.4127 5.43359C35.4127 4.12953 34.3622 3.07903 33.0582 3.07903H31.3194V2.06476C31.3194 0.941823 30.4138 4.76837e-07 29.2547 4.76837e-07C28.1317 4.76837e-07 27.1899 0.905599 27.1899 2.06476V3.07903H9.22283V2.06476C9.22283 0.941823 8.31723 4.76837e-07 7.15807 4.76837e-07C5.9989 4.76837e-07 5.0933 0.905599 5.0933 2.06476V3.07903H3.35456C2.05049 3.07903 1 4.12953 1 5.43359V9.2371H35.4127V5.43359Z" fill="#a7aaad"/>
    </svg>';

    // Events
    $labels = new LabelMaker('Events', 'Event');
    $labels = $labels->labels;

    $default_args = array(
      'label'             => $labels['name'],
      'labels'            => $labels,
      'supports'          => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'slug', 'custom-fields'],
      'hierarchical'      => false,
      'public'            => true,
      'menu_position'     => 25,
      'menu_icon'         => 'data:image/svg+xml;base64,' . base64_encode($icon),
      'has_archive'       => true,
      'show_in_nav_menus' => true,
      'show_in_rest'      => true,
      'template'          => $template,
      'rewrite'           => [
        'slug' => $events_slug,
        'with_front' => false,
      ],

    );

    $args = \apply_filters('bc_events_events_post_type_settings', $default_args);

    register_post_type( $events_machine_name, $args );

    // Add `all` as slug for recurring events
    add_rewrite_rule(
      '^events/([^/]+)(?:/([0-9]+))?/([^/]+)/?$',
      'index.php?post_type=events&name=$matches[1]&all=$matches[3]',
      'top'
    );

    //You then need to add a tag to it
    add_rewrite_tag('%all%','([^&]+)');



    // Event Locations
    $use_locations = Plugin\Hooks::hook_filter_use_event_locations();

    if ( $use_locations ) {
      $loc_slug = Plugin\Hooks::hook_filter_event_locations_rewrite_slug();

      $labels = new LabelMaker('Event Locations', 'Event Location');
      $labels->labels['all_items'] = 'Event Locations';
      $labels = $labels->labels;

      $public   = Plugin\Hooks::hook_filter_set_locations_public();
      $supports = Plugin\Hooks::hook_filter_locations_supports();

      $default_args = array(
        'label'                 => $labels['name'],
        'labels'                => $labels,
        'supports'              => $supports,
        'hierarchical'          => false,
        'public'                => $public,
        'show_ui'               => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-building',
        'has_archive'           => $public,
        'show_in_nav_menus'			=> $public,
        'show_in_rest'          => true,
        'show_in_menu'          => 'edit.php?post_type=' . $events_machine_name,
        'rewrite'               => [
          'slug' => $loc_slug,
          'with_front' => false,
        ],
      );

      $args = \apply_filters('bc_events_location_post_type_settings', $default_args);

      register_post_type( Plugin\Settings::$location_machine_name, $args );
    }



    // Event Locations
    $use_contacts = Plugin\Hooks::hook_filter_use_event_contacts();

    if ( $use_contacts ) {
      $loc_slug = Plugin\Hooks::hook_filter_event_contacts_rewrite_slug();

      $labels = new LabelMaker('Event Contacts', 'Event Contact');
      $labels->labels['all_items'] = 'Event Contacts';
      $labels = $labels->labels;

      $public = Plugin\Hooks::hook_filter_set_contacts_public();
      $supports = Plugin\Hooks::hook_filter_contacts_supports();

      $default_args = array(
        'label'                 => $labels['name'],
        'labels'                => $labels,
        'supports'              => $supports,
        'hierarchical'          => false,
        'public'                => $public,
        'show_ui'               => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-building',
        'has_archive'           => $public,
        'show_in_nav_menus'			=> $public,
        'show_in_rest'          => true,
        'show_in_menu'          => 'edit.php?post_type=' . $events_machine_name,
        'rewrite'               => [
          'slug' => $loc_slug,
          'with_front' => false,
        ],
      );

      $args = \apply_filters('bc_events_contact_post_type_settings', $default_args);

      register_post_type( Plugin\Settings::$contact_machine_name, $args );
    }


    // Event Series
    $use_series = Plugin\Hooks::hook_filter_use_event_series();

    if ( $use_series ) {
      $series_slug = Plugin\Hooks::hook_filter_event_series_rewrite_slug();

      $labels = new LabelMaker('Event Series', 'Event Series');
      $labels->labels['all_items'] = 'Event Series';
      $labels = $labels->labels;

      $public = Plugin\Hooks::hook_filter_set_series_public();
      $supports = Plugin\Hooks::hook_filter_series_supports();

      $default_args = array(
        'label'                 => $labels['name'],
        'labels'                => $labels,
        'supports'              => $supports,
        'hierarchical'          => false,
        'public'                => $public,
        'show_ui'               => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-list-view',
        'has_archive'           => $public,
        'show_in_nav_menus'			=> $public,
        'show_in_rest'          => true,
        'show_in_menu'          => 'edit.php?post_type=' . $events_machine_name,
        'rewrite'               => [
          'slug' => $series_slug,
          'with_front' => false,
        ],
      );

      $args = \apply_filters('bc_events_series_post_type_settings', $default_args);

      register_post_type( Plugin\Settings::$series_machine_name, $args );
    }


  }


  public function change_sidebar_menu_label() {
    global $menu;
    global $submenu;

    $events_machine_name = Plugin\Settings::$events_machine_name;

    foreach ($submenu as $key => $value) {
      if ( is_array($value) ) {
        foreach ( $value as $subkey => $subvalue ) {
          if ( $subvalue[2] === 'post-new.php?post_type=' . 'edit.php?post_type=' . $events_machine_name) {
            $submenu[$key][$subkey][0] = 'Add New Event';
          }
        }
      }
    }
  }

}
