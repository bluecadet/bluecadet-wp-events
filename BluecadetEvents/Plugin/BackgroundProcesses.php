<?php

namespace BluecadetEvents\Plugin;
use BluecadetEvents\Admin\Save\Recur\Background\BackgroundEventHandler;
use BluecadetEvents\Admin\Save\Recur\Background\BackgroundDeleteHandler;

class BackgroundProcesses {

  private static $initialized = false;
  private static BackgroundEventHandler $background_event_handler;
  private static BackgroundDeleteHandler $background_delete_handler;

  public static function __init() {

    if ( self::$initialized ) {
      return;
    }

    self::$initialized          = true;
    self::$background_event_handler = new BackgroundEventHandler();
    self::$background_delete_handler = new BackgroundDeleteHandler();

  }

  public static function get_event_handler() {
    return self::$background_event_handler;
  }

  public static function get_event_delete_handler() {
    return self::$background_delete_handler;
  }

}
