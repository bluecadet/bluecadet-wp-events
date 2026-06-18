<?php

namespace BluecadetEvents\Admin\Meta\Locations;
use BluecadetEvents\Plugin\Settings;

class MetaKeys {
	private static $_instance = null;
  private array $keys;

	private function __construct() {
		Settings::__init();
    $this->generate_keys();
	}

	private function __clone() {
		// Locked.
	}

	public static function __init() {
		return self::get_instance();
	}

	public static function get_instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


  public static function get_keys() : array {
    $instance = self::get_instance();
    return $instance->keys; 
  }


  private function generate_keys() {
    $this->keys = [
      'address' => Settings::$locations_meta_ns . 'address',
      'description' => Settings::$locations_meta_ns . 'description',
      'website' => Settings::$locations_meta_ns . 'website',
    ];
  }

	

}
  