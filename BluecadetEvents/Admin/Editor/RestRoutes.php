<?php

namespace BluecadetEvents\Admin\Editor;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Meta\MetaKeys;

/**
 * Handle date formatting
 *
 */
class RestRoutes {

  public function __construct() {
    add_action( 'rest_api_init', [$this, 'rest_routes'] );
  }



  public function rest_routes() {
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
			'/get-keys',
			array(
				'methods'             => 'GET',
				'callback'            => [$this, 'get_keys'],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
  }


  public function to_timestamp(\WP_REST_Request $request) : \WP_REST_Response | \WP_Error {
    $date = $request->get_param( 'date' );
    $time = $request->get_param( 'time' );
    $tz = wp_timezone();

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

    $date_time_str = $date . ' ' . ( $time ? $time : '00:00' );
    $date_time = \DateTime::createFromFormat( 'Y-m-d H:i', $date_time_str, $tz );

    if ( ! $date_time ) {
      return new \WP_Error( 'invalid_date_time', 'Invalid date/time', [ 'status' => 400 ] );
    }

    $results = [
      'timestamp' => $date_time->getTimestamp(),
    ];

		return rest_ensure_response( $results );
  }


  public function get_keys() : \WP_REST_Response | \WP_Error {
    $keys = MetaKeys::get_keys();
    return rest_ensure_response( $keys );
  }


}
