<?php

namespace BluecadetEvents\Admin\Utils;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Save\Recur\Objects\EventClone;
use BluecadetEvents\Admin\Save\Recur\Objects\EventPost;

/**
 * Database helper functions
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class DatabaseHelpers {

  private static ?DatabaseHelpers $_instance = null;
  private string $events_table;
  private string $recurring_table;
  // private $meta_table       = 'bce_event_meta_types';
  private string $now_format       = 'Y-m-d H:i:s';


  private function  __construct() {
    // Locked
    $this->events_table     = Settings::$events_table;
    $this->recurring_table  = Settings::$recurring_events_table;
  }

  private function  __clone() {
    // Locked
  }

  public static function get_instance() : DatabaseHelpers {
    if ( self::$_instance == null ) {
      self::$_instance = new DatabaseHelpers();
    }

    return self::$_instance;
  }


  /**
   * Create DB row with post_id, start & end dates
   *
   * @param int $post_id
   * @param int $start_date unix timestamp
   * @param int $end_date unix timestamp
   * @return int|false
   */
  // public function write_or_update_event(int $post_id, int $start_date, int $end_date, false|string $title = false) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->events_table;

  //   $results = $wpdb->get_results(
  //     $wpdb->prepare(
  //       "SELECT * FROM $table WHERE post_id=%d",
  //       $post_id
  //     )
  //   );

  //   if ( !$results ) {
  //     return $this->write_event($post_id, $start_date, $end_date, $title);
  //   }

  //   return $this->update_event($post_id, $start_date, $end_date, $title);
  // }



  /**
   * Create DB row with post_id, start & end dates
   *
   * @param int $post_id
   * @param int $start_date unix timestamp
   * @param int $end_date unix timestamp
   * @return int|false
   */
  // public function write_event(int $post_id, int $start_date, int $end_date, false|string $title = false) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->events_table;
  //   $timezone = \wp_timezone();
  //   $now = new \DateTime('now', $timezone);
  //   $title = $title ? $title : get_the_title($post_id);

  //   $data = [
  //     'post_id' => $post_id,
  //     'event_start_date' => $start_date,
  //     'event_end_date' => $end_date,
  //     'post_title' => $title,
  //     'modified' => $now->format($this->now_format)
  //   ];

  //   $result = $wpdb->insert($table, $data, ['%d','%d','%d','%s','%s']);

  //   return $result;
  // }


  /**
   * Update DB row with post_id, start & end dates
   *
   * @param int $post_id
   * @param int $start_date unix timestamp
   * @param int $end_date unix timestamp
   * @return int|false
   */
  // public function update_event(int $post_id, int $start_date, int $end_date, false|string $title = false) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->events_table;
  //   $timezone = \wp_timezone();
  //   $now = new \DateTime('now', $timezone);
  //   $title = $title ? $title : get_the_title($post_id);

  //   $data = [
  //     'event_start_date' => $start_date,
  //     'event_end_date' => $end_date,
  //     'post_title' => $title,
  //     'modified' => $now->format($this->now_format),
  //   ];

  //   $where = ['post_id' => $post_id];

  //   $result = $wpdb->update($table, $data, $where, ['%d','%d','%s','%s'], ['%d']);
  //   return $result;
  // }


  /**
   * Delete DB row with parent/child ids
   *
   * @param int $post_id
   * @return int|false
   */
  // public function delete_event(int $post_id) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->events_table;

  //   $where = ['post_id' => $post_id];
  //   $result = $wpdb->delete($table, $where, ['%d']);

  //   return $result;
  // }


  public function insert_event(EventPost $event_post) : int|false {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $data = [
      'modified' => $event_post->modified,
      'post_id' => $event_post->post_id,
      'post_slug' => $event_post->post_slug,
      'event_start' => $event_post->event_start,
      'event_end' => $event_post->event_end,
      'parent_ID' => $event_post->parent_ID,
    ];

    $format = ['%s','%d','%s','%d','%d','%d'];

    if ( $this->event_row_exists($event_post->post_id) ) {
      $where = ['post_id' => $event_post->post_id];
      $result = $wpdb->update($table, $data, $where, $format, ['%d']);
      return $result;
    }

    $result = $wpdb->insert($table, $data, $format);

    return $result;
  }


  function event_row_exists(int $post_id) : bool {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $exists = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT 1 FROM $table WHERE post_id=%d LIMIT 1",
        $post_id
      )
    );

    if ( !$exists ) { return false; }

    return true;
  }


  function delete_event_row(int $post_id) : int|false {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $where = ['post_id' => $post_id];
    $result = $wpdb->delete($table, $where, ['%d']);

    return $result;
  }


  // ===================================
  //             Recurring Events
  // ===================================



  /**
   * Check if event id is in the `parent_ID` column of recurring events table
   *
   * @param int $event_id
   * @return array|false
   */
  public function is_recurring_parent($event_id) : bool|array {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT * FROM $table WHERE parent_ID=%d",
        $event_id
      )
    );

    if ( !$results ) { return false; }

    $eids = [];

    foreach ( $results as $r ) {
      $eids[] = (int)$r->child_ID;
    }

    if ( !empty($eids) ) {
      return $eids;
    }

    return false;

  }


  /**
   * Check if event id is in the `child_ID` column of recurring events table
   *
   * @param int $event_id
   * @return array|false
   */
  public function is_recurring_child($event_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT * FROM $table WHERE child_ID=%d",
        $event_id
      )
    );

    if ( !$results ) { return false; }

    $eids = [];

    foreach ( $results as $r ) {
      $eids[] = (int)$r->parent_ID;
    }

    if ( !empty($eids) ) {
      return $eids;
    }

    return false;

  }


  /**
   * Get results of parent/child db rows
   *
   * @param int $parent_id
   * @return array|false
   */
  public function get_recurring_child_ids($parent_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT child_ID FROM $table WHERE parent_ID=%d",
        $parent_id
      )
    );

    if ( !$results ) { return false; }

    $cids = [];

    foreach ( $results as $r ) {
      $cids[] = (int)$r->child_ID;
    }

    if ( !empty($cids) ) { return $cids; }

    return false;

  }

  /**
   * Create DB row with parent/child ids
   *
   * @param int $parent_id
   * @param int $child_id
   * @return int|false
   */
  public function write_recurring_child($parent_id, $child_id, false|string $date_slug = false, $update_check = false) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;
    $timezone = \wp_timezone();
    $now = new \DateTime('now', $timezone);

    $data = array('parent_ID' => $parent_id, 'child_ID' => $child_id, 'modified' => $now->format($this->now_format));
    $format = array('%d','%d','%s');

    if ( $date_slug ) {
      $data['date_slug'] = $date_slug;
      $format[] = '%s';
    }

    if ( $update_check ) {
      $data['update_check'] = 1;
      $format[] = '%d';
    }

    $result = $wpdb->insert($table, $data, $format);

    return $result;
  }


  /**
   * Update DB row with parent/child ids
   *
   * @param int $parent_id
   * @param int $child_id
   * @return int|false
   */
  public function update_recurring_child($parent_id, $child_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;
    $timezone = \wp_timezone();
    $now = new \DateTime('now', $timezone);

    $data = array('modified' => $now->format($this->now_format));
    $where = array('parent_ID' => $parent_id, 'child_ID' => $child_id);
    $format = array('%s');
    $result = $wpdb->update($table, $data, $where, $format, $format);

    return $result;
  }


  /**
   * Delete DB row with parent/child ids
   *
   * @param int $parent_id
   * @param int $child_id
   * @return int|false
   */
  public function delete_recurring_child($parent_id, $child_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $where = array('parent_ID' => $parent_id, 'child_ID' => $child_id);
    $format = array('%d','%d');
    $result = $wpdb->delete($table, $where, $format);

    return $result;
  }





  // NEW ========================

  public function write_new_recurring_child(EventClone $item, int $child_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;
    $timezone = \wp_timezone();
    $now = new \DateTime('now', $timezone);

    $data = array(
      'modified' => $now->format($this->now_format),
      'parent_ID' => $item->parent_id,
      'child_ID' => $child_id,
      'event_start' => $item->start_date->getTimestamp(),
      'event_end' => $item->end_date->getTimestamp(),
      'post_status' => $item->post['post_status'],
      'update_check' => 1,
      'date_slug' => $item->event_slug,
    );
    $format = array('%s', '%d', '%d', '%d', '%d', '%s', '%d', '%s');

    $result = $wpdb->insert($table, $data, $format);

    return $result;
  }


  public function update_existing_recurring_child(EventClone $item, int $child_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;
    $timezone = \wp_timezone();
    $now = new \DateTime('now', $timezone);

    $data = array('modified' => $now->format($this->now_format));
    $format = array('%s');
    

    $data = array(
      'modified' => $now->format($this->now_format),
      'event_start' => $item->start_date->getTimestamp(),
      'event_end' => $item->end_date->getTimestamp(),
      'post_status' => $item->post['post_status'],
      'update_check' => 1,
      'date_slug' => $item->event_slug,
    );
    $where = array('parent_ID' => $item->parent_id, 'child_ID' => $child_id);
    $format = array('%s', '%d', '%d', '%s', '%d', '%s');

    $result = $wpdb->update($table, $data, $where, $format);

    return $result;
  }


  public function update_existing_recurring_child_modified(int $child_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;
    $timezone = \wp_timezone();
    $now = new \DateTime('now', $timezone);

    $data = array('modified' => $now->format($this->now_format));
    $where = array('child_ID' => $child_id);
    $format = array('%s');

    $result = $wpdb->update($table, $data, $where, $format);

    return $result;
  }


  /**
   * Clear update checks for recurring events
   *
   * @param int $parent_id
   * @return int|false
   */
  public function clear_recurring_update_checks(int $parent_id) : bool {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $data = array('update_check' => 0);
    $where = array('parent_ID' => $parent_id);
    $format = array('%d');
    $result = $wpdb->update($table, $data, $where, $format);

    return (bool) $result;
  }


  /**
   * Get events with matching slug
   *
   * @param int $parent_id
   * @return int|false
   */
  public function check_unused_update_checks(int $parent_id) : false|array {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT child_ID FROM $table WHERE parent_ID=%d AND update_check=0",
        $parent_id
      )
    );

    if ( !$results ) { return false; }

    return $results;
  }



  /**
   * Get events with matching slug
   *
   * @param string $slug
   * @return int|false
   */
  public function check_child_events_for_date_slug(string $slug) : false|array {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT child_ID FROM $table WHERE date_slug=%s AND update_check=0",
        $slug
      )
    );

    if ( !$results ) { return false; }

    return $results;
  }




  /**
   * Delete DB row with parent/child ids
   *
   * @param int $post_id
   * @return int|false
   * 
   * delete_recurring_event
   */
  public function delete_recurring_event(int $post_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $where = array('child_ID' => $post_id);
    $format = array('%d');
    $result = $wpdb->delete($table, $where, $format);

    return $result;
  }



  /**
   * Get last child event
   *
   * @param int $parent_id
   * @return int|false
   */
  public function get_last_child_event(int $parent_id) : false|int {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT MAX(event_start) AS last_event_start FROM $table WHERE parent_ID=%d",
        $parent_id
      )
    );

    if ( !$results ) { return false; }

    if ( isset($results[0]->last_event_start) ) {
      return $results[0]->last_event_start;
    }

    return false;
  }



  // NEW ========================
}
