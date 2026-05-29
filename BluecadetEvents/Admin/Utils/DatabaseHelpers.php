<?php

namespace BluecadetEvents\Admin\Utils;
use BluecadetEvents\Plugin\Settings;

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
  public function write_or_update_event(int $post_id, int $start_date, int $end_date, false|string $title = false) {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT * FROM $table WHERE post_id=%d",
        $post_id
      )
    );

    if ( !$results ) {
      return $this->write_event($post_id, $start_date, $end_date, $title);
    }

    return $this->update_event($post_id, $start_date, $end_date, $title);
  }



  /**
   * Create DB row with post_id, start & end dates
   *
   * @param int $post_id
   * @param int $start_date unix timestamp
   * @param int $end_date unix timestamp
   * @return int|false
   */
  public function write_event(int $post_id, int $start_date, int $end_date, false|string $title = false) {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;
    $timezone = \wp_timezone();
    $now = new \DateTime('now', $timezone);
    $title = $title ? $title : get_the_title($post_id);

    $data = [
      'post_id' => $post_id,
      'event_start_date' => $start_date,
      'event_end_date' => $end_date,
      'post_title' => $title,
      'modified' => $now->format($this->now_format)
    ];

    $result = $wpdb->insert($table, $data, ['%d','%d','%d','%s','%s']);

    return $result;
  }


  /**
   * Update DB row with post_id, start & end dates
   *
   * @param int $post_id
   * @param int $start_date unix timestamp
   * @param int $end_date unix timestamp
   * @return int|false
   */
  public function update_event(int $post_id, int $start_date, int $end_date, false|string $title = false) {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;
    $timezone = \wp_timezone();
    $now = new \DateTime('now', $timezone);
    $title = $title ? $title : get_the_title($post_id);

    $data = [
      'event_start_date' => $start_date,
      'event_end_date' => $end_date,
      'post_title' => $title,
      'modified' => $now->format($this->now_format),
    ];

    $where = ['post_id' => $post_id];

    $result = $wpdb->update($table, $data, $where, ['%d','%d','%s','%s'], ['%d']);
    return $result;
  }


  /**
   * Delete DB row with parent/child ids
   *
   * @param int $post_id
   * @return int|false
   */
  public function delete_event(int $post_id) {
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
      $eids[] = $r->child_ID;
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
      $eids[] = $r->parent_ID;
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
      $cids[] = $r->child_ID;
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
  public function write_recurring_child($parent_id, $child_id) {
    global $wpdb;
    $table = $wpdb->prefix . $this->recurring_table;
    $timezone = \wp_timezone();
    $now = new \DateTime('now', $timezone);

    $data = array('parent_ID' => $parent_id, 'child_ID' => $child_id, 'modified' => $now->format($this->now_format));
    $format = array('%d','%d','%s');
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








  // // ===================================
  // //             Meta Types
  // // ===================================

  // /**
  //  * Get results of meta/event db rows
  //  *
  //  * @param int $meta_id
  //  * @return array|false
  //  */
  // public function get_meta_type_event_ids($meta_id, $meta_type) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->meta_table;

  //   $results = $wpdb->get_results(
  //     $wpdb->prepare(
  //       "SELECT eventID FROM $table WHERE metaID=%d AND metaType=%s",
  //       $meta_id, $meta_type
  //     )
  //   );

  //   if ( !$results ) { return false; }

  //   $eids = [];

  //   foreach ( $results as $r ) {
  //     $eids[] = $r->eventID;
  //   }

  //   if ( !empty($eids) ) { return $eids; }

  //   return false;

  // }

  // /**
  //  * Get results of metas/event db rows
  //  *
  //  * @param int $meta_id
  //  * @return array|false
  //  */
  // public function get_event_meta_type_ids(int $event_id, string $meta_type) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->meta_table;

  //   $results = $wpdb->get_results(
  //     $wpdb->prepare(
  //       "SELECT metaID FROM $table WHERE eventID=%d AND metaType=%s",
  //       $event_id, $meta_type
  //     )
  //   );

  //   if ( !$results ) { return false; }

  //   $eids = [];

  //   foreach ( $results as $r ) {
  //     $eids[] = $r->metaID;
  //   }

  //   if ( !empty($eids) ) { return $eids; }

  //   return false;

  // }


  // /**
  //  * Get results of metas/event db rows
  //  *
  //  * @param int $meta_id
  //  * @return array|false
  //  */
  // public function get_event_meta_type_all_ids(int $event_id) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->meta_table;

  //   $results = $wpdb->get_results(
  //     $wpdb->prepare(
  //       "SELECT metaID FROM $table WHERE eventID=%d",
  //       $event_id
  //     )
  //   );

  //   if ( !$results ) { return false; }

  //   $eids = [];

  //   foreach ( $results as $r ) {
  //     $eids[] = $r->metaID;
  //   }

  //   if ( !empty($eids) ) { return $eids; }

  //   return false;

  // }


  // /**
  //  * Create DB row with metas/child ids
  //  *
  //  * @param int $meta_id
  //  * @param int $child_id
  //  * @return int|false
  //  */
  // public function write_meta_type_event(int $meta_id, string $meta_type, int $event_id) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->meta_table;
  //   $timezone = \wp_timezone();
  //   $now = new \DateTime('now', $timezone);

  //   $data = array(
  //     'metaID' => $meta_id,
  //     'eventID' => $event_id,
  //     'modified' => $now->format($this->now_format),
  //     'metaType' => $meta_type,
  //   );
  //   $format = array('%d','%d','%s', '%s');
  //   $result = $wpdb->insert($table, $data, $format);

  //   return $result;
  // }


  // /**
  //  * Create DB row with metas/child ids
  //  *
  //  * @param int $meta_id
  //  * @param int $child_id
  //  * @return int|false
  //  */
  // public function write_meta_type_event_from_array(array $meta_ids, string $meta_type, int $event_id) {
  //   if ( !empty($meta_ids) ) {
  //     foreach ($meta_ids as $meta_id) {
  //       $this->write_meta_type_event($meta_id, $meta_type, $event_id);
  //     }
  //   }
  // }


  // /**
  //  * Update DB row with metas/event ids
  //  *
  //  * @param int $meta_id
  //  * @param int $event_id
  //  * @return int|false
  //  */
  // public function update_meta_type_event($meta_id, $event_id) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->meta_table;
  //   $timezone = \wp_timezone();
  //   $now = new \DateTime('now', $timezone);

  //   $data = array('modified' => $now->format($this->now_format));
  //   $where = array('metaID' => $meta_id, 'eventID' => $event_id);
  //   $format = array('%s');
  //   $result = $wpdb->update($table, $data, $where, $format, $format);

  //   return $result;
  // }


  // /**
  //  * Create DB row with metas/child ids
  //  *
  //  * @param int $meta_id
  //  * @param int $child_id
  //  * @return int|false
  //  */
  // public function update_meta_type_event_from_array(array $meta_ids, string $meta_type, int $event_id) {
  //   if ( !empty($meta_ids) ) {
  //     foreach ($meta_ids as $meta_id) {
  //       $this->update_meta_event($meta_id, $meta_type, $event_id);
  //     }
  //   }
  // }


  // /**
  //  * Delete DB row with meta/event ids
  //  *
  //  * @param int $meta_id
  //  * @param int $event_id
  //  * @return int|false
  //  */
  // public function delete_meta_type_event($meta_id, $event_id) {
  //   global $wpdb;
  //   $table = $wpdb->prefix . $this->meta_table;

  //   $where = array('metaID' => $meta_id, 'eventID' => $event_id);
  //   $format = array('%d','%d');
  //   $result = $wpdb->delete($table, $where, $format);

  //   return $result;
  // }


  // /**
  //  * Create DB row with metas/child ids
  //  *
  //  * @param int $meta_id
  //  * @param int $child_id
  //  * @return int|false
  //  */
  // public function deleta_meta_type_event_from_array(array $meta_ids, int $event_id) {
  //   if ( !empty($meta_ids) ) {
  //     foreach ($meta_ids as $meta_id) {
  //       $this->deleta_meta_event($meta_id, $event_id);
  //     }
  //   }
  // }

}
