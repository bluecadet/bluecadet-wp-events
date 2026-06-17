<?php

namespace BluecadetEvents\Rest;
use BluecadetEvents\Plugin\Settings;

class PublicEndpoints {

  private string $rest_namespace;
  private array $datepicker_data;
  private \DateTimeZone $timezone;

  public function __construct() {
    $this->rest_namespace = Settings::$rest_namespace;
    $this->timezone       = \wp_timezone();
    add_action( 'rest_api_init', [$this, 'handle_endpoints'] );
  }



  /**
   * Add rest routes
   *
   * @return void
   */
  public function handle_endpoints() : void {

    register_rest_route( $this->rest_namespace, '/datepicker', array(
      'methods' => 'GET',
      'callback' => [$this, 'handle_datepicker'],
      'permission_callback' => '__return_true',
    ) );
  }


  /**
   * Datepicker Endpoint
   *
   * @param \WP_REST_Request $request
   * @return \WP_REST_Response|\WP_Error
   */
  public function handle_datepicker(\WP_REST_Request $request) : \WP_REST_Response | \WP_Error {
    $cache_key = 'bc-events-datepicker';
    $tax       = false;
    $tax_field = 'slug';
    $term      = false;

    $response = [
      'status' => 'error',
    ];

    if ( $request->has_param('taxonomy') && $request->has_param('term') ) {

      $tax       = sanitize_text_field($request->get_param('taxonomy'));
      $term      = sanitize_text_field($request->get_param('term'));
      $cache_key = $cache_key . ': ' . $tax . ': ' . $term;
      $term      = explode(',', $term);
      

      if ( $request->has_param('tax_field') ) {
        $f = sanitize_text_field($request->get_param('tax_field'));
        if ( $f === 'id' ) {
          $tax_field = 'id';
        }
      }
    }

    $transient_data = \get_transient( $cache_key );

    if ( $transient_data !== FALSE ) {
      $response = [
        'status'          => 'success',
        'cache_key'       => $cache_key,
        'cached_response' => true,
        'data'            => $transient_data,
      ];

      return \rest_ensure_response($response);
    }

    $args = [
      'post_type'      => Settings::$events_machine_name,
      'posts_per_page' => -1,
      'fields'         => 'ids',
    ];

    if ( $tax && $term ) {
      $args['tax_query'] = [
        [
          'taxonomy' => $tax,
          'terms'    => $term,
          'field'    => $tax_field,
        ]
      ];
    }

    $response['args'] = $args;

    $events = new \WP_Query($args);

    if ( $events->have_posts() ) {

      $response['status'] = 'success';
      $response['cache_key'] = $cache_key;
      $response['cached_response'] = false;

      $this->datepicker_data = [];

      foreach ( $events->posts as $pID) {
        $start_timestamp = get_post_meta($pID, '_bc_events_start_timestamp', true);
        $end_timestamp   = get_post_meta($pID, '_bc_events_end_timestamp', true);

        if ( $start_timestamp && $end_timestamp) {
          $start = new \DateTime('now', $this->timezone);
          $start->setTimestamp($start_timestamp);
          $s_ymd = $start->format('Ymd');

          $end = new \DateTime('now', $this->timezone);
          $end->setTimestamp($end_timestamp);
          $e_ymd = $end->format('Ymd');

          if ( $s_ymd !== $e_ymd ) {
            while ($s_ymd <= $e_ymd) {
              $this->add_timestamp_to_data(false, $start);
              $start->modify('+1 day');
              $start_timestamp = $start->format('U');
              $s_ymd = $start->format('Ymd');
            }

          } else {
            $this->add_timestamp_to_data($start_timestamp);
          }
        }
      }

      $response['data'] = $this->datepicker_data;
      set_transient( $cache_key, $this->datepicker_data, 3600 );

    }

    return \rest_ensure_response($response);
  }



  public function add_timestamp_to_data($timestamp = false, $date_obj = false) : bool {
    
    if ( $timestamp ) {
      $date = new \DateTime('now', $this->timezone);
      $date->setTimestamp($timestamp);
    } else if ( $date_obj ) {
      $date = $date_obj;
    } else {
      return false;
    }

    $y = $date->format('Y');
    $m = $date->format('m');
    $d = intval($date->format('j'));

    if ( !isset($this->datepicker_data[$y]) ) {
      $this->datepicker_data[$y] = [];
    }

    if ( !isset($this->datepicker_data[$y][$m]) ) {
      $this->datepicker_data[$y][$m] = [];
    }

    if ( !in_array($d, $this->datepicker_data[$y][$m]) ) {
      $this->datepicker_data[$y][$m][] = $d;
    }

    return true;
  }
  
}