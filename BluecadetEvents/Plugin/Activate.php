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
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      post_id bigint NOT NULL,
      post_title text NOT NULL,
      event_start int(11) NOT NULL,
      event_end int(11) NOT NULL,
      post_status varchar(20) NOT NULL,
      UNIQUE KEY id (id)
    ) $charset_collate;";

    dbDelta( $event_table_sql );


    /**
     * Recurring Events Table
     */
    $recur_table_name = $wpdb->prefix . Settings::$recurring_events_table;

    $recur_sql = "CREATE TABLE IF NOT EXISTS $recur_table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      parent_ID mediumint(9) NOT NULL,
      child_ID mediumint(9) NOT NULL,
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
