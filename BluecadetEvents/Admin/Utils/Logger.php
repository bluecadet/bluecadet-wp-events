<?php

namespace BluecadetEvents\Admin\Utils;

/**
 * Logger
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class Logger {
  private static $can_log = true;


  public static function log(mixed $data) : void {
    if ( !self::$can_log ) return;

    error_log(print_r($data, true));
  }
}
