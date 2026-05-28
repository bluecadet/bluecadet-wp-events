<?php

namespace BluecadetEvents\Admin\Meta;
use BluecadetEvents\Plugin\Settings;

class MetaKeys {
	private static $_instance = null;
  private $keys;

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


  public static function get_keys() {
    $instance = self::get_instance();
    return $instance->keys; 
  }


  private function generate_keys() {
    $this->keys = [
      'start_date' => Settings::$events_meta_ns . 'start_date',
      'start_time' => Settings::$events_meta_ns . 'start_time',
      'start_timestamp' => Settings::$events_meta_ns . 'start_timestamp',
      'end_date' => Settings::$events_meta_ns . 'end_date',
      'end_time' => Settings::$events_meta_ns . 'end_time',
      'end_timestamp' => Settings::$events_meta_ns . 'end_timestamp',
      'start_month_year' => Settings::$events_meta_ns . 'start_month_year',
      'hide_time_display' => Settings::$events_meta_ns . 'hide_time_display',
      'hide_end_time_display' => Settings::$events_meta_ns . 'hide_end_time_display',
      'virtual_event' => Settings::$events_meta_ns . 'virtual_event',
      'virtual_url' => Settings::$events_meta_ns . 'virtual_url',
      'is_recurring' => Settings::$events_meta_ns . 'is_recurring',
      'is_recurring_was' => Settings::$events_meta_ns . 'is_recurring_was',
      'use_frequency' => Settings::$events_meta_ns . 'use_frequency',
      'freq' => Settings::$events_meta_ns . 'frequency',
      'freq_days' => Settings::$events_meta_ns . 'frequency_weekly_days',
      'freq_mo_schedule' => Settings::$events_meta_ns . 'frequency_monthly_schedule',
      'freq_mo_day' => Settings::$events_meta_ns . 'frequency_monthly_day',
      'freq_mo_date' => Settings::$events_meta_ns . 'frequency_monthly_date',
      'freq_end_type' => Settings::$events_meta_ns . 'frequency_end_type',
      'freq_end_date' => Settings::$events_meta_ns . 'frequency_end_date',
      'freq_end_after_x' => Settings::$events_meta_ns . 'frequency_end_after_x',
      'custom_occurrences' => Settings::$events_meta_ns . 'recurring_custom_occurrences',
      'omissions' => Settings::$events_meta_ns . 'recurring_omissions',
    ];
  }

	

}
  