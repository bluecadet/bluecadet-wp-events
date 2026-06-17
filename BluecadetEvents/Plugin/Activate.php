<?php

namespace BluecadetEvents\Plugin;
use BluecadetEvents\Plugin\Settings;

class Activate {

  static public function activate() {
    self::create_tables();
  }

  static private function create_tables() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();

    /**
     * Events Table
     */
    $bc_events_table = $wpdb->prefix . Settings::$events_table;

    $event_table_sql = "CREATE TABLE IF NOT EXISTS {$bc_events_table} (
      id bigint NOT NULL AUTO_INCREMENT,
      modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      post_id bigint NOT NULL,
      post_slug varchar(200) NOT NULL,
      event_start int(11) NOT NULL,
      event_end int(11) NOT NULL,
      parent_ID bigint NOT NULL,
      UNIQUE KEY id (id),
      KEY post_id (post_id),
      KEY event_start (event_start),
      KEY parent_ID (parent_ID),
      KEY event_start_parent (event_start, parent_ID)
    ) $charset_collate;";

    dbDelta( $event_table_sql );


    /**
     * Recurring Events Table
     */
    $recur_table_name = $wpdb->prefix . Settings::$recurring_events_table;

    $recur_sql = "CREATE TABLE IF NOT EXISTS $recur_table_name (
      id bigint NOT NULL AUTO_INCREMENT,
      modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      parent_ID bigint NOT NULL,
      child_ID bigint NOT NULL,
      event_start int(11) NOT NULL,
      event_end int(11) NOT NULL,
      post_status varchar(20) NOT NULL,
      custom_content boolean DEFAULT false,
      update_check boolean DEFAULT false,
      date_slug varchar(255) NOT NULL,
      UNIQUE KEY id (id),
      KEY parent_child (parent_ID, child_ID)
    ) $charset_collate;";

    dbDelta( $recur_sql );
  }
}
