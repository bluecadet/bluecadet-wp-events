<?php

namespace BluecadetEvents\Templates;

use BluecadetEvents\Admin\Utils\AbstractService;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Plugin\Settings;

class Query extends AbstractService {

  private array $settings;
  private array $taxonomies = [];
  private QuerySetters $query_setters;

  public function boot() : void {
    add_action( 'pre_get_posts', [$this, 'handle_query'] );
    add_filter( 'posts_clauses', [$this, 'events_posts_clauses'], 10, 2 );
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
      $day_of = \sanitize_text_field(\wp_unslash($_GET['day-of']));
      $this->query_setters->set_query('day_of', $day_of);

    } elseif ( isset($_GET['week-of']) ) {
      $week_of = \sanitize_text_field(\wp_unslash($_GET['week-of']));
      $this->query_setters->set_query('week_of', $week_of);

    } elseif ( isset($_GET['month-of']) ) {
      $month_of = \sanitize_text_field(\wp_unslash($_GET['month-of']));
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



  /**
   * Modify query clauses to join bc_events_table for date filtering and ordering.
   *
   * Triggered by the bc_events_query query var. Supports:
   *   - bc_events_query: 'upcoming' | 'past' | 'range'
   *   - bc_events_range_start: Unix timestamp (range mode)
   *   - bc_events_range_end: Unix timestamp (range mode)
   *   - bc_events_timestamp: Unix timestamp override for 'now' (optional)
   *   - bc_events_dedupe: true — collapse each series to its earliest occurrence
   *
   * Series masters (is_parent = 1) are never returned: they are single-page
   * canonical anchors that list their own occurrences, so every listing shows
   * children and standalone events only.
   *
   * @param array     $clauses
   * @param \WP_Query $query
   * @return array
   */
  public function events_posts_clauses(array $clauses, \WP_Query $query) : array {
    global $wpdb;

    $bc_query = $query->get('bc_events_query');
    if ( !$bc_query ) {
      return $clauses;
    }

    $bc_events_table = $wpdb->prefix . Settings::$events_table;

    $order_raw = strtoupper( $query->get('order') );
    $order     = in_array( $order_raw, ['ASC', 'DESC'] ) ? $order_raw : 'ASC';

    $dedupe = (bool) $query->get('bc_events_dedupe');
    if ( !$dedupe && $query->is_main_query() ) {
      $settings = Hooks::hook_filter_archive_settings();
      $dedupe   = !empty( $settings['dedupe_main_query'] );
    }

    $timestamp = (int) $query->get('bc_events_timestamp');
    if ( !$timestamp ) {
      $timestamp = ( new \DateTime('now', \wp_timezone()) )->getTimestamp();
    }

    $date_condition = '';
    switch ( $bc_query ) {
      case 'upcoming':
        $date_condition = $wpdb->prepare(
          "(t.event_start >= %d OR (t.event_start <= %d AND t.event_end >= %d))",
          $timestamp, $timestamp, $timestamp
        );
        break;

      case 'past':
        $date_condition = $wpdb->prepare( "t.event_end <= %d", $timestamp );
        break;

      case 'range':
        $range_start = (int) $query->get('bc_events_range_start');
        $range_end   = (int) $query->get('bc_events_range_end');
        if ( $range_start && $range_end ) {
          $date_condition = $wpdb->prepare(
            "t.event_start <= %d AND t.event_end >= %d",
            $range_end, $range_start
          );
        } elseif ( $range_start ) {
          $date_condition = $wpdb->prepare( "t.event_start >= %d", $range_start );
        } elseif ( $range_end ) {
          $date_condition = $wpdb->prepare( "t.event_start <= %d", $range_end );
        }
        break;
    }

    if ( $dedupe ) {
      // One row per series (a standalone event is its own series): the earliest
      // occurrence in range — the latest one for past views, where the most
      // recent occurrence is the relevant one. The inner query picks the winning
      // start per series, the outer join resolves it back to a post id (MIN() as
      // a tiebreak so the result stays deterministic if two ever share a start).
      $pick           = ( $bc_query === 'past' ) ? 'MAX' : 'MIN';
      $subquery_where = $date_condition ? "AND {$date_condition}" : '';

      $clauses['join'] .= " INNER JOIN (
        SELECT g.event_start, MIN(o.post_id) AS post_id
        FROM (
          SELECT
            CASE WHEN t.parent_ID != 0 THEN t.parent_ID ELSE t.post_id END AS series_id,
            {$pick}(t.event_start) AS event_start
          FROM {$bc_events_table} t
          WHERE t.is_parent = 0 {$subquery_where}
          GROUP BY series_id
        ) AS g
        INNER JOIN {$bc_events_table} o
          ON ( CASE WHEN o.parent_ID != 0 THEN o.parent_ID ELSE o.post_id END ) = g.series_id
          AND o.event_start = g.event_start
          AND o.is_parent = 0
        GROUP BY g.series_id, g.event_start
      ) AS bc_events ON {$wpdb->posts}.ID = bc_events.post_id";
      $clauses['orderby'] = "bc_events.event_start {$order}";
    } else {
      $clauses['join']  .= " INNER JOIN {$bc_events_table} AS t ON {$wpdb->posts}.ID = t.post_id";
      $clauses['where'] .= " AND t.is_parent = 0";
      if ( $date_condition ) {
        $clauses['where'] .= " AND {$date_condition}";
      }
      $clauses['orderby'] = "t.event_start {$order}";
    }

    return $clauses;
  }

}
