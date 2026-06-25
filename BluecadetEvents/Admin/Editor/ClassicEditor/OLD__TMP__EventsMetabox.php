<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor;
use BluecadetEvents\Plugin;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
// use BluecadetEvents\Admin\Editor\ClassicEditor\MetaBoxPatterns;


/**
 * Create Metabox form components for Events post type
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class EventsForm extends MetaBoxPatterns {

  private static DatabaseHelpers $db_helpers;
  private static false|array $is_recurring_child;
  private static false|array $is_recurring_parent;

  public static function create_form(\WP_Post $post) {

    parent::setup($post);
    self::$db_helpers = DatabaseHelpers::get_instance();
    self::$is_recurring_child = self::$db_helpers->is_recurring_child($post->ID);
    self::$is_recurring_parent = self::$db_helpers->is_recurring_parent($post->ID);

    if ( self::$is_recurring_child ) {
      self::child_event_form($post);
    } else {
      self::event_form();
    }

  }


  private static function child_event_form(\WP_Post $post) {
    Plugin\Settings::__init();

    ?>
    <div class="">CHILD POST</div>
    <?php

    // $use_virtual     = Plugin\Hooks::hook_filter_include_virtual_options();
    // $exclude_keys    = Plugin\Hooks::hook_filter_exclude_acf_keys();
    // $exclude_keys    = !is_array($exclude_keys) ? []: $exclude_keys;
    // $allowed_keys    = !empty($exclude_keys) ? self::get_allowed_key_data($exclude_keys, $post) : false;
    // $event_keys      = parent::$events_meta;
    // $parent_link     = get_edit_post_link(self:: $is_recurring_child[0]);

    // $tz              = \wp_timezone();
    // $start           = \bce__get_start_timestamp($post);
    // $end             = \bce__get_end_timestamp($post);
    // $start_date      = new \DateTime('now', $tz);
    // $end_date        = new \DateTime('now', $tz);

    // $start_date->setTimestamp($start);
    // $end_date->setTimestamp($end);

    // $date_format     = Plugin\Hooks::hook_filter_date_display_format();
    // $time_format     = Plugin\Hooks::hook_filter_time_display_format();
    // $sep             = Plugin\Hooks::hook_filter_date_time_sep_format();
    // $all_day         = \bce__is_all_day($post);
    // $hide_time       = \bce__hide_time($post);
    // $hide_end_time   = \bce__hide_end_time($post);

    // $virtual_mixed   = \bce__is_mixed_virtual($post);
    // $virtual_url     = \bce__get_virtual_url($post);
    // $use_location    = Plugin\Hooks::hook_filter_use_event_locations();
    // $use_series      = Plugin\Hooks::hook_filter_use_event_series();
    // $use_contacts    = Plugin\Hooks::hook_filter_use_event_contacts();

    
  }


  // private static function post_to_edit_link($post) {
  //   return '<a href="' . get_edit_post_link($post->ID) . '" target="blank">' . $post->post_title . '</a>';
  // }


  


  private static function event_form() {

    ?>
    <div class="">EVENT POST</div>
      <?php if ( self::$is_recurring_parent ) { ?>
        <p>I AM PARENT</p>
      <?php } ?>

    <?php
  }

}
