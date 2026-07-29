<?php

namespace BluecadetEvents\Plugin;

class Settings {

  private static bool   $initialized = false;
  public  static string $plugin_name;
  public  static string $plugin_dir;
  public  static string $plugin_url;
  public  static string $version;
  public  static array  $user_settings;
  public  static string $rest_namespace;
  public  static string $events_meta_ns;
  public  static string $locations_meta_ns;
  public  static string $series_meta_ns;
  public  static string $contact_meta_ns;
  public  static string $ics_param_name;
  public  static string $events_machine_name;
  public  static string $locations_machine_name;
  public  static string $contact_machine_name;
  public  static string $series_machine_name;
  public  static string $date_save_format;
  public  static string $time_save_format;
  public  static string $date_time_save_format;
  public  static string $events_table;


  public static function init() {

    if ( self::$initialized ) {
      return;
    }

    self::$initialized       = true;
    self::$plugin_name       = 'bluecadet-events';
    self::$plugin_dir        = plugin_dir_path( dirname( __FILE__, 2 ) );
    self::$plugin_url        = plugin_dir_url(  dirname( __FILE__, 2 ) );
    self::$version           = '1.0.1';
    self::$rest_namespace    = 'bc-events/v1';
    self::$events_meta_ns    = 'bc_events_';
    self::$locations_meta_ns = 'bc_events_location_';
    self::$series_meta_ns    = 'bc_events_series_';
    self::$contact_meta_ns   = 'bc_events_contact_';

    self::$events_machine_name   = 'bc_events';
    self::$locations_machine_name = 'bc_events_locations';
    self::$contact_machine_name  = 'bc_events_contacts';
    self::$series_machine_name   = 'bc_events_series';

    self::$events_table = 'bc_events';

    self::$date_save_format      = 'Y-m-d';
    self::$time_save_format      = 'G:i';
    self::$date_time_save_format = self::$date_save_format . ' ' . self::$time_save_format;

    self::$ics_param_name        = 'bce_ical';

  }


}
