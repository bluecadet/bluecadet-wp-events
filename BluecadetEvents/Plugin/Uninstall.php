<?php

namespace BluecadetEvents\Plugin;

/**
 * Uninstall data handling.
 *
 * Runs from uninstall.php when the plugin is deleted. Whether data is removed
 * is a user choice, captured ahead of time (the deactivate modal / settings)
 * and stored in the plugin option, with a developer filter as the final say.
 *
 * @package BluecadetEvents
 */
class Uninstall {

  /** Plugin option holding user settings, incl. the uninstall preference. */
  const OPTION = 'bc_events_settings';

  /** Key within OPTION for the "delete data on uninstall" preference. */
  const PREF_KEY = 'delete_data_on_uninstall';

  /**
   * Persist the user's "delete data on uninstall" preference.
   *
   * @param bool $delete
   * @return void
   */
  public static function save_preference( bool $delete ) : void {
    $opts = get_option( self::OPTION, [] );
    if ( ! is_array( $opts ) ) {
      $opts = [];
    }
    $opts[ self::PREF_KEY ] = $delete;
    update_option( self::OPTION, $opts );
  }

  /**
   * Should uninstall remove all plugin data?
   *
   * Resolution: the saved preference is the default; the
   * `bc_events/uninstall/delete_data` filter has the final say (a real bool
   * only — a bad filter return falls back to the saved preference, so a stray
   * value can never trigger an unwanted deletion).
   *
   * @return bool
   */
  public static function should_delete_data() : bool {
    $opts    = get_option( self::OPTION, [] );
    $default = ( is_array( $opts ) && ! empty( $opts[ self::PREF_KEY ] ) );

    $filtered = apply_filters( 'bc_events/uninstall/delete_data', $default );

    return is_bool( $filtered ) ? $filtered : $default;
  }

  /**
   * Entry point for uninstall.php.
   *
   * @return void
   */
  public static function run() : void {
    if ( self::should_delete_data() ) {
      self::delete_all_data();
    }
  }

  /**
   * Remove everything this plugin created: the custom table, all event /
   * location / series posts and their meta, plugin options, transients, and
   * background-queue / cron leftovers.
   *
   * @return void
   */
  public static function delete_all_data() : void {
    global $wpdb;

    Settings::init();

    // 1. Delete every event/location/series post (any status) + their meta.
    $post_types = [
      Settings::$events_machine_name,
      Settings::$locations_machine_name,
      Settings::$series_machine_name,
    ];
    $placeholders = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );
    $ids = $wpdb->get_col(
      $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ({$placeholders})", ...$post_types )
    );
    foreach ( $ids as $id ) {
      wp_delete_post( (int) $id, true );
    }

    // 2. Drop the custom table (trusted, constant-derived name).
    $table = $wpdb->prefix . Settings::$events_table;
    $wpdb->query( "DROP TABLE IF EXISTS {$table}" );

    // 3. Plugin option.
    delete_option( self::OPTION );

    // 4. Background-processing batches / status / locks for both handlers.
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wp_bc_events_recur_event_%'" );

    // 5. Plugin transients (e.g. datepicker/month-year caches).
    $wpdb->query(
      "DELETE FROM {$wpdb->options}
       WHERE option_name LIKE '\_transient\_bc-events-%'
          OR option_name LIKE '\_transient\_timeout\_bc-events-%'
          OR option_name LIKE '\_transient\_bc_events%'
          OR option_name LIKE '\_transient\_timeout\_bc_events%'"
    );

    // 6. Scheduled cron for the background handlers.
    wp_clear_scheduled_hook( 'wp_bc_events_recur_event_handler_cron' );
    wp_clear_scheduled_hook( 'wp_bc_events_recur_event_delete_handler_cron' );
  }
}
