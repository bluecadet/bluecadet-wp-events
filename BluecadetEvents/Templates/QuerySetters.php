<?php

namespace BluecadetEvents\Templates;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;

class QuerySetters {

  private array $settings;
  private array $keys;
  private \DateTimeZone $timezone;
  private \WP_Query $query;
  private array $args  = [];
  private array $query_decorators = [];
  private \DateTime $now;
  private int $now_ts;
  private \DateTime $now_date;
  private string|false $view_value;
  private string $view_name;
  private bool $is_past;
  private bool $starting_on;


  public function __construct(\WP_Query $query) {
    $this->query            = $query;
    $this->settings         = Hooks::hook_filter_archive_settings();
    $this->timezone         = \wp_timezone();
    $this->args             = [];
    $this->query_decorators = [];
    $this->keys             = EventsMetaKeys::get_keys();
    $this->now_date         = new \DateTime('now', $this->timezone);
    $this->now_ts           = $this->now_date->format('U');
    $this->is_past          = isset($_GET[$this->settings['past_parameter']]);
    $this->starting_on      = isset($_GET[$this->settings['starting_on_parameter']]);
  }



  /**
   * Set query values based on view name and view value
   *
   * @param string $view_name
   * @param string|false $view_value
   * @return void
   */
  public function set_query(string $view_name, string|false $view_value = false) : void {
    $this->view_value = \sanitize_text_field($view_value);
    $this->view_name  = $view_name;

    $this->set_args();
    $this->set_taxonomy_params_to_args();

    foreach ( $this->args as $arg => $value ) {
      $this->query->set($arg, $value);
    }

    foreach ( $this->query_decorators as $arg => $value ) {
      $this->query->$arg = $value;
    }
  }


  /**
   * Set args for query
   *
   * @return void
   */
  public function set_args() : void {
    $this->now_date   = new \DateTime('now', $this->timezone);
    $this->now_ts     = $this->now_date->format('U');
    

    switch ( $this->view_name ) {
      case 'day_of':
      case 'day':

        $this->set_day_view();
        break;

      case 'week_of':
      case 'week':

        $this->set_week_view();
        break;

      case 'month':
      case 'month_of':

        $this->set_month_view();
        break;

      // case 'day-list':

      //   $this->set_day_list_view();
      //   break;

      default:
        $this->set_list_view();
        break;
    }
  }



  /**
   * Set list view args
   *
   * @return void
   */
  private function set_list_view() : void {
    $this->args['posts_per_page'] = $this->settings['per_page'];
    $this->args['order']          = $this->is_past ? 'DESC' : 'ASC';

    $this->set_view_decorator('list');
    $this->set_view_status_decorator();

    if ( $this->is_past ) {
      $this->args['bc_events_query'] = 'past';
    } else {

      if ( $this->starting_on ) {
        $ymd = sanitize_text_field(wp_unslash($_GET[$this->settings['starting_on_parameter']]));

        if ( $ymdDate = \DateTime::createFromFormat('Y-m-d', $ymd, $this->timezone) ) {
          $ymdDate->setTime(0, 0, 0);
          $this->now_date = $ymdDate;
          $this->now_ts   = $this->now_date->format('U');
          $this->args['bc_events_timestamp'] = (int) $this->now_ts;
        }
      }

      $this->args['bc_events_query'] = 'upcoming';
    }
  }



  // private function set_day_list_view() {
  //   $this->args['posts_per_page'] = $this->settings['per_page'];
  //   $this->args['meta_key']       = $this->keys['start_timestamp'];
  //   $this->args['orderby']        = 'meta_value_num';
  //   $this->args['order']          = $this->is_past ? 'DESC' : 'ASC';
  //   $compare_operator             = $this->is_past ? '<=' : '>=';

  //   $this->set_view_decorator('list');
  //   $this->set_view_status_decorator();



  //   if ( $this->is_past ) {

  //     $this->args['meta_query'] = [
  //       'relation' => 'OR',
  //       [
  //         'key'     => $this->keys['end_timestamp'],
  //         'value'   => $this->now_ts,
  //         'compare' => '<=',
  //         'type'    => 'NUMERIC',
  //       ]
  //     ];

  //   } else {

  //     // All upcoming start timestamps starting after now
  //     $this->args['meta_query'] = [
  //       'relation' => 'OR',
  //       [
  //         'key'     => $this->keys['start_timestamp'],
  //         'value'   => $this->now_ts,
  //         'compare' => '>=',
  //         'type'    => 'NUMERIC',
  //       ]
  //     ];

  //     $this->args['meta_query']['relation'] = 'OR';

  //     // All events starting before or ending after now
  //     $this->args['meta_query'][] = [
  //       'relation' => 'AND',
  //       [
  //         'key' => $this->keys['start_timestamp'],
  //         'value' => $this->now_ts,
  //         'compare' => '<=',
  //         'type' => 'NUMERIC'
  //       ],
  //       [
  //         'key' => $this->keys['end_timestamp'],
  //         'value' => $this->now_ts,
  //         'compare' => '>=',
  //         'type' => 'NUMERIC'
  //       ]
  //     ];
  //   }
  // }



  /**
   * Set month view
   *
   * @return void
   */
  private function set_month_view() {
    if ( $this->view_value ) {
      // Month of value as Y-m
      $date = \DateTime::createFromFormat('Y-m', $this->view_value, $this->timezone);

      // Bail on malformed input before using $date (e.g. ?month-of=garbage).
      if ( !$date ) { return; }

      $now_ym  = $this->now_date->format('Ym');
      $date_ym = $date->format('Ym');
      $this->is_past = $date_ym < $now_ym;

    } else {
      // Get current month
      $date = clone $this->now_date;
    }

    $this->set_unlimited_query_defaults();

    $date->modify('first day of this month');
    $date->setTime(0, 0, 0);

    $end = clone $date;
    $end->modify('last day of this month');
    $end->setTime(23, 59, 59);
    $next  = clone $date;
    $next->modify('+1 month');
    $prev  = clone $date;
    $prev->modify('-1 month');

    $this->args['bc_events_query']       = 'range';
    $this->args['bc_events_range_start'] = (int) $date->format('U');
    $this->args['bc_events_range_end']   = (int) $end->format('U');

    $this->set_view_date_decorators('month-of', $date, $prev, $next, 'Y-m');

    $now_month_start = clone $this->now_date;
    $now_month_start->modify('first day of this month');
    $now_month_start->setTime(0, 0, 0);

    // $this->is_past = $now_month_start->format('Ym') < $date->format('U');

    $title = Hooks::hook_filter_month_of_view_title($date);
    $this->set_view_title_decorator($title);
    $this->set_view_decorator('month');
    $this->set_view_status_decorator();
    $this->args['posts_per_page'] = -1;
  }



  /**
   * Set week view args
   *
   * @return void
   */
  private function set_week_view() {

    $wp_week_starts_on = $this->get_week_start_day();

    if ( $this->view_value ) {
      // Week of value as Y-m-d
      $date = \DateTime::createFromFormat('Y-m-d', $this->view_value, $this->timezone);

    } else {
      // Get current week
      $date = clone $this->now_date;
      $day  = $date->format('l');

      if ( $day !== $wp_week_starts_on['text'] ) {
        $date->modify('last ' . $wp_week_starts_on['text']);
      }
    }

    if ( !$date ) { return false; }

    $this->set_unlimited_query_defaults();

    $date->setTime(0, 0, 0);
    $end = clone $date;
    $end->add(new \DateInterval('P6D'));
    $end->setTime(23, 59, 59);
    $next  = clone $date;
    $next->modify('+1 week');
    $prev  = clone $date;
    $prev->modify('-1 week');

    $this->args['bc_events_query']       = 'range';
    $this->args['bc_events_range_start'] = (int) $date->format('U');
    $this->args['bc_events_range_end']   = (int) $end->format('U');
    $this->set_view_date_decorators('week-of', $date, $prev, $next);

    $now_week_start = clone $this->now_date;
    $now_week_start->modify('last ' . $wp_week_starts_on['text']);
    $now_week_start->setTime(0, 0, 0);

    $this->is_past = $now_week_start->format('U') > $date->format('U');

    $title = Hooks::hook_filter_week_of_view_title($date);

    $this->set_view_title_decorator($title);
    $this->set_view_decorator('week');
    $this->set_view_status_decorator();

  }



  /**
   * Get first day of the week value from WP
   *
   * @return array
   */
  private function get_week_start_day() : array {
    $day  = \get_option('start_of_week');
    $day  = $day ? $day : 0;
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    return [
      'num' => $day,
      'text' => $days[$day],
    ];
  }



  /**
   * Set Day View Args
   *
   * @return void
   */
  private function set_day_view() : void {
    if ( $this->view_value ) {
      // Day of value as Y-m-d
      $date = \DateTime::createFromFormat('Y-m-d', $this->view_value, $this->timezone);

    } else {
      // Get current day
      $date = clone $this->now_date;
    }

    if ( !$date ) { 
      return; 
    }

    $this->set_unlimited_query_defaults();

    $date->setTime(0, 0, 0);
    $end  = clone $date;
    $end->setTime(23, 59, 59);
    $next = clone $date;
    $next->modify('+1 day');
    $prev = clone $date;
    $prev->modify('-1 day');


    $this->args['bc_events_query']       = 'range';
    $this->args['bc_events_range_start'] = (int) $date->format('U');
    $this->args['bc_events_range_end']   = (int) $end->format('U');
    $this->set_view_date_decorators('day-of', $date, $prev, $next);

    $now_day_start = clone $this->now_date;
    $now_day_start->setTime(0, 0, 0);

    $this->is_past = $now_day_start->format('U') > $date->format('U');

    $title = Hooks::hook_filter_day_view_title($date);
    $this->set_view_title_decorator($title);
    $this->set_view_decorator('day');
    $this->set_view_status_decorator();

  }



  /**
   * Set taxonomy param args
   *
   */
  private function set_taxonomy_params_to_args() : void {

    $taxonomies = Hooks::hook_filter_filter_taxonomies();
    $field      = Hooks::hook_filter_taxonomy_query_field();

    if ( empty($taxonomies) || !is_array($taxonomies) || !is_string($field) ) {
      return;
    }

    $tax_query = [];

    foreach( $taxonomies as $taxonomy => $settings ) {

      if ( !$settings || !is_array($settings) || !isset($settings['parameter']) ) {
        return;
      }

      if ( !isset($_GET[$settings['parameter']]) ) {
        continue;
      }

      // Guard against array input (e.g. ?param[]=x) which would make explode() throw.
      if ( !is_string($_GET[$settings['parameter']]) ) {
        continue;
      }

      $terms       = explode(',', wp_unslash($_GET[$settings['parameter']]));
      $clean_terms = [];

      foreach( $terms as $term ) {
        if ( $field === 'term_id') {
          $clean_terms[] = intval($term);
        } else {
          $clean_terms[] = sanitize_text_field($term);
        }
      }

      $tax_query[] = [
        'taxonomy' => $taxonomy,
        'terms'    => $clean_terms,
        'field'    => $field,
      ];
    }

    if ( !empty($tax_query) ) {
      $this->query_decorators['bce_has_taxonomy_params'] = true;
      $this->args['tax_query'] = $tax_query;
    }
  }



  /**
   * Set start/end meta query
   *
   */
  // private function set_start_end_meta_query($start_timestamp, $end_timestamp) {
  //   if ( !isset($this->args['meta_query']) ) {
  //     $this->args['meta_query'] = [];
  //   }

  //   $this->args['meta_query'][] = [
  //     'key' => $this->keys['start_timestamp'],
  //     'value' => $start_timestamp,
  //     'compare' => '>=',
  //     'type' => 'NUMERIC'
  //   ];

  //   $this->args['meta_query'][] = [
  //     'key' => $this->keys['end_timestamp'],
  //     'value' => $end_timestamp,
  //     'compare' => '<=',
  //     'type' => 'NUMERIC'
  //   ];

  // }




  /**
   * Set view name
   *
   * @param string $value
   * @return void
   */
  private function set_view_decorator(string $value) : void {
    $this->query_decorators['bce_view'] = $value;
  }



  /**
   * Set view status
   *
   * @return void
   */
  private function set_view_status_decorator() : void {
    $value = $this->is_past ? 'past' : 'upcoming';
    $this->query_decorators['bce_status'] = $value;
  }


  /**
   * Set title
   *
   * @param string $title
   * @return void
   */
  private function set_view_title_decorator(string $title) : void {
    $this->query_decorators['bce_view_title'] = $title;
  }


  /**
   * Set default queries for views with unlimited resutls
   *
   * @return void
   */
  private function set_unlimited_query_defaults() : void {
    $this->args['posts_per_page'] = 100;
    $this->args['order']          = 'ASC';
  }


  private function set_view_date_decorators(string $param, \DateTime $current, \DateTime $prev, \DateTime $next, string $format = 'Y-m-d') : void {
    $this->query_decorators['bce_view_wrapping_dates'] = [
      'pagination_param' => $param,
      'current_string'   => $current->format($format),
      'current_date'     => $current,
      'next_string'      => $next->format($format),
      'next_date'        => $next,
      'prev_string'      => $prev->format($format),
      'prev_date'        => $prev,
    ];
  }

}