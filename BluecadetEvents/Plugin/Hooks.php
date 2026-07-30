<?php

namespace BluecadetEvents\Plugin;

class Hooks {

  // ==========================================================================
  //                                Event Hooks
  // ==========================================================================


  // ==================================
  //  Settings
  // ==================================

  /**
   * Filters the events archive settings: the layout, per-page count, and the
   * query-string parameter names used on the list view.
   *
   * @hook 'bc_events/events/settings/archive'
   * @hook_object_type Events
   * @hook_category Settings
   * @hook_type filter
   * @since 1.0.0
   *
   * @param array $args The archive settings, with keys: 'layout' (string 'list'|'week', default 'list'), 'per_page' (int, default 12), 'past_parameter' (string, default 'is-past'), 'starting_on_parameter' (string, default 'starting-on'), 'dedupe_main_query' (bool, default false). Invalid values fall back to these defaults.
   * @return array The validated archive settings.
   */
  public static function hook_filter_archive_settings() {

    $args = [
      'layout' => 'list',                       // list, week, day
      'per_page' => 12,                         // per page count
      'past_parameter' => 'is-past',            // applies to list view only
      'starting_on_parameter' => 'starting-on', // applies to list view only
      'dedupe_main_query' => false,
    ];

    $filtered = \apply_filters('bc_events/events/settings/archive', $args);
    $args     = is_array($filtered) ? $filtered : $args;

    // Enforce every known key to a safe, correctly-typed value.
    $args['layout'] = ( isset($args['layout']) && in_array($args['layout'], ['list', 'week'], true) )
      ? $args['layout'] : 'list';

    $args['per_page'] = ( isset($args['per_page']) && is_numeric($args['per_page']) && (int) $args['per_page'] > 0 )
      ? (int) $args['per_page'] : 12;

    $args['past_parameter'] = ( isset($args['past_parameter']) && is_string($args['past_parameter']) && $args['past_parameter'] !== '' )
      ? $args['past_parameter'] : 'is-past';

    $args['starting_on_parameter'] = ( isset($args['starting_on_parameter']) && is_string($args['starting_on_parameter']) && $args['starting_on_parameter'] !== '' )
      ? $args['starting_on_parameter'] : 'starting-on';

    $args['dedupe_main_query'] = isset($args['dedupe_main_query']) ? (bool) $args['dedupe_main_query'] : false;

    return $args;
  }



  /**
   * hook_filter_frequency_options
   *
   * Array can only contain 'daily', 'weekly', 'monthly', and 'consecutive'.
   * If array is empty, no recurring frequency options will be available
   *
   * @hook 'bc_events/events/settings/frequency_options'
   * @hook_object_type Events
   * @hook_category Settings
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_frequency_options() {

    $allowed = [
      'daily' => 'Daily',
      'weekly' => 'Weekly',
      'monthly' => 'Monthly',
      'consecutive' => 'Consecutive',
    ];

    $options = \apply_filters('bc_events/events/settings/frequency_options', $allowed);

    if ( !is_array($options) || empty($options) ) {
      $options = $allowed;
    }

    foreach ($options as $key => $value) {
      if ( !array_key_exists($key, $allowed) ) {
        unset($options[$key]);
      }
    }

    if ( !is_array($options) || empty($options) ) {
      $options = $allowed;
    }

    return $options;
  }



  // ==================================
  //  Post Type
  // ==================================


  /**
   * hook_filter_events_rewrite_slug
   *
   * @hook 'bc_events/events/post_type/rewrite_slug'
   * @hook_object_type Events
   * @hook_category Post Type
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_events_rewrite_slug() : string {
    return self::guard_string(
      \apply_filters('bc_events/events/post_type/rewrite_slug', 'events'),
      'events'
    );
  }


  /**
   * hook_filter_events_gutenberg_template
   *
   *
   * @hook 'bc_events/events/post_type/gutenberg_template'
   * @hook_object_type Events
   * @hook_category Post Type
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_events_gutenberg_template(array $template) : array {
    return self::guard_array(
      \apply_filters('bc_events/events/post_type/gutenberg_template', $template),
      []
    );
  }



  // ==================================
  //  Save
  // ==================================

  /**
   * hook_filter_exclude_meta_keys
   *
   * @hook 'bc_events/events/exclude_copy_meta_keys'
   * @hook_object_type Events
   * @hook_category Save
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_exclude_meta_keys() : array {
    return self::guard_array(
      \apply_filters('bc_events/events/save/exclude_copy_meta_keys', []),
      []
    );
  }



  /**
   * hook_filter_finalized_exclude_cloned_meta_keys
   *
   * @hook 'bc_events/events/save/finalized_exclude_cloned_meta_keys'
   * @hook_object_type Events
   * @hook_category Save
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_finalized_exclude_cloned_meta_keys(array $finalized_exclude_keys) : array {
    return self::guard_array(
      \apply_filters('bc_events/events/save/finalized_exclude_cloned_meta_keys', $finalized_exclude_keys),
      $finalized_exclude_keys
    );
  }



  // ==================================
  //  Display
  // ==================================

  /**
   * hook_filter_date_display_format
   *
   * @hook 'bc_events/events/display/date_format'
   * @hook_object_type Events
   * @hook_category Display
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_date_display_format() : string {
    return self::guard_string(
      \apply_filters('bc_events/events/display/date_format', 'F j, Y'),
      'F j, Y'
    );
  }



  /**
   * hook_filter_time_display_format
   *
   * @hook 'bc_events/events/display/time_format'
   * @hook_object_type Events
   * @hook_category Display
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_time_display_format() : string {
    return self::guard_string(
      \apply_filters('bc_events/events/display/time_format', 'g:ia'),
      'g:ia'
    );
  }



  /**
   * hook_filter_date_time_sep_format
   *
   * @hook 'bc_events/events/display/date_time_sep_format'
   * @hook_object_type Events
   * @hook_category Display
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_date_time_sep_format() : string {
    return self::guard_string(
      \apply_filters('bc_events/events/display/date_time_sep_format', ' | '),
      ' | ',
      true // a separator may legitimately be an empty string
    );
  }



  /**
   * Filters the heading shown above the single-day archive view.
   *
   * @hook 'bc_events/events/display/day_view_title'
   * @hook_object_type Events
   * @hook_category Display
   * @hook_type filter
   * @since 1.0.0
   *
   * @param string    $title The formatted day heading. Default e.g. "Monday, September 7th".
   * @param \DateTime $date  The day being rendered.
   * @return string The heading to display above the day view.
   */
  public static function hook_filter_day_view_title(\DateTime $date) : string {
    $title = $date->format('l, F jS');
    return self::guard_string(
      \apply_filters('bc_events/events/display/day_view_title', $title, $date),
      $title
    );
  }



  /**
   * hook_filter_week_of_view_title
   *
   * @hook 'bc_events/events/display/week_of_view_title'
   * @hook_object_type Events
   * @hook_category Display
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_week_of_view_title(\DateTime $date) : string {
    $title = 'Week of ' . $date->format('F jS, Y');
    return self::guard_string(
      \apply_filters('bc_events/events/display/week_of_view_title', $title, $date),
      $title
    );
  }



  /**
   * hook_filter_month_of_view_title
   *
   * @hook 'bc_events/events/display/month_of_view_title'
   * @hook_object_type Events
   * @hook_category Display
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_month_of_view_title(\DateTime $date) : string {
    $title = $date->format('F Y');
    return self::guard_string(
      \apply_filters('bc_events/events/display/month_of_view_title', $title, $date),
      $title
    );
  }



  /**
   * TODO: KEEP OR REMOVE
   *
   * hook_filter_use_event_recurring_description
   *
   * @hook 'bc_events/events/display/use_event_recurring_description'
   * @hook_object_type Events
   * @hook_category Display
   * @hook_type filter
   * @return bool
   */
  public static function hook_filter_use_event_recurring_description() : bool {
    return self::guard_bool(
      \apply_filters('bc_events/events/display/use_event_recurring_description', true),
      true
    );
  }

  /**
   * TODO: KEEP OR REMOVE
   *
   * hook_filter_recurring_description_helper_text
   *
   * @hook 'bc_events/events/display/recurring_description_helper_text'
   * @hook_object_type Events
   * @hook_category Display
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_recurring_description_helper_text() : string {
    $default = "i.e. 'Daily' or 'Every Monday'";
    return self::guard_string(
      \apply_filters('bc_events/events/display/recurring_description_helper_text', $default),
      $default,
      true // may be emptied to hide the helper text
    );
  }


  // ==================================
  //  Schema
  // ==================================


  /**
   * TODO: ADD SUPPORT FOR THIS
   *
   * hook_filter_event_schema
   *
   * @hook 'bc_events/events/schema/event_schema'
   * @hook_object_type Events
   * @hook_category Schema
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_event_schema(array $schema, int $post_id, \WP_Post $post) : array {
    return self::guard_array(
      \apply_filters('bc_events/events/schema/event_schema', $schema, $post_id, $post),
      $schema
    );
  }


  /**
   * TODO: ADD SUPPORT FOR THIS
   *
   * hook_filter_include_schema
   *
   * @hook 'bc_events/events/schema/include_schema'
   * @hook_object_type Events
   * @hook_category Schema
   * @hook_type filter
   * @return bool
   */
  public static function hook_filter_include_schema() : bool {
    return self::guard_bool(
      \apply_filters('bc_events/events/schema/include_schema', true),
      true
    );
  }



  // ==================================
  //  ICS
  // ==================================


  /**
   * hook_filter_ics_event_title
   *
   * @hook 'bc_events/events/ics/event_title'
   * @hook_object_type Events
   * @hook_category ICS
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_ics_event_title(string $title, int $post_id) : string {
    return self::guard_string(
      \apply_filters('bc_events/events/ics/event_title', $title, $post_id),
      $title
    );
  }


  /**
   * hook_filter_ics_default_organizer
   *
   * @hook 'bc_events/events/ics/default_organizer'
   * @hook_object_type Events
   * @hook_category ICS
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_ics_default_organizer() : string {
    $default = (string) \get_bloginfo('name');
    return self::guard_string(
      \apply_filters('bc_events/events/ics/default_organizer', $default),
      $default,
      true
    );
  }


  /**
   * hook_filter_ics_default_organizer_email
   *
   * @hook 'bc_events/events/ics/default_organizer_email'
   * @hook_object_type Events
   * @hook_category ICS
   * @hook_type filter
   * @return string
   */
  // public static function hook_filter_ics_default_organizer_email() {
  //   return \apply_filters('bc_events_ics_default_organizer_email', \get_bloginfo('admin_email'));
  // }



  // ==================================
  //  Render
  // ==================================

  /**
   * hook_action_event_dates_meta_block_render
   *
   * @hook 'bc_events/events/render/event_dates_meta_block'
   * @hook_object_type Events
   * @hook_category Render
   * @hook_type action
   * @return void
   */
  public static function hook_action_event_dates_meta_block_render(array $attributes, \WP_Block $block) {
    do_action( 'bc_events/events/render/event_dates_meta_block', $attributes, $block );
  }



  // ==========================================================================
  //                               Location Hooks
  // ==========================================================================



  // ==================================
  //  Post Type
  // ==================================



  /**
   * hook_filter_use_event_locations
   *
   * @hook 'bc_events/locations/post_type/use_event_locations'
   * @hook_object_type Locations
   * @hook_category Post Type
   * @hook_type filter
   * @return bool
   */
  public static function hook_filter_use_event_locations() : bool {
    return self::guard_bool(
      \apply_filters('bc_events/locations/post_type/use_event_locations', true),
      true
    );
  }


  /**
   * hook_filter_event_locations_rewrite_slug
   *
   * @hook 'bc_events/locations/post_type/rewrite_slug'
   * @hook_object_type Locations
   * @hook_category Post Type
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_locations_rewrite_slug() : string {
    return self::guard_string(
      \apply_filters('bc_events/locations/post_type/rewrite_slug', 'event-locations'),
      'event-locations'
    );
  }


  /**
   * hook_filter_set_locations_public
   *
   * @hook 'bc_events/locations/post_type/set_locations_public'
   * @hook_object_type Locations
   * @hook_category Post Type
   * @hook_type filter
   * @return bool
   */
  public static function hook_filter_set_locations_public() : bool {
    return self::guard_bool(
      \apply_filters('bc_events/locations/post_type/set_locations_public', true),
      true
    );
  }


  /**
   * hook_filter_locations_gutenberg_template
   *
   * @hook 'bc_events/locations/post_type/gutenberg_template'
   * @hook_object_type Locations
   * @hook_category Post Type
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_locations_gutenberg_template(array $template) : array {
    return self::guard_array(
      \apply_filters('bc_events/locations/post_type/gutenberg_template', $template),
      $template
    );
  }



  /**
   * hook_filter_set_locations_supports
   *
   * @hook 'bc_events/locations/post_type/set_locations_supports'
   * @hook_object_type Locations
   * @hook_category Post Type
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_locations_supports(array $supports = ['title', 'thumbnail', 'slug', 'custom-fields', 'editor']) : array {
    return self::guard_array(
      \apply_filters('bc_events/locations/post_type/set_locations_supports', $supports),
      $supports
    );
  }




  /**
   * hook_filter_event_locations_rewrite_slug
   *
   * @hook 'bc_events/locations/post_type/rewrite_slug'
   * @hook_object_type Locations
   * @hook_category Post Type
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_rewrite_slug() : string {
    return self::guard_string(
      \apply_filters('bc_events/locations/post_type/rewrite_slug', 'event-locations'),
      'event-locations'
    );
  }



  // /**
  //  * hook_filter_set_locations_multiple
  //  *
  //  * @hook 'bc_events/locations/post_type/set_locations_multiple'
  //  * @hook_object_type Locations
  //  * @hook_category Post Type
  //  * @hook_type filter
  //  * @return mixed
  //  */
  // public static function hook_filter_locations_allow_multiple() : mixed {
  //   return \apply_filters('bc_events/locations/post_type/set_locations_multiple', false);
  // }




  // ==========================================================================
  //                            Series Hooks
  // ==========================================================================


  // ==================================
  //  Post Type
  // ==================================

  /**
   * hook_filter_use_event_series
   *
   * @hook 'bc_events/series/post_type/use_event_series'
   * @hook_object_type Series
   * @hook_category Post Type
   * @hook_type filter
   * @return bool
   */
  public static function hook_filter_use_event_series() : bool {
    return self::guard_bool(
      \apply_filters('bc_events/series/post_type/use_event_series', true),
      true
    );
  }



  /**
   * hook_filter_series_gutenberg_template
   *
   * @hook 'bc_events/series/post_type/gutenberg_template'
   * @hook_object_type Series
   * @hook_category Post Type
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_series_gutenberg_template(array $template) : array {
    return self::guard_array(
      \apply_filters('bc_events/series/post_type/gutenberg_template', $template),
      $template
    );
  }



  /**
   * hook_filter_set_series_public
   *
   * @hook 'bc_events/series/post_type/set_series_public'
   * @hook_object_type Series
   * @hook_category Post Type
   * @hook_type filter
   * @return bool
   */
  public static function hook_filter_set_series_public() : bool {
    return self::guard_bool(
      \apply_filters('bc_events/series/post_type/set_series_public', false),
      false
    );
  }


  /**
   * hook_filter_event_series_rewrite_slug
   *
   * @hook 'bc_events/series/post_type/rewrite_slug'
   * @hook_object_type Series
   * @hook_category Post Type
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_event_series_rewrite_slug() : string {
    return self::guard_string(
      \apply_filters('bc_events/series/post_type/rewrite_slug', 'event-series'),
      'event-series'
    );
  }



  /**
   * hook_filter_series_supports
   *
   * @hook 'bc_events/series/post_type/supports'
   * @hook_object_type Series
   * @hook_category Post Type
   * @hook_type filter
   * @return array
   */
  public static function hook_filter_series_supports(array $supports = ['title', 'thumbnail', 'slug', 'custom-fields', 'editor']) : array {
    return self::guard_array(
      \apply_filters('bc_events/series/post_type/supports', $supports),
      $supports
    );
  }




  // ==========================================================================
  //                          Query Filter Hooks
  // ==========================================================================


  /**
   * hook_filter_taxonomy_query_params
   *
   * @hook 'bc_events/query_filters/taxonomies'
   * @hook_object_type Events
   * @hook_category Query Filters
   * @hook_type filter
   * @example
   * [
   *   'taxonomy_name' => [
   *      'parameter' => 'parameter-name',
   *    ]
   * ]
   * @return array
   */
  public static function hook_filter_filter_taxonomies() : array {
    return self::guard_array(
      \apply_filters('bc_events/query_filters/taxonomies', []),
      []
    );
  }



  /**
   * hook_apply_taxonomy_term_pages
   *
   * Apply event queries to taxonomy term archive pages
   *
   * @hook 'bc_events/query_filters/apply_taxonomy_term_pages'
   * @hook_object_type Events
   * @hook_category Query Filters
   * @hook_type taxonomy
   * @example ['event_type', 'audience']
   * @return array
   */
  public static function hook_apply_taxonomy_term_pages() : array {
    return self::guard_array(
      \apply_filters('bc_events/query_filters/apply_taxonomy_term_pages', []),
      []
    );
  }



  /**
   * hook_filter_taxonomy_query_field
   *
   * @hook 'bc_events/query_filters/taxonomy_query_field'
   * @hook_object_type Events
   * @hook_category Query Filters
   * @hook_type filter
   * @return string
   */
  public static function hook_filter_taxonomy_query_field() : string {
    $field = \apply_filters('bc_events/query_filters/taxonomy_query_field', 'slug');

    if ( !is_string($field) || empty($field) || !in_array( $field, ['slug', 'term_id'], true ) ) {
      $field = 'slug';
    }

    return $field;
  }



  // ==========================================================================
  //  Return-value guards
  //
  //  Filters are user-supplied. These keep a bad return value (wrong type,
  //  empty where content is required, arbitrary garbage) from fataling or
  //  corrupting behavior by falling back to the documented default.
  // ==========================================================================

  private static function guard_string( mixed $value, string $default, bool $allow_empty = false ) : string {
    if ( is_string($value) && ( $allow_empty || $value !== '' ) ) {
      return $value;
    }
    return $default;
  }

  private static function guard_bool( mixed $value, bool $default ) : bool {
    return is_bool($value) ? $value : $default;
  }

  private static function guard_array( mixed $value, array $default, bool $allow_empty = true ) : array {
    if ( is_array($value) && ( $allow_empty || ! empty($value) ) ) {
      return $value;
    }
    return $default;
  }



  // +=========================================================================+
  // MAYBE DELETE?
  // +=========================================================================+


  /**
   * hook_filter_exclude_acf_keys
   *
   * @hook 'bc_events_exclude_copy_acf_keys'
   * @hook_type filter
   * @return array
   */
  // public static function hook_filter_exclude_acf_keys() : array {
  //   return \apply_filters('bc_events_exclude_copy_acf_keys', []);
  // }


  /**
   * hook_filter_exclude_acf_keys_if_empty
   *
   * @hook 'bc_events_exclude_copy_acf_keys_if_empty'
   * @hook_type filter
   * @return array
   */
  // public static function hook_filter_exclude_acf_keys_if_empty() : array {
  //   return \apply_filters('bc_events_exclude_copy_acf_keys_if_empty', []);
  // }



  // ==============================
  //       REST API Hooks
  // ==============================

  /**
   * hook_filter_get_events_post_values
   *
   * @hook 'bc_events_get_events_post_values'
   * @hook_type filter
   * @return array
   */
  // public static function hook_filter_get_events_post_values($post_data, $post) {
  //   return \apply_filters('bc_events_get_events_post_values', $post_data, $post);
  // }



  /**
   * hook_filter_taxonomy_query_params
   * IN DEV
   *
   * @hook 'bc_events/query_filters/taxonomy_query_params'
   * @hook_type filter
   * @example
   * [
   *   'meta_key' => [
   *      'label'      => 'Label Name',
   *      'input_type' => 'checkbox', // checkbox, radio, select
   *      'value'      => 'on', // or string or array
   *    ]
   * ]
   * @return mixed
   */
  // public static function hook_filter_filter_meta() : mixed {
  //   return \apply_filters('bc_events/query_filters/meta', []);
  // }
}
