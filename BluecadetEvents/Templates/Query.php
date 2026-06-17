<?php

namespace BluecadetEvents\Templates;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Plugin\Settings;

class Queries {

  private array $settings;
  private array $taxonomies = [];
  private QuerySetters $query_setters;

  public function __construct() {

    add_action( 'pre_get_posts', [$this, 'handle_query'] );

    add_filter( 'posts_join', [$this, 'events_posts_join'], 10, 2 );

    add_filter( 'posts_where', [$this, 'events_posts_where'], 10, 2 );
  }


  /**
   * Set Query for Events Pages and Taxonomied
   *
   * @param \WP_Query $query
   * @return void
   */
  public function handle_query(\WP_Query $query) : void {

    if ( !$query->is_main_query() || is_admin() ) {
      return;
    }

    if ( is_post_type_archive( Settings::$events_machine_name ) ) {
      $this->handle_archive_query($query);
    } else {
      $this->taxonomies = Hooks::hook_apply_taxonomy_term_pages();
      
      if ( !empty($this->taxonomies) ) {
        foreach ( $this->taxonomies as $taxonomy ) {
          if ( is_tax($taxonomy) ) {
            $this->query_setters = new QuerySetters($query);
            $this->query_setters->set_query('list');
          }
        }
      }
    }
  }



  /**
   * Determine archive queries to set
   *
   * @param \WP_Query $query
   * @return void
   */
  private function handle_archive_query(\WP_Query $query) : void {

    $this->query_setters = new QuerySetters($query);
    $this->settings  = Hooks::hook_filter_archive_settings();

    if ( isset($_GET['day-of']) ) {
      $day_of = \sanitize_text_field($_GET['day-of']);
      $this->query_setters->set_query('day_of', $day_of);

    } elseif ( isset($_GET['week-of']) ) {
      $week_of = \sanitize_text_field($_GET['week-of']);
      $this->query_setters->set_query('week_of', $week_of);

    } elseif ( isset($_GET['month-of']) ) {
      $month_of = \sanitize_text_field($_GET['month-of']);
      $this->query_setters->set_query('month_of', $month_of);

    } else {
      switch ($this->settings['layout']) {
        case 'week':
          $this->query_setters->set_query('week');
          break;

        // case 'day-list':
        //   $this->query_setters->set_query('day-list');
        //   break;

        default:
          $this->query_setters->set_query('list');
          break;
      }
    }
  }



  public function events_posts_join(string $join, \WP_Query $query) {
    global $wpdb;


    if ( $bce_order = $query->get( 'bce_events_order' ) ) {
      $bc_events_table = $wpdb->prefix . 'bce_events';

      if ( $bce_order === 'upcoming' ) {
        $join .= "LEFT JOIN {$bc_events_table} ON {$wpdb->posts}.ID = {$bc_events_table}.post_id ";
      }
    }

    return $join;
  }



  public function events_posts_where(string $where, \WP_Query $query) {
    global $wpdb;

    if ( $bce_order = $query->get( 'bce_events_order' ) ) {
      if ( $bce_order === 'upcoming' ) {
        if ( $query->get('bce_events_honor_timestamp') ) {
          $ts = $query->get('bce_events_honor_timestamp');
        } else {
          $tz  = \wp_timezone();
          $now  = new \DateTime('now', $tz);
          $ts = $now->getTimestamp();
        }

        $table = $wpdb->prefix . 'bce_events';
        $where .= " AND ( {$table}.event_start_date >= {$ts} OR ( {$table}.event_start_date <= {$ts} AND {$table}.event_end_date >= {$ts} ) )";
      }
    }

    return $where;
  }

}