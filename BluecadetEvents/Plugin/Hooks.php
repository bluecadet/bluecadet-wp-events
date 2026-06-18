<?php

namespace BluecadetEvents\Plugin;

class Hooks {

  private static $_instance = null;


  private function  __construct() {
    // Locked
  }

  private function  __clone() {
    // Locked
  }

  public static function get_instance() {
    if ( self::$_instance == null ) {
      self::$_instance = new Hooks();
    }

    return self::$_instance;
  }

  // ==============================
  //          Event Hooks
  // ==============================

  /**
   * hook_filter_events_rewrite_slug
   *
   * @hook 'bc_events_rewrite_slug'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_events_rewrite_slug() {
    return \apply_filters('bc_events_rewrite_slug', 'events');
  }


  /**
   * hook_filter_events_gutenberg_template
   *
   * @hook 'bc_events_gutenberg_template'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_events_gutenberg_template($template) {
    return \apply_filters('bc_events_gutenberg_template', $template);
  }



  /**
   * hook_filter_exclude_meta_keys
   *
   * @hook 'bc_events_exclude_copy_meta_keys'
   * @hookType filter
   * @return array
   */
  public static function hook_filter_exclude_meta_keys() {
    return \apply_filters('bc_events_exclude_copy_meta_keys', []);
  }



  // +=========================================================================+
  // MAYBE DELETE?
  // +=========================================================================+


  /**
   * hook_filter_exclude_acf_keys
   *
   * @hook 'bc_events_exclude_copy_acf_keys'
   * @hookType filter
   * @return array
   */
  public static function hook_filter_exclude_acf_keys() {
    return \apply_filters('bc_events_exclude_copy_acf_keys', []);
  }


  /**
   * hook_filter_exclude_acf_keys_if_empty
   *
   * @hook 'bc_events_exclude_copy_acf_keys_if_empty'
   * @hookType filter
   * @return array
   */
  // public static function hook_filter_exclude_acf_keys_if_empty() {
  //   return \apply_filters('bc_events_exclude_copy_acf_keys_if_empty', []);
  // }


  


  /**
   * hook_filter_date_display_format
   *
   * @hook 'bc_events_date_display_format'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_date_display_format() {
    return \apply_filters('bc_events_date_display_format', 'F j, Y');
  }


  /**
   * hook_filter_time_display_format
   *
   * @hook 'bc_events_time_display_format'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_time_display_format() {
    return \apply_filters('bc_events_time_display_format', 'g:ia');
  }


  /**
   * hook_filter_date_time_sep_format
   *
   * @hook 'bc_events_front_end_date_time_sep_format'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_date_time_sep_format() {
    return \apply_filters('bc_events_front_end_date_time_sep_format', ' | ');
  }



  /**
   * hook_filter_day_view_title
   *
   * @hook 'bc_events_day_view_title'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_day_view_title($date) {
    $title = $date->format('l, F jS');
    return \apply_filters('bc_events_day_view_title', $title, $date);
  }



  /**
   * hook_filter_week_of_view_title
   *
   * @hook 'bc_events_week_of_view_title'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_week_of_view_title($date) {
    $title = 'Week of ' . $date->format('F jS, Y');
    return \apply_filters('bc_events_week_of_view_title', $title, $date);
  }



  /**
   * hook_filter_month_of_view_title
   *
   * @hook 'bc_events_month_of_view_title'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_month_of_view_title($date) {
    $title = $date->format('F Y');
    return \apply_filters('bc_events_month_of_view_title', $title, $date);
  }



  /**
   * hook_filter_recurring_child_slug
   *
   * @hook 'bc_events_recurring_child_slug'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_recurring_child_slug($slug, $start_date, $end_date) {
    return \apply_filters('bc_events_recurring_child_slug', $slug, $start_date, $end_date);
  }

  /**
   * hook_filter_include_all_day_option
   *
   * @hook 'bc_events_include_all_day_option'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_include_all_day_option() {
    return \apply_filters('bc_events_include_all_day_option', true);
  }



  /**
   * hook_filter_use_event_recurring_description
   *
   * @hook 'bc_events_use_event_recurring_description'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_use_event_recurring_description() {
    return \apply_filters('bc_events_use_event_recurring_description', true);
  }

  /**
   * hook_filter_recurring_description_text
   *
   * @hook 'bc_events_recurring_description_text'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_recurring_description_text() {
    return \apply_filters('bc_events_recurring_description_text', "i.e. 'Daily' or 'Every Monday'");
  }


  /**
   * hook_filter_include_virtual_options
   *
   * @hook 'bc_events_include_virtual_options'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_include_virtual_options() {
    return \apply_filters('bc_events_include_virtual_options', true);
  }


  /**
   * hook_filter_include_virtual_options
   *
   * @hook 'bc_events_include_virtual_options'
   * @hookType filter
   * @return array
   */
  public static function hook_filter_event_schema($schema, $post_id, $post) {
    return \apply_filters('bc_events_event_schema', $schema, $post_id, $post);
  }


  /**
   * hook_filter_include_schema
   *
   * @hook 'bc_events_include_schema'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_include_schema() {
    return \apply_filters('bc_events_include_schema', true);
  }


  /**
   * hook_filter_ics_event_title
   *
   * @hook 'bc_events_ics_event_title'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_ics_event_title($title, $post_id) {
    return \apply_filters('bc_events_ics_event_title', $title, $post_id);
  }


  /**
   * hook_filter_ics_default_organizer
   *
   * @hook 'bc_events_ics_default_organizer'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_ics_default_organizer() {
    return \apply_filters('bc_events_ics_default_organizer', \get_bloginfo('name'));
  }


  /**
   * hook_filter_ics_default_organizer_email
   *
   * @hook 'bc_events_ics_default_organizer_email'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_ics_default_organizer_email() {
    return \apply_filters('bc_events_ics_default_organizer_email', \get_bloginfo('admin_email'));
  }



  /**
   * hook_filter_exclude_cloned_meta_keys
   *
   * @hook 'bc_events_exclude_cloned_meta_keys'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_exclude_cloned_meta_keys(array $exclude_keys) {
    return \apply_filters('bc_events_exclude_cloned_meta_keys', $exclude_keys);
  }


  /**
   * hook_filter_finalized_exclude_cloned_meta_keys
   *
   * @hook 'bc_events_finalized_exclude_cloned_meta_keys'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_finalized_exclude_cloned_meta_keys(array $finalized_exclude_keys) {
    return \apply_filters('bc_events_finalized_exclude_cloned_meta_keys', $finalized_exclude_keys);
  }







  // ==============================
  //        Location Hooks
  // ==============================

  /**
   * hook_filter_use_event_locations
   *
   * @hook 'bc_events_use_event_locations'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_use_event_locations() {
    return \apply_filters('bc_events_use_event_locations', true);
  }



  /**
   * hook_filter_set_locations_public
   *
   * @hook 'bc_events_set_locations_public'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_set_locations_public() {
    return \apply_filters('bc_events_set_locations_public', false);
  }



  /**
   * hook_filter_set_locations_public
   *
   * @hook 'bc_events_set_locations_public'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_locations_supports($supports = ['title', 'thumbnail', 'slug', 'custom-fields']) {
    return \apply_filters('bc_events_locations_supports', $supports);
  }




  /**
   * hook_filter_event_locations_rewrite_slug
   *
   * @hook 'bc_events_locations_rewrite_slug'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_event_locations_rewrite_slug() {
    return \apply_filters('bc_events_locations_rewrite_slug', 'event-locations');
  }



  /**
   * hook_filter_set_locations_multiple
   *
   * @hook 'bc_events_set_locations_multiple'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_locations_allow_multiple() {
    return \apply_filters('bc_events_locations_allow_multiple', false);
  }



  // ==============================
  //        Contact Hooks
  // ==============================

  /**
   * hook_filter_use_event_contacts
   *
   * @hook 'bc_events_use_event_contacts'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_use_event_contacts() {
    return \apply_filters('bc_events_use_event_contacts', true);
  }



  /**
   * hook_filter_set_contacts_public
   *
   * @hook 'bc_events_set_contacts_public'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_set_contacts_public() {
    return \apply_filters('bc_events_set_contacts_public', false);
  }



  /**
   * hook_filter_set_contacts_public
   *
   * @hook 'bc_events_set_contacts_public'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_contacts_supports($supports = ['title', 'thumbnail', 'slug', 'custom-fields']) {
    return \apply_filters('bc_events_contacts_supports', $supports);
  }


  /**
   * hook_filter_event_contacts_rewrite_slug
   *
   * @hook 'bc_events_contacts_rewrite_slug'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_event_contacts_rewrite_slug() {
    return \apply_filters('bc_events_contacts_rewrite_slug', 'event-contacts');
  }


  /**
   * hook_filter_set_contacts_multiple
   *
   * @hook 'bc_events_set_contacts_multiple'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_contacts_allow_multiple() {
    return \apply_filters('bc_events_contacts_allow_multiple', false);
  }



  /**
   * hook_filter_event_phone_pattern
   *
   * @hook 'bc_events_phone_pattern'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_event_phone_pattern() {
    return \apply_filters('bc_events_phone_pattern', '[0-9]{3}-[0-9]{3}-[0-9]{4}');
  }



  /**
   * hook_filter_event_phone_description
   *
   * @hook 'bc_events_phone_description'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_event_phone_description() {
    return \apply_filters('bc_events_phone_description', 'Format: 123-456-7890');
  }



  // ==============================
  //         Series Hooks
  // ==============================

  /**
   * hook_filter_use_event_series
   *
   * @hook 'bc_events_use_event_series'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_use_event_series() {
    return \apply_filters('bc_events_use_event_series', true);
  }



  /**
   * hook_filter_set_series_public
   *
   * @hook 'bc_events_set_series_public'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_set_series_public() {
    return \apply_filters('bc_events_set_series_public', false);
  }


  /**
   * hook_filter_event_series_rewrite_slug
   *
   * @hook 'bc_events_series_rewrite_slug'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_event_series_rewrite_slug() {
    return \apply_filters('bc_events_series_rewrite_slug', 'event-series');
  }



  /**
   * hook_filter_set_series_public
   *
   * @hook 'bc_events_set_series_public'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_series_supports($supports = ['title', 'thumbnail', 'slug', 'custom-fields']) {
    return \apply_filters('bc_events_series_supports', $supports);
  }


  /**
   * hook_filter_set_series_multiple
   *
   * @hook 'bc_events_set_series_multiple'
   * @hookType filter
   * @return bool
   */
  public static function hook_filter_series_allow_multiple() {
    return \apply_filters('bc_events_series_allow_multiple', false);
  }


  /**
   * hook_filter_series_display_name_tooltip_text
   *
   * @hook 'bc_events_series_display_name_tooltip_text'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_series_display_name_tooltip_text() {
    $text = 'Use to display a name on the front end different from the CMS title. This field allows CMS titles to be more exact and descriptive, but Display Names to be loose and understandable. <br><br>For example, the CMS title could be "Epic Film Series 2022", but a Display Name value of "Epic Film Series" would display on the front end, rather than the CMS title.';
    return \apply_filters('bc_events_series_display_name_tooltip_text', $text);
  }


  /**
   * hook_filter_series_plural_tooltip_text
   *
   * @hook 'bc_events_series_plural_tooltip_text'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_series_plural_tooltip_text() {
    $text = 'Use this field if the plural name of a series is different from the series name. For example "League Nights" for the series "Bowling League".';
    return \apply_filters('bc_events_series_plural_tooltip_text', $text);
  }


  /**
   * hook_filter_series_single_tooltip_text
   *
   * @hook 'bc_events_series_single_tooltip_text'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_series_single_tooltip_text() {
    $text = 'Use this is the singular name of a series is different from the series name. For example "League Night" for the series "Bowling League".';
    return \apply_filters('bc_events_series_single_tooltip_text', $text);
  }



  // ==============================
  //         Display Hooks
  // ==============================

  /**
   * hook_filter_archive_settings
   *
   * @hook 'bc_events_archive_settings'
   * @hookType filter
   * @return array
   */
  // self::$filter_archive_settings
  public static function hook_filter_archive_settings() {

    $args = [
      'layout' => 'list',                       // list, week, day
      'per_page' => 12,                         // per page count
      'past_parameter' => 'is-past',            // applies to list view only
      'starting_on_parameter' => 'starting-on', // applies to list view only
      'dedupe_main_query' => false,
    ];

    $args  = \apply_filters('bc_events_archive_settings', $args);

    // Enforce some things...
    if ( !isset($args['layout']) || empty($args['layout']) || !in_array( $args['layout'], ['list', 'week'] ) ) {
      $args['layout'] = 'list';
    }

    if ( !isset($args['per_page']) || empty($args['per_page']) ) {
      $args['per_page'] = 12;
    }

    if ( !isset($args['past_parameter']) || empty($args['past_parameter']) ) {
      $args['past_parameter'] = 'is-past';
    }

    if ( !isset($args['starting_on_parameter']) || empty($args['starting_on_parameter']) ) {
      $args['starting_on_parameter'] = 'starting-on';
    }

    if ( !isset($args['dedupe_main_query']) || empty($args['dedupe_main_query']) ) {
      $args['dedupe_main_query'] = false;
    }

    return $args;
  }


  /**
   * hook_filter_load_template_css
   *
   * @hook 'bc_events_load_template_css'
   * @hookType filter
   * @return boolean
   */
  public static function hook_filter_load_template_css() {
    return \apply_filters('bc_events_load_template_css', true);
  }

  /**
   * hook_filter_load_template_js
   *
   * @hook 'bc_events_load_template_js'
   * @hookType filter
   * @return boolean
   */
  public static function hook_filter_load_template_js() {
    return \apply_filters('bc_events_load_template_js', true);
  }



  // ==============================
  //     Taxonomy/Filter Hooks
  // ==============================

  /**
   * hook_filter_taxonomy_query_params
   *
   * @hook 'bc_events_taxonomy_query_params'
   * @hookType filter
   * @example
   * [
   *   'taxonomy_name' => [
   *      'parameter' => 'parameter-name',
   *    ]
   * ]
   * @return array
   */
  public static function hook_filter_filter_taxonomies() {
    return \apply_filters('bc_events_filter_taxonomies', []);
  }


  /**
   * hook_apply_taxonomy_term_pages
   *
   * Apply event queries to taxonomy term archive pages
   *
   * @hook 'bc_events_apply_taxonomy_term_pages'
   * @hookType taxonomy
   * @example ['event_type', 'audience']
   * @return array
   */
  public static function hook_apply_taxonomy_term_pages() {
    return \apply_filters('bc_events_apply_taxonomy_term_pages', []);
  }



  /**
   * hook_filter_taxonomy_query_params
   * IN DEV
   *
   * @hook 'bc_events_taxonomy_query_params'
   * @hookType filter
   * @example
   * [
   *   'meta_key' => [
   *      'label'      => 'Label Name',
   *      'input_type' => 'checkbox', // checkbox, radio, select
   *      'value'      => 'on', // or string or array
   *    ]
   * ]
   * @return array
   */
  public static function hook_filter_filter_meta() {
    return \apply_filters('bc_events_filter_meta', []);
  }



  /**
   * hook_filter_taxonomy_query_field
   *
   * @hook 'bc_events_taxonomy_query_field'
   * @hookType filter
   * @return string
   */
  public static function hook_filter_taxonomy_query_field() {
    $field = \apply_filters('bc_events_filter_taxonomy_query_field', 'slug');

    if ( empty($field) || !in_array( $field, ['slug', 'term_id'] ) ) {
      $field = 'slug';
    }

    return $field;
  }



    // ==============================
    //       REST API Hooks
    // ==============================

    /**
     * hook_filter_get_events_post_values
     *
     * @hook 'bc_events_get_events_post_values'
     * @hookType filter
     * @return array
     */
    public static function hook_filter_get_events_post_values($post_data, $post) {
      return \apply_filters('bc_events_get_events_post_values', $post_data, $post);
    }
}
