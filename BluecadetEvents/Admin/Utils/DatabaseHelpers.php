<?php

namespace BluecadetEvents\Admin\Utils;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Save\Recur\Objects\EventClone;
use BluecadetEvents\Admin\Save\Recur\Objects\EventPost;

/**
 * Database helper functions
 *
 * Single events table (`bc_events`). Every event occurrence is one row keyed by
 * its post_id. Standalone events and series masters have parent_ID = 0 (masters
 * additionally have is_parent = 1); recurring children carry parent_ID = the
 * master's post_id plus the recurrence-generation columns date_slug / update_check.
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class DatabaseHelpers {

  private static ?DatabaseHelpers $_instance = null;
  private string $events_table;
  private string $now_format       = 'Y-m-d H:i:s';


  private function  __construct() {
    // Locked
    $this->events_table = Settings::$events_table;
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


  private function now() : string {
    $now = new \DateTime('now', \wp_timezone());
    return $now->format($this->now_format);
  }


  // =======================================
  //             Writes
  // =======================================


  /**
   * Insert or update an event
   *
   * Children are written by upsert_child() from the recurrence engine, never here.
   *
   * @param EventPost $event_post
   * @return bool
   */
  public function upsert_event(EventPost $event_post) : bool {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $result = $wpdb->query(
      $wpdb->prepare(
        "INSERT INTO {$table}
          (modified, post_id, post_slug, event_start, event_end, parent_ID, is_parent)
         VALUES (%s, %d, %s, %d, %d, %d, %d)
         ON DUPLICATE KEY UPDATE
          modified = VALUES(modified),
          post_slug = VALUES(post_slug),
          event_start = VALUES(event_start),
          event_end = VALUES(event_end),
          parent_ID = VALUES(parent_ID),
          is_parent = VALUES(is_parent)",
        $event_post->modified,
        $event_post->post_id,
        $event_post->post_slug,
        $event_post->event_start,
        $event_post->event_end,
        $event_post->parent_ID,
        $event_post->is_parent ? 1 : 0
      )
    );

    return $result !== false;
  }


  /**
   * Insert or update a recurring child row.
   *
   * The recurrence engine is the sole writer of child rows. Keyed on the child
   * post_id, so a regenerated occurrence reusing an existing post updates in place.
   *
   * @param EventClone $item
   * @param int        $child_id
   * @return bool
   */
  public function upsert_child(EventClone $item, int $child_id) : bool {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $post_slug = isset($item->post['post_name']) ? (string) $item->post['post_name'] : '';

    $result = $wpdb->query(
      $wpdb->prepare(
        "INSERT INTO {$table}
          (modified, post_id, post_slug, event_start, event_end, parent_ID, is_parent, date_slug, update_check)
         VALUES (%s, %d, %s, %d, %d, %d, 0, %s, 1)
         ON DUPLICATE KEY UPDATE
          modified = VALUES(modified),
          post_slug = VALUES(post_slug),
          event_start = VALUES(event_start),
          event_end = VALUES(event_end),
          parent_ID = VALUES(parent_ID),
          date_slug = VALUES(date_slug),
          update_check = VALUES(update_check)",
        $this->now(),
        $child_id,
        $post_slug,
        $item->start_date->getTimestamp(),
        $item->end_date->getTimestamp(),
        $item->parent_id,
        (string) $item->event_slug
      )
    );

    return $result !== false;
  }


  /**
   * Bump the modified timestamp for a child row.
   *
   * @param int $child_id
   * @return bool
   */
  public function update_child_modified(int $child_id) : bool {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $result = $wpdb->update(
      $table,
      ['modified' => $this->now()],
      ['post_id' => $child_id],
      ['%s'],
      ['%d']
    );

    return $result !== false;
  }


  // =======================================
  //             Deletes
  // =======================================


  /**
   * Delete an event row (standalone, master, or child) by post_id.
   *
   * @param integer $post_id
   * @return int|false
   */
  public function delete_event(int $post_id) : int|false {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    return $wpdb->delete($table, ['post_id' => $post_id], ['%d']);
  }


  // =======================================
  //             Relationship lookups
  // =======================================


  /**
   * Get the master post id for a child, or false if not a child.
   *
   * @param int $event_id
   * @return int|false
   */
  public function is_recurring_child(int $event_id) : int|false {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $parent_id = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT parent_ID FROM {$table} WHERE post_id=%d AND parent_ID!=0 LIMIT 1",
        $event_id
      )
    );

    if ( $parent_id === null ) { return false; }

    return (int) $parent_id;
  }


  /**
   * Get the child post ids for a master, or false if it has none.
   *
   * @param int $event_id
   * @return array|false
   */
  public function is_recurring_parent(int $event_id) : bool|array {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $child_ids = $wpdb->get_col(
      $wpdb->prepare(
        "SELECT post_id FROM {$table} WHERE parent_ID=%d",
        $event_id
      )
    );

    if ( empty($child_ids) ) { return false; }

    return array_map('intval', $child_ids);
  }


  /**
   * Alias of is_recurring_parent(): the child post ids for a master.
   *
   * @param int $parent_id
   * @return array|false
   */
  public function get_recurring_child_ids(int $parent_id) : bool|array {
    return $this->is_recurring_parent($parent_id);
  }


  /**
   * Get the master post for a child.
   *
   * @param int $event_id
   * @return false|\WP_Post
   */
  public function get_recurring_parent(int $event_id) : false|\WP_Post {
    $parent_id = $this->is_recurring_child($event_id);

    if ( !$parent_id ) { return false; }

    $parent_post = get_post($parent_id);

    return $parent_post instanceof \WP_Post ? $parent_post : false;
  }


  /**
   * Get the latest occurrence start for a master's children.
   *
   * @param int $parent_id
   * @return int|false
   */
  public function get_last_child_event(int $parent_id) : false|int {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $last = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT MAX(event_start) FROM {$table} WHERE parent_ID=%d",
        $parent_id
      )
    );

    if ( $last === null ) { return false; }

    return (int) $last;
  }


  // =======================================
  //       Recurrence-generation diffing
  // =======================================


  /**
   * Clear update checks for a master's children after a generation pass.
   *
   * @param int $parent_id
   * @return bool
   */
  public function clear_recurring_update_checks(int $parent_id) : bool {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $result = $wpdb->update(
      $table,
      ['update_check' => 0],
      ['parent_ID' => $parent_id],
      ['%d'],
      ['%d']
    );

    return (bool) $result;
  }


  /**
   * Children that were not touched during the latest generation pass (stale).
   *
   * @param int $parent_id
   * @return false|array Rows with a `post_id` property.
   */
  public function check_unused_update_checks(int $parent_id) : false|array {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT post_id FROM {$table} WHERE parent_ID=%d AND update_check=0",
        $parent_id
      )
    );

    if ( !$results ) { return false; }

    return $results;
  }


  /**
   * Find an existing, not-yet-touched occurrence matching a date slug.
   *
   * @param string $slug
   * @param int    $child_id
   * @return false|array Rows with a `post_id` property.
   */
  public function check_child_events_for_date_slug(string $slug, int $child_id) : false|array {
    global $wpdb;
    $table = $wpdb->prefix . $this->events_table;

    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT post_id FROM {$table} WHERE date_slug=%s AND post_id=%d AND update_check=0",
        $slug,
        $child_id
      )
    );

    if ( !$results ) { return false; }

    return $results;
  }

}
