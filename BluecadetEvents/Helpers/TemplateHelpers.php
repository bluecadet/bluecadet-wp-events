<?php

namespace BluecadetEvents\Helpers;

use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Plugin\Settings;

class TemplateHelpers {

  private static ?TemplateHelpers $instance = null;
  private array $options = [];
  private array $keys = [];

  private function __construct() {
    $this->keys = EventsMetaKeys::get_keys();
  }

  public static function getInstance() : self {
    if ( self::$instance === null ) {
      self::$instance = new self();
    }
    return self::$instance;
  }



  private function resolve_post_id( null|int|\WP_Post $post ) : int|false {
    if ( !$post ) {
      global $post;
    }

    if ( is_int( $post ) ) {
      return $post;
    }

    if ( isset( $post->ID ) ) {
      return (int) $post->ID;
    }

    return false;
  }



  /**
   * Get date display options
   *
   * @return array
   */
  public function get_date_display_options() : array {
    if ( empty( $this->options ) ) {
      $this->options = [
        'date_format'   => esc_html( Hooks::hook_filter_date_display_format() ),
        'time_format'   => esc_html( Hooks::hook_filter_time_display_format() ),
        'date_time_sep' => esc_html( Hooks::hook_filter_date_time_sep_format() ),
      ];
    }
    return $this->options;
  }



  // ========================================================================================================
  //  GENERAL DATE STUFF
  // ========================================================================================================


  /**
   * Get start and end date object
   *
   * Both entries are timezone-aware DateTimeImmutable instances, in the site timezone.
   *
   * @param null|int|\WP_Post $post
   * @return array{start: \DateTimeImmutable, end: \DateTimeImmutable}|false
   */
  public function get_event_date_objects( null|int|\WP_Post $post = null ) : array|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $tz              = \wp_timezone();
    $start_timestamp = $this->get_start_timestamp( $post_id );
    $end_timestamp   = $this->get_end_timestamp( $post_id );

    /*
     * DateTimeImmutable, NOT DateTime. On a mutable DateTime, setTimestamp() mutates the
     * receiver and returns $this — so building both entries from one object handed back two
     * references to the SAME instance, and because 'end' is assigned second, 'start' and
     * 'end' both reported the END time. Callers saw a zero-length event.
     *
     * On an immutable, setTimestamp() returns a new instance, so each entry is its own value.
     * Kept as setTimestamp() against a timezone-aware base rather than
     * `new DateTime( '@' . $timestamp )`, which forces UTC and would need a separate
     * setTimezone() call.
     */
    $base = new \DateTimeImmutable( 'now', $tz );

    if ( $start_timestamp && $end_timestamp ) {
      return [
        'start' => $base->setTimestamp( $start_timestamp ),
        'end'   => $base->setTimestamp( $end_timestamp ),
      ];
    }

    return false;
  }


  // ========================================================================================================
  //  START DATE STUFF
  // ========================================================================================================

  public function get_start_timestamp( null|int|\WP_Post $post = null ) : int|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( $field = get_post_meta( $post_id, $this->keys['start_timestamp'], true ) ) {
      return (int) $field;
    }

    return false;
  }



  /**
   * Get formatted start date
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_start_date( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['start_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['date_format'] );
    }

    return false;
  }



  /**
   * Get formatted start time
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_start_time( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['start_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['time_format'] );
    }

    return false;
  }



  /**
   * Get formatted start date and time
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_start_date_time( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['start_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['date_format'] . $settings['date_time_sep'] . $settings['time_format'] );
    }

    return false;
  }



  // ========================================================================================================
  //  END DATE STUFF
  // ========================================================================================================



  /**
   * Get end timestamp
   *
   * @param null|int|\WP_Post $post
   * @return int|false
   */
  public function get_end_timestamp( null|int|\WP_Post $post = null ) : int|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( $field = get_post_meta( $post_id, $this->keys['end_timestamp'], true ) ) {
      return (int) $field;
    }

    return false;
  }



  /**
   * Get formatted end date
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_end_date( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['end_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['date_format'] );
    }

    return false;
  }



  /**
   * Get formatted end time
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_end_time( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['end_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['time_format'] );
    }

    return false;
  }



  /**
   * Get formatted end date and time
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_end_date_time( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['end_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['date_format'] . $settings['date_time_sep'] . $settings['time_format'] );
    }

    return false;
  }



  // ========================================================================================================
  //  FORMATTING STUFF
  // ========================================================================================================


  /**
   * Given a timestamp, format a date string to event settings
   *
   * @param integer $timestamp
   * @return string
   */
  public function date_from_timestamp( int $timestamp ) : string {
    $settings = $this->get_date_display_options();
    $fDate    = new \DateTime( '', \wp_timezone() );
    $fDate->setTimestamp( $timestamp );
    return $fDate->format( $settings['date_format'] );
  }


  /**
   * Given a timestamp, format a time string to event settings
   *
   * @param integer $timestamp
   * @return string
   */
  public function time_from_timestamp( int $timestamp ) : string {
    $settings = $this->get_date_display_options();
    $fDate    = new \DateTime( '', \wp_timezone() );
    $fDate->setTimestamp( $timestamp );
    return $fDate->format( $settings['time_format'] );
  }



  // ========================================================================================================
  //  MULTIDAY STUFF
  // ========================================================================================================



  /**
   * Determine if post runs multiple days
   *
   * @param null|int|\WP_Post $post
   * @return boolean
   */
  public function is_multiday( null|int|\WP_Post $post = null ) : bool {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $start_timestamp = $this->get_start_timestamp( $post_id );
    $end_timestamp   = $this->get_end_timestamp( $post_id );

    if ( $start_timestamp && $end_timestamp && $end_timestamp > $start_timestamp ) {
      $start_date = ( new \DateTime( '', \wp_timezone() ) )->setTimestamp( $start_timestamp )->format( 'Ymd' );
      $end_date   = ( new \DateTime( '', \wp_timezone() ) )->setTimestamp( $end_timestamp )->format( 'Ymd' );

      return $start_date !== $end_date;
    }

    return false;
  }



  // ========================================================================================================
  //  RECURRING STUFF
  // ========================================================================================================


  /**
   * Is recurring
   *
   * @param null|int|\WP_Post $post
   * @return bool
   */
  public function is_recurring( null|int|\WP_Post $post = null ) : bool {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( get_post_meta( $post_id, $this->keys['is_recurring'], true ) ) {
      return true;
    }

    return false;
  }


  /**
   * Is parent
   * 
   * @param null|int|\WP_Post $post
   * @return bool
   */
  public function is_parent( null|int|\WP_Post $post = null ) : bool {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( get_post_meta( $post_id, $this->keys['is_parent'], true ) ) {
      return true;
    }

    return false;
  }



  /**
   * Get Recurring Child IDs
   *
   * @param null|int|\WP_Post $post
   * @return false|array
   */
  public function get_child_ids( null|int|\WP_Post $post = null ) : false|array {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $db_helpers = DatabaseHelpers::get_instance();
    $children = $db_helpers->get_recurring_child_ids($post_id);

    if ( $children && is_array($children) && !empty($children) ) {
      return $children;
    }

    return false;
  }



  /**
   * Get Recurring Child Posts
   *
   * @param null|int|\WP_Post $post
   * @return false|array
   */
  public function get_child_posts( null|int|\WP_Post $post = null ) : false|array {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $child_ids = $this->get_child_ids( $post_id );

    if ( $child_ids && is_array( $child_ids ) && !empty( $child_ids ) ) {
      $child_posts = array_map( function( $id ) {
        if ( $child_post = get_post( $id ) ) {
          return [
            ...$child_post,
            'start_timestamp' => $this->get_start_timestamp( $child_post->ID ),
            'end_timestamp'   => $this->get_end_timestamp( $child_post->ID ),
          ];
        }
        return null;
      }, $child_ids );

      return array_filter( $child_posts );
    }

    return false;
  }



  /**
   * Is child
   * 
   * @param null|int|\WP_Post $post
   * @return bool
   */
  public function is_child( null|int|\WP_Post $post = null ) : bool {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( get_post_meta( $post_id, $this->keys['parent_id'], true ) ) {
      return true;
    }

    return false;
  }



  /**
   * Check if the post is a child with custom content (i.e. content that differs from the parent)
   *
   * @param null|int|\WP_Post $post
   * @return bool
   */
  public function is_child_custom( null|int|\WP_Post $post = null ) : bool {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( get_post_meta( $post_id, $this->keys['custom_content'], true ) ) {
      return true;
    }

    return false;
  }



  // ========================================================================================================
  //  VIEW STUFF
  // ========================================================================================================


  /**
   * Get current view
   *
   * @param false|\WP_Query $custom_query Optional custom query to check for view parameter (defaults to global query)
   * @return string|false
   */
  public function get_view(false|\WP_Query $custom_query = false) : string|false {
    global $wp_query;

    $use_query = $custom_query instanceof \WP_Query ? $custom_query : $wp_query;

    if ( isset($use_query->bce_view) ) {
      return $use_query->bce_view;
    }

    if ( isset( $_GET['view'] ) && in_array( $_GET['view'], [ 'list', 'month', 'week', 'day' ] ) ) {
      return sanitize_text_field( $_GET['view'] );
    }

    return false;
  }



  /**
   * Get the title of the view set in a query
   *
   * @param false|\WP_Query $custom_query Optional custom query to check for view title (defaults to global query)
   * @return string|false
   */
  public function get_view_title(false|\WP_Query $custom_query = false) : string|false {
    global $wp_query;

    $use_query = $custom_query instanceof \WP_Query ? $custom_query : $wp_query;

    if ( isset($use_query->bce_view_title) ) {
      return $use_query->bce_view_title;
    }

    return false;
  }



  /**
   * Check if past is set in current view
   *
   * @return boolean
   */
  public function is_past() : bool {
    $settings = Hooks::hook_filter_archive_settings();

    $is_past = !empty($settings['past_parameter']) && isset($_GET[$settings['past_parameter']]);

    // Resolved through the same filter the archive query uses, so a theme that
    // overrides the view can't leave the template disagreeing with the results.
    $query = $GLOBALS['wp_query'] ?? null;

    return Hooks::hook_filter_is_past($is_past, $query instanceof \WP_Query ? $query : null);
  }



  /**
   * Pagination link array
   *
   * @return false|array
   */
  public function get_view_pagination_links() : false|array {
    global $wp_query, $wp;

    if ( !isset($wp_query->bce_view_wrapping_dates) || empty($wp_query->bce_view_wrapping_dates) ) {
      return false;
    }

    if ( 
      !isset($wp_query->bce_view_wrapping_dates['pagination_param']) || 
      !isset($wp_query->bce_view_wrapping_dates['prev_string']) || 
      !isset($wp_query->bce_view_wrapping_dates['next_string']) || 
      !isset($wp_query->bce_view_wrapping_dates['current_date']) 
    ) {
      return false;
    }

    $param = $wp_query->bce_view_wrapping_dates['pagination_param'];

    // Page from the path WordPress actually matched rather than the bare archive
    // link, so a view served by a rewrite rule (/events/past/, say) survives
    // paging. $wp->request is relative to the home path, which is what home_url()
    // wants, so a subdirectory install doesn't end up doubled.
    $base_url = ( isset($wp->request) && $wp->request !== '' )
      ? home_url( user_trailingslashit($wp->request) )
      : get_post_type_archive_link(Settings::$events_machine_name);

    // Carry the rest of the query string. The other view-date params are dropped
    // so paging one view never leaves a stale date from another behind.
    $carry = map_deep( wp_unslash($_GET), 'sanitize_text_field' );
    unset( $carry['day-of'], $carry['week-of'], $carry['month-of'] );

    if ( !empty($carry) ) {
      $base_url = add_query_arg( $carry, $base_url );
    }

    return [
      'prev'    => add_query_arg( $param, $wp_query->bce_view_wrapping_dates['prev_string'], $base_url ),
      'next'    => add_query_arg( $param, $wp_query->bce_view_wrapping_dates['next_string'], $base_url ),
      'current' => $wp_query->bce_view_wrapping_dates['current_date'],
    ];

  }



  /**
   * Get link to ICS file
   *
   * @param null|int|\WP_Post $post
   * @return false|string
   */
  public function get_ics_link( null|int|\WP_Post $post = null ) : false|string {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    return add_query_arg(Settings::$ics_param_name, '1', get_permalink($post));
  }



  /**
   * Get google cal link data
   *
   * @param null|int|\WP_Post $post
   * @return false|string
   */
  public function get_google_cal_link( null|int|\WP_Post $post = null ) : false|string {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $start = get_post_meta($post_id, $this->keys['start_timestamp'], true); // Unix timestamp
    $end   = get_post_meta($post_id, $this->keys['end_timestamp'], true);

    $params = http_build_query([
        'action'   => 'TEMPLATE',
        'text'     => get_the_title($post),
        'dates'    => gmdate('Ymd\THis\Z', $start) . '/' . gmdate('Ymd\THis\Z', $end),
        'details'  => wp_strip_all_tags(get_the_excerpt($post)),
        // 'location' => get_post_meta($post->ID, '_bc_event_location', true),
        'sprop'    => 'website:' . home_url(),
    ]);

    return 'https://calendar.google.com/calendar/render?' . $params;
  }

}
