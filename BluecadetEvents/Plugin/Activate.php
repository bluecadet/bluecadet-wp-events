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
     *
     * Store event data for better querying and filtering. This table is used to store event data 
     * for each post that has been processed by the plugin.
     */
    $bc_events_table = $wpdb->prefix . Settings::$events_table;

    $event_table_sql = "CREATE TABLE {$bc_events_table} (
      id bigint NOT NULL AUTO_INCREMENT,
      modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      post_id bigint NOT NULL,
      post_slug varchar(200) NOT NULL,
      event_start int(11) NOT NULL,
      event_end int(11) NOT NULL,
      parent_ID bigint NOT NULL DEFAULT 0,
      is_parent tinyint(1) NOT NULL DEFAULT 0,
      date_slug varchar(255) NOT NULL DEFAULT '',
      update_check tinyint(1) NOT NULL DEFAULT 0,
      PRIMARY KEY (id),
      UNIQUE KEY post_id (post_id),
      KEY event_start (event_start),
      KEY parent_ID (parent_ID),
      KEY event_start_parent (event_start, parent_ID),
      KEY parent_update (parent_ID, update_check),
      KEY is_parent (is_parent)
    ) $charset_collate;";

    dbDelta( $event_table_sql );
  }
}
