<?php

namespace BluecadetEvents\Admin\Views;
use BluecadetEvents\Plugin;
use BluecadetEvents\Admin\Admin_Utils;
use BluecadetEvents\Plugin\Settings;

/**
 * Create Custom Post Types
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class AdminEventsViews {

  private $query_view;
  private $event_date_filter_key;
  private $recur_by_id_key;

  function __construct() {

    $this->query_view = 'bc_query_view';
    $this->event_date_filter_key = 'bc_events_date';
    $this->recur_by_id_key = 'recurring-by-id';

    // Order events by start date by default, and add sorting for start date column
    add_action( 'pre_get_posts', [$this, 'admin_queries'], 100 );

    add_filter( 'manage_' . Settings::$events_machine_name . '_posts_columns', [$this, 'columns_display'] );
    add_action( 'manage_' . Settings::$events_machine_name . '_posts_custom_column',   [$this, 'columns_content'], 10, 2);
    add_filter( 'manage_edit-' . Settings::$events_machine_name . '_sortable_columns', [$this, 'sortable_columns']);

    add_action( 'restrict_manage_posts', [$this, 'table_filtering'], 10, 1 );

    add_action( 'admin_head', [$this, 'remove_core_dates_filter'] );

    // add_filter( 'display_post_states',                    [$this, 'post_states'], 10, 2 );
    // add_filter( 'admin_body_class',                       [$this, 'add_admin_classes'] );

  }



  /**
   * Custom post states for recurring events
   *
   * @param array $post_states
   * @param object $post
   * @return array
   * @since 1.0.0
   */
  // function post_states( $post_states, $post ) {

  //   if ( $post->post_type !== 'bc-events') {
  //     return $post_states;
  //   }

  //   $db_helpers           = Admin_Utils\DatabaseHelpers::get_instance();
  //   $is_recurring_child  = $db_helpers->is_recurring_child($post->ID);
  //   $is_recurring_parent = $db_helpers->is_recurring_parent($post->ID);

  //   if ( $is_recurring_child ) {
  //     $post_states['bc_events_recurring_child'] = 'Recurring Child';
  //   } elseif ( $is_recurring_parent ) {
  //     $post_states['_bc_events_recurring_parent'] = 'Recurring Parent';
  //   }

  //   return $post_states;

  // }



  /**
   * Customize events admin list columns
   *
   * @param array $columns
   * @return array
   * @since 1.0.0
   */
  function columns_display( $columns ) {

    $new_columns = array(
      'cb'         => $columns['cb'],
      'title'      => __( 'Title' ),
      'event_date' => __( 'Event Date', 'bc_events' ),
      // 'recurring'  => __( 'Recurring', 'bc_events' ),
    );

    $columns = array_merge($new_columns, $columns);

    return $columns;
  }



  /**
   * Provide content to custom columns
   *
   * @param array $column
   * @param int $post_id
   * @return void
   * @since 1.0.0
   */
  function columns_content( $column, $post_id ) {

    // $db_helpers           = Admin_Utils\DatabaseHelpers::get_instance();
    // $is_recurring_child  = $db_helpers->is_recurring_child($post_id);
    // $is_recurring_parent = $db_helpers->is_recurring_parent($post_id);

    if ( 'event_date' === $column ) {

      $start_date = \bce__get_start_date($post_id);
      $end_date   = \bce__get_start_date($post_id);

      if ( $start_date === $end_date ) {
        echo $start_date;
      } else {
        echo $start_date . ' - ' . $end_date;
      }

    }
    // elseif ( 'recurring' === $column ) {
    //   $is_trash = isset($_GET['post_status']) && $_GET['post_status'] === 'trash';

    //   if ( $is_recurring_child ) {

    //     if ( !$is_trash && isset($is_recurring_child[0]) ) {
    //       echo '<a href="' . get_edit_post_link($is_recurring_child[0]) . '">Edit Recurring Parent</a>';
    //     }

    //     if ( isset($_GET[$this->recur_by_id_key])) {
    //       if ( $is_trash ) {
    //         echo '<br /><a href="' . admin_url('edit.php?post_type=bc-events&post_status=trash') . '">Back to Events (in trash)</a>';
    //       } else {
    //         echo '<br /><a href="' . admin_url('edit.php?post_type=bc-events') . '">Back to Events</a>';
    //       }
    //     }

    //   } elseif ( $is_recurring_parent ) {
    //     $view_url = 'edit.php?post_type=bc-events&recurring-by-id=' . $post_id;
    //     $view_title = 'View Recurring Events';

    //     if ( $is_trash ) {
    //       $view_url = $view_url . '&post_status=' . $_GET['post_status'];
    //       $view_title = $view_title . ' (in trash)';
    //     }

    //     echo '<a href="' . admin_url($view_url) . '">' . $view_title . '</a>';
    //   }

    // }
  }



  /**
   * Add sorting to custom columns
   *
   * @param array $columns
   * @return void
   * @since 1.0.0
   */
  function sortable_columns( $columns ) {
    $columns['event_date'] = 'bc_event_date';
    // $columns['last_modified'] = 'bc_last_modified';
    return $columns;
  }



  /**
   * Customize default query order for events
   * - Sort by start date by default
   * - Add sortable column queries
   *
   * @param object $query
   * @return object
   * @since 1.0.0
   */
  function admin_queries( $query ) {

    if ( !is_admin() || !$query->is_main_query() || $query->query['post_type'] !== Settings::$events_machine_name ) {
      return;
    }

    // Do not add any other filters if user doesnt want them
    if ( isset($_GET[$this->query_view]) && $_GET[$this->query_view] === 'show_all_no_filter' ) {
      return $query;
    }

    // Always override the default query values
    $query->set( 'orderby', 'meta_value_num title' );
    $query->set( 'meta_key', 'bc_events_start_timestamp' );
    $queries_applied = false;

    $meta_query = $query->get('meta_query');
    $meta_query = $meta_query && is_array($meta_query) ? $meta_query : [];


    // Handle showing recurring children for specific post
    if ( isset($_GET[$this->recur_by_id_key])) {
      $parent_id = $_GET[$this->recur_by_id_key];
      $meta_query[] = [
        [
          'key' => '_bc_events_recurring_parent',
          'value' => $parent_id
        ],
      ];

      $query->set( 'meta_query', $meta_query);

      return $query;
    }

    // Storage for meta_query arrays



    // Handle Filter Actions
    if ( isset($_GET['filter_action']) ) {

      // Filter Showing/Hiding Recurring Children
      if ( isset($_GET[$this->query_view] ) ) {

        // Only show parent events
        if ( $_GET[$this->query_view] === 'parent_only' ) {

          $meta_query[] = [
            'relation' => 'OR',
            [
              [
                'key' => '_bc_events_recurring_has_children',
                'value' => '',
                'compare' => '!='
              ],
              [
                'key' => 'bc_events_recurring',
                'value' => 'on',
                'compare' => '='
              ]
            ]
          ];

          $queries_applied = true;

        }

        if ( $_GET[$this->query_view] === 'show_all' ) {
          $queries_applied = true;
        }

        // Only show Primary events
        if ( $_GET[$this->query_view] === 'default' ) {
          $queries_applied = false;
        }
      }

      // Filter Showing by Month/Year
      if ( isset($_GET[$this->event_date_filter_key]) ) {

        $date_val = $_GET[$this->event_date_filter_key];

        if ( $date_val !== 'all' ) {
          $meta_query[] = [
            [
              'key' => 'bc_events_start_month_year',
              'value' => $date_val,
              'compare' => '='
            ],
          ];

          $queries_applied = true;
        }
      }
    }


    // Handle column sorting
    if (isset($_GET['orderby']) ) {
      $orderby = $_GET['orderby'];

      if ( $orderby === 'bc_last_modified' ) {
        $query->set( 'orderby', 'modified' );
      }
    }

    // Handle default, hide recurring children
    if ( !$queries_applied ) {

      $meta_query[] = [
        'relation' => 'OR',
        [
          'key' => '_bc_events_recurring_parent',
          'compare' => 'NOT EXISTS'
        ],
        [
          'key' => '_bc_events_recurring_parent',
          'value' => '',
          'compare' => '=='
        ],
      ];
    }

    if ( !empty($meta_query) ) {
      $query->set( 'meta_query', $meta_query );
    }

  }




  // /**
  //  * Add filters
  //  *
  //  * @param [type] $post_type
  //  * @return void
  //  */
  function table_filtering($post_type) {


    if ( $post_type !== 'bc-events' ) {
      return;
    }

    $this->filter_month_year();
    $this->filter_query_view();
  }



  /**
   * Create a filter for month/year
   *
   * @return void
   */
  private function filter_month_year() {
    global $wpdb;

    $date_selected = isset($_REQUEST[$this->event_date_filter_key]) ? $_REQUEST[$this->event_date_filter_key] : '';

    // Custom Event Date Filter
    $d = $wpdb->get_results( "
        SELECT DISTINCT(pm.meta_value) FROM $wpdb->postmeta pm
        LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id
        WHERE pm.meta_key = 'bc_events_start_month_year'
        AND p.post_status = 'publish'
        AND p.post_type = 'bc-events'
    " );

    if ($d) {
      foreach($d as $dv) {
        $v = $dv->meta_value;
        $d_opts[$v] = $v;
      }

      uksort($d_opts, function($a1, $a2) {
        $time1 = strtotime($a1);
        $time2 = strtotime($a2);

        return $time2 - $time1;
      });

      $final_opts = array_merge(['all' => 'All event dates'], $d_opts);

      $this->create_select('bc-events-date-opts', $this->event_date_filter_key, $final_opts, $date_selected);
    }
  }



  /**
   * Create a filter for event view queries
   *
   * @return void
   */
  private function filter_query_view() {

      $query_view_selected = isset($_REQUEST[$this->query_view]) ? $_REQUEST[$this->query_view] : '';


      // Custom Event Recur Filter
      $query_view_opts = [
        'default'            => 'Event Date',
        // 'default'            => 'Show All Primary Events',
        // 'show_all'           => 'Show All Events',
        // 'parent_only'        => 'Recurring Parent Events Only',
        'show_all_no_filter' => 'Remove All Events Filters',
      ];

      $this->create_select('bc-query-view', $this->query_view, $query_view_opts, $query_view_selected);
    }



  /**
   * Helper to create a filter select
   *
   * @param string|int $id
   * @param string $name
   * @param array $opts
   * @param string $selected_val
   * @return void
   */
  function create_select($id, $name, $opts, $selected_val) {
    echo '<select id="' . $id . '" name="' . $name . '">';

    foreach($opts as $value => $option){
      $selected = ($value == $selected_val) ? ' selected="selected"':'';
      echo '<option value="' . $value . '"' . $selected . '>' . $option . ' </option>';
    }

    echo '</select>';
  }



  /**
   * Remove the default WordPress date filter
   *
   * @return void
   */
  function remove_core_dates_filter() {
    $screen = get_current_screen();

    if ( Settings::$events_machine_name == $screen->post_type ){
      add_filter('months_dropdown_results', '__return_empty_array');
    }
  }



  /**
   * Add Admin Classes
   *
   * @param string $classes
   * @return string
   */
  // function add_admin_classes( $classes ) {
  //   global $pagenow;
  //   global $post;

  //   if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
  //     $db_helpers           = Admin_Utils\DatabaseHelpers::get_instance();
  //     $is_recurring_child  = $db_helpers->is_recurring_child($post->ID);
  //     $is_recurring_parent = $db_helpers->is_recurring_parent($post->ID);

  //     if ( $is_recurring_child ) {
  //       $classes .= ' bc-events--is-recurring-child';
  //     } elseif ( $is_recurring_parent ) {
  //       $classes .= ' bc-events--is-recurring-parent';
  //     }

  //     if ( $post->post_type === 'bc-events') {
  //       $classes .= ' bc-events-edit-screen';
  //     }
  //   }

  //   return $classes;
  // }


}
