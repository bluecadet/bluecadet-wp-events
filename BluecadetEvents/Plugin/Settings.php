<?php

namespace BluecadetEvents\Plugin;

class Settings {

  private static $initialized = false;
  public static  $plugin_name;
  public static  $plugin_dir;
  public static  $plugin_url;
  public static  $version;
  public static  $user_settings;
  public static  $rest_namespace;
  public static  $events_meta_ns;
  public static  $locations_meta_ns;
  public static  $series_meta_ns;
  public static  $contact_meta_ns;
  public static  $events_machine_name;
  public static  $location_machine_name;
  public static  $contact_machine_name;
  public static  $series_machine_name;
  public static  $date_save_format;
  public static  $time_save_format;
  public static  $date_time_save_format;
  public static  $events_table;
  public static  $recurring_events_table;


  public static function __init() {

    if ( self::$initialized ) {
      return;
    }

    self::$initialized       = true;
    self::$plugin_name       = 'bluecadet-events';
    self::$plugin_dir        = plugin_dir_path( dirname( __FILE__, 2 ) );
    self::$plugin_url        = plugin_dir_url(  dirname( __FILE__, 2 ) );
    self::$version           = '1.0.0';
    self::$rest_namespace    = 'bc-events/v1';
    self::$events_meta_ns    = 'bc_events_';
    self::$locations_meta_ns = 'bc_events_location_';
    self::$series_meta_ns    = 'bc_events_series_';
    self::$contact_meta_ns   = 'bc_events_contact_';

    self::$events_machine_name   = 'bc-events';
    self::$location_machine_name = 'bc-events-locations';
    self::$contact_machine_name  = 'bc-events-contacts';
    self::$series_machine_name   = 'bc-events-series';

    self::$events_table = 'bc_events';
    self::$recurring_events_table = 'bc_events_recurring';

    self::$date_save_format      = 'Y-m-d';
    self::$time_save_format      = 'G:i';
    self::$date_time_save_format = self::$date_save_format . ' ' . self::$time_save_format;

  }


}
