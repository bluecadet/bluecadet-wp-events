<?php

namespace BluecadetEvents\Admin\Editor;
use BluecadetEvents\Admin\Utils\AbstractService;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;
use BluecadetEvents\Admin\Meta\Keys\LocationMetaKeys;
use BluecadetEvents\Admin\Meta\Keys\SeriesMetaKeys;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;

/**
 * Handle date formatting
 *
 */
class RestRoutes extends AbstractService {

  public function boot() : void {
    add_action( 'rest_api_init', [$this, 'rest_routes'] );
  }

  public function rest_routes() : void {
    $namespace = Settings::$rest_namespace;

    // Time to timestamp
    register_rest_route(
			$namespace,
			'/to-timestamp',
			array(
				'methods'             => 'GET',
				'callback'            => [$this, 'to_timestamp'],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'date' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
          'time' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

    // Get Meta Keys
    register_rest_route(
			$namespace,
			'/get-support-settings',
			array(
				'methods'             => 'GET',
				'callback'            => [$this, 'get_support_settings'],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);


    // Get Meta Keys
    register_rest_route(
			$namespace,
			'/get-keys',
			array(
				'methods'             => 'GET',
				'callback'            => [$this, 'get_keys'],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

    // Get Location Meta Keys
    register_rest_route(
			$namespace,
			'/get-locations-keys',
			array(
				'methods'             => 'GET',
				'callback'            => [$this, 'get_location_keys'],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

    // Get Series Meta Keys
    register_rest_route(
			$namespace,
			'/get-series-keys',
			array(
				'methods'             => 'GET',
				'callback'            => [$this, 'get_series_keys'],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);


    // Get Meta Keys
    register_rest_route(
			$namespace,
			'/is-child',
			array(
				'methods'             => 'GET',
				'callback'            => [$this, 'is_child'],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
        'args'                => array(
          'id' => array(
            'required'          => true,
            'sanitize_callback' => 'sanitize_text_field',
          ),
        ),
			)
		);
  }


  public function to_timestamp(\WP_REST_Request $request) : \WP_REST_Response | \WP_Error {
    $date = $request->get_param( 'date' );
    $time = $request->get_param( 'time' );

    if ( ! $date ) {
      return new \WP_Error( 'missing_date', 'Date parameter is required', [ 'status' => 400 ] );
    }

    // Check date is YYYY-MM-DD
    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
      return new \WP_Error( 'invalid_date_format', 'Date must be in YYYY-MM-DD format', [ 'status' => 400 ] );
    }

    // If time is provided, check it's HH:MM
    if ( $time && ! preg_match( '/^\d{2}:\d{2}$/', $time ) ) {
      return new \WP_Error( 'invalid_time_format', 'Time must be in HH:MM format', [ 'status' => 400 ] );
    }

    $timestamp = self::date_time_to_timestamp( $date, (string) $time );

    if ( $timestamp === null ) {
      return new \WP_Error( 'invalid_date_time', 'Invalid date/time', [ 'status' => 400 ] );
    }

    $results = [
      'timestamp' => $timestamp,
    ];

		return rest_ensure_response( $results );
  }


  /**
   * Convert a site-timezone date (YYYY-MM-DD) and optional time (HH:MM) to a
   * Unix timestamp. Shared by the editor route and the abilities.
   *
   * The leading '!' zeroes the unparsed fields; without it createFromFormat
   * fills the seconds from the current clock.
   *
   * @param string $date YYYY-MM-DD
   * @param string $time HH:MM, empty for midnight
   * @return int|null Null when the date/time is invalid.
   */
  public static function date_time_to_timestamp( string $date, string $time = '' ) : ?int {
    $date_time = \DateTime::createFromFormat( '!Y-m-d H:i', $date . ' ' . ( $time ?: '00:00' ), \wp_timezone() );
    $errors    = \DateTime::getLastErrors();

    return $date_time && ( false === $errors || ( 0 === $errors['warning_count'] && 0 === $errors['error_count'] ) )
      ? $date_time->getTimestamp()
      : null;
  }


  public function get_keys() : \WP_REST_Response | \WP_Error {
    $keys = EventsMetaKeys::get_keys();
    return rest_ensure_response( $keys );
  }


  public function get_location_keys() : \WP_REST_Response | \WP_Error {
    $keys = LocationMetaKeys::get_keys();
    return rest_ensure_response( $keys );
  }

  public function get_series_keys() : \WP_REST_Response | \WP_Error {
    $keys = SeriesMetaKeys::get_keys();
    return rest_ensure_response( $keys );
  }



  public function is_child(\WP_REST_Request $request) : \WP_REST_Response | \WP_Error {
    $id = $request->get_param( 'id' );

    if ( ! $id ) {
      return new \WP_Error( 'missing_id', 'ID parameter is required', [ 'status' => 400 ] );
    }

    $value = false;

    $db_helpers = DatabaseHelpers::get_instance();
    $is_child = $db_helpers->is_recurring_child($id);

    if ( is_array($is_child) && !empty($is_child) ) {
      $parent_id = $is_child[0];
      $value = [
        'parent_url' => get_edit_post_link($parent_id),
        'parent_id' => $parent_id,
        'parent_title' => get_the_title($parent_id),
      ];
    }

    return \rest_ensure_response( $value );
  }



  /**
   * Provide support settings to Events block
   *
   * @param \WP_REST_Request $request
   * @return \WP_REST_Response | \WP_Error
   */
  public function get_support_settings(\WP_REST_Request $request) : \WP_REST_Response | \WP_Error {
    
    $settings = [
      'use_locations' => Hooks::hook_filter_use_event_locations(),
      'use_series' => Hooks::hook_filter_use_event_series(),
      'use_recuring_description' => Hooks::hook_filter_use_event_recurring_description(),
      'recurring_description_helper_text' => Hooks::hook_filter_recurring_description_helper_text(),
      'frequency_options' => Hooks::hook_filter_frequency_options(),
    ];

    return rest_ensure_response( $settings );
  }


}
