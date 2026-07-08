<?php

namespace BluecadetEvents\Admin\Views;
use BluecadetEvents\Admin\Utils\AbstractService;
use BluecadetEvents\Plugin;
use BluecadetEvents\Admin\Admin_Utils;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;
use BluecadetEvents\Helpers\TemplateHelpers;

/**
 * Create Custom Post Types
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class AdminEventsViews extends AbstractService {

  private string $filter_events_param              = 'bc_query_view';
  private string $event_date_filter_param          = 'bc_events_date';
  private string $recurring_children_of_param      = 'recurring-by-id';
  private string $filter_events_default_key        = 'default';
  private string $filter_events_all_events_key     = 'show_all_events';
  private string $filter_events_parent_only_key    = 'parent_only';
  private string $filter_events_no_alterations_key = 'no_filters';
  private array  $keys;

  public function register() : void {
    $this->keys = EventsMetaKeys::get_keys();
  }
    
  public function boot() : void {
    // Add post state to admin list
    add_filter( 'display_post_states', [$this, 'post_states'], 10, 2 );

    // Order events by start date by default, and add sorting for start date column
    add_action( 'pre_get_posts', [$this, 'admin_queries'], 100 );

    // Display Column Data
    add_filter( 'manage_' . Settings::$events_machine_name . '_posts_columns', [$this, 'columns_display'] );
    add_action( 'manage_' . Settings::$events_machine_name . '_posts_custom_column', [$this, 'columns_content'], 10, 2);
    add_filter( 'manage_edit-' . Settings::$events_machine_name . '_sortable_columns', [$this, 'sortable_columns']);

    // Add filters
    add_action( 'restrict_manage_posts', [$this, 'table_filtering'], 10, 1 );

    // Remove default WP date filter on events post type
    add_action( 'admin_head', [$this, 'remove_core_dates_filter'] );

    add_filter( 'post_row_actions', [$this, 'remove_cpt_trash_action'], 99, 2 );

  }



  /**
   * Custom post states for recurring events
   *
   * @param array $post_states
   * @param \WP_Post $post
   * @return array
   * @since 1.0.0
   */
  public function post_states( array $post_states, \WP_Post $post ) : array {

    if ( $post->post_type !== 'bc-events') {
      return $post_states;
    }

    $db_helpers           = DatabaseHelpers::get_instance();
    $is_recurring_child  = $db_helpers->is_recurring_child($post->ID);
    $is_recurring_parent = $db_helpers->is_recurring_parent($post->ID);

    if ( $is_recurring_child ) {
      $post_states['bc_events_recurring_child'] = 'Recurring Child';
      if ( get_post_meta($post->ID, $this->keys['child_deny_override'], true) ) {
        $post_states['bc_events_recurring_child_override'] = '<span style="color: #ff0f0f; font-style: italic;">Custom Content</span>';
      }

    } elseif ( $is_recurring_parent ) {
      $freq = get_post_meta($post->ID, $this->keys['freq'], true);

      if ( $freq ) {
        $state = '';
        switch ($freq) {
          case 'consecutive':
            $state = ' (Consecutive)';
            break;
          case 'weekly':
            $state = ' (Weekly)';
            break;
          case 'daily':
            $state = ' (Daily)';
            break;
        }
        $post_states['_bc_events_recurring_parent'] = 'Recurring Parent' . $state;
      } else {
        $post_states['_bc_events_recurring_parent'] = 'Recurring Parent';
      }
    }

    return $post_states;

  }



  /**
   * Customize events admin list columns
   *
   * @param array $columns
   * @return array
   * @since 1.0.0
   */
  public function columns_display( array $columns ) : array {

    $new_columns = array(
      'cb'         => $columns['cb'],
      'title'      => __( 'Title' ),
      'event_date' => __( 'Event Date', 'bc_events' ),
      'recurring'  => __( 'Recurring', 'bc_events' ),
    );

    $columns = array_merge($new_columns, $columns);

    return $columns;
  }



  /**
   * Provide content to custom columns
   *
   * @param string $column
   * @param int $post_id
   * @return void
   * @since 1.0.0
   */
  public function columns_content( string $column, int $post_id ) : void {

    $db_helpers          = DatabaseHelpers::get_instance();
    $is_recurring_child  = $db_helpers->is_recurring_child($post_id);
    $is_recurring_parent = $db_helpers->is_recurring_parent($post_id);

    if ( 'event_date' === $column ) {

      $formatter  = TemplateHelpers::getInstance();
      $start_date = $formatter->get_formatted_start_date($post_id);
      $end_date   = $formatter->get_formatted_end_date($post_id);

      if ( $is_recurring_parent ) {

        $freq = get_post_meta($post_id, $this->keys['freq'], true);

        if ($freq === 'consecutive') {
          echo 'First Start: ' . $formatter->get_formatted_start_date($post_id) . ' @ ' . $formatter->get_formatted_start_time($post_id);
        } else {
          echo 'Starts: ' . $start_date;
        }

        if ($freq === 'consecutive') {
          $last = $db_helpers->get_last_child_event_end($post_id);
          if ( $last ) {
            echo '<br>Last End: ' . $formatter->date_from_timestamp($last) . ' @ ' . $formatter->time_from_timestamp($last);
          }
        } else {
          $last = $db_helpers->get_last_child_event($post_id);
          if ( $last ) {
            echo '<br>Ends: ' . $formatter->date_from_timestamp($last);
          }
        }
        
      } elseif ( $start_date === $end_date ) {
        echo $start_date;
        
      } else {
        echo $start_date . ' - ' . $end_date;
      }

    } elseif ( 'recurring' === $column ) {

      $is_trash = isset($_GET['post_status']) && $_GET['post_status'] === 'trash';

      if ( $is_recurring_child ) {

        if ( isset($_GET[$this->recurring_children_of_param]) && !$is_trash) {
          if ($is_recurring_child) {
            echo '<a href="' . get_edit_post_link($is_recurring_child) . '">Edit Recurring Parent</a>';
          }
          echo '<br /><a href="' . admin_url('edit.php?post_type=' . Settings::$events_machine_name) . '">Back to Events</a>';
        }

      } elseif ( $is_recurring_parent ) {
        if ( !$is_trash ) {
          $view_url = 'edit.php?post_type=' . Settings::$events_machine_name . '&recurring-by-id=' . $post_id;
          echo '<a href="' . admin_url($view_url) . '">' . 'View Recurring Events' . '</a>';
        }
      }

    }
  }



  /**
   * Add sorting to custom columns
   *
   * @param array $columns
   * @return array
   * @since 1.0.0
   */
  public function sortable_columns( array $columns ) : array {
    $columns['event_date'] = 'bc_event_date';
    return $columns;
  }



  /**
   * Customize default query order for events
   * - Sort by start date by default
   * - Add sortable column queries
   *
   * @param \WP_Query $query
   * @return \WP_Query
   * @since 1.0.0
   */
  public function admin_queries( \WP_Query $query ) : \WP_Query {

    if ( !is_admin() || !$query->is_main_query() || $query->query['post_type'] !== Settings::$events_machine_name ) {
      return $query;
    }

    // Do not add any other filters if user doesnt want them
    if ( isset($_GET[$this->filter_events_param]) && $_GET[$this->filter_events_param] === $this->filter_events_no_alterations_key ) {
      return $query;
    }

    // If is trash view, do not show filters
    if ( isset($_GET['post_status']) && $_GET['post_status'] === 'trash' ) {
      return $query;
    }

    // Always override the default query values
    $query->set( 'orderby', 'meta_value_num title' );
    $query->set( 'meta_key', 'bc_events_start_timestamp' );
    $queries_applied = false;

    $meta_query = $query->get('meta_query');
    $meta_query = $meta_query && is_array($meta_query) ? $meta_query : [];


    // Handle showing recurring children for specific post
    if ( isset($_GET[$this->recurring_children_of_param])) {
      $parent_id = (int)$_GET[$this->recurring_children_of_param];
      $child_ids = DatabaseHelpers::get_instance()->get_recurring_child_ids($parent_id);
      $query->set( 'post__in', $child_ids );
      return $query;
    }


    // Handle Filter Actions
    if ( isset($_GET['filter_action']) ) {

      // Filter Showing/Hiding Recurring Children
      if ( isset($_GET[$this->filter_events_param] ) ) {

        // Only show parent events
        if ( $_GET[$this->filter_events_param] === $this->filter_events_parent_only_key ) {

          $meta_query[] = [
            [
              'key' => $this->keys['is_parent'],
              'value' => '1',
              'compare' => '='
            ]
          ];

          $queries_applied = true;

        }

        if ( $_GET[$this->filter_events_param] === $this->filter_events_all_events_key ) {
          $meta_query[] = [
            'relation' => 'OR',
            [
              'key' => $this->keys['is_parent'],
              'value' => '1',
              'compare' => '='
            ],
            [
              'key' => $this->keys['is_child'],
              'value' => '1',
              'compare' => '='
            ]
          ];
          $queries_applied = true;
        }

        // Only show Primary events
        if ( $_GET[$this->filter_events_param] === 'default' || !isset($_GET[$this->filter_events_param]) ) {
          $queries_applied = false;
        }
      }

      // Filter Showing by Month/Year
      if ( isset($_GET[$this->event_date_filter_param]) ) {

        $date_val = $_GET[$this->event_date_filter_param];

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
          'key' => $this->keys['is_child'],
          'compare' => 'NOT EXISTS'
        ],
        [
          'key' => $this->keys['is_child'],
          'compare' => '!=',
          'value' => '1'
        ]
      ];
    }

    if ( !empty($meta_query) ) {
      $query->set( 'meta_query', $meta_query );
    }

    return $query;

  }




  // /**
  //  * Add filters
  //  *
  //  * @param [type] $post_type
  //  * @return void
  //  */
  public function table_filtering($post_type) : void {

    // If is trash view, do not show filters
    if ( isset($_GET['post_status']) && $_GET['post_status'] === 'trash' ) {
      return;
    }


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
  private function filter_month_year() : void {
    global $wpdb;

    $date_selected = isset($_REQUEST[$this->event_date_filter_param]) ? $_REQUEST[$this->event_date_filter_param] : '';

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
        if ( $v == '' ) {
          continue;
        }
        $d_opts[$v] = $v;
      }

      uksort($d_opts, function($a1, $a2) {
        $time1 = strtotime($a1);
        $time2 = strtotime($a2);

        return $time2 - $time1;
      });

      $final_opts = array_merge(['all' => 'All event dates'], $d_opts);

      $this->create_select('bc-events-date-opts', $this->event_date_filter_param, $final_opts, $date_selected);
    }
  }



  /**
   * Create a filter for event view queries
   *
   * @return void
   */
  private function filter_query_view() : void {

      $query_view_selected = isset($_REQUEST[$this->filter_events_param]) ? $_REQUEST[$this->filter_events_param] : '';

      // Custom Event Recur Filter
      $query_view_opts = [
        $this->filter_events_default_key => 'Primary Events',
        $this->filter_events_all_events_key => 'Primary and Child Events',
        $this->filter_events_parent_only_key => 'Recurring Parent Events Only',
        $this->filter_events_no_alterations_key => 'Remove All Events Filters',
      ];

      $this->create_select('bc-query-view', $this->filter_events_param, $query_view_opts, $query_view_selected);
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
  private function create_select(string|int $id, string $name, array $opts, string $selected_val) : void {
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
  public function remove_core_dates_filter() : void {
    $screen = get_current_screen();

    if ( Settings::$events_machine_name == $screen->post_type ){
      add_filter('months_dropdown_results', '__return_empty_array');
    }
  }


  public function remove_cpt_trash_action( array $actions, \WP_Post $post ) : array {
    if ( $post->post_type !== Settings::$events_machine_name ) {
      return $actions;
    }

    $db_helpers          = DatabaseHelpers::get_instance();
    $is_recurring_child  = $db_helpers->is_recurring_child($post->ID);
    $is_trash            = isset($_GET['post_status']) && $_GET['post_status'] === 'trash';

    if ( $is_trash && $is_recurring_child ) {
      $new_actions = [];
    } elseif ( $is_recurring_child ) {
      $new_actions = [
        'edit' => $actions['edit'],
        'view' => $actions['view']
      ];
    } else {
      $new_actions = $actions;
    }
    

    return $new_actions;
  }

}
