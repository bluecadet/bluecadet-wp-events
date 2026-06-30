<?php

namespace BluecadetEvents\Admin\Meta;
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
      'freq_consecutive_buffer' => Settings::$events_meta_ns . 'frequency_consecutive_buffer',
      'freq_consecutive_count' => Settings::$events_meta_ns . 'frequency_consecutive_count',
      'freq_end_type' => Settings::$events_meta_ns . 'frequency_end_type',
      'freq_end_date' => Settings::$events_meta_ns . 'frequency_end_date',
      'freq_end_after_x' => Settings::$events_meta_ns . 'frequency_end_after_x',
      'custom_occurrences' => Settings::$events_meta_ns . 'recurring_custom_occurrences',
      'recur_desc' => Settings::$events_meta_ns . 'recurring_description',
      'omissions' => Settings::$events_meta_ns . 'recurring_omissions',
      'remove_recurring' => Settings::$events_meta_ns . 'remove_recurring',
      'recur_strategy_was' => Settings::$events_meta_ns . 'recur_strategy_was',
      'is_parent' => Settings::$events_meta_ns . 'is_parent',
      'is_child' => Settings::$events_meta_ns . 'is_child',
      'parent_id' => Settings::$events_meta_ns . 'parent_id',
      'date_slug' => Settings::$events_meta_ns . 'date_slug',
      'child_deny_override' => Settings::$events_meta_ns . 'child_deny_override',
      'location_ids' => Settings::$events_meta_ns . 'location_ids',
      'series_ids' => Settings::$events_meta_ns . 'series_ids',
      // 'child_remove_from_recurring' => Settings::$events_meta_ns . 'child_remove_from_recurring',
    ];
  }


  public static function get_event_save_keys() : array {
    $instance = self::get_instance();
    return [
      $instance->keys['start_date'] => 'string',
      $instance->keys['start_time'] => 'string',
      $instance->keys['start_timestamp'] => 'string',
      $instance->keys['end_date'] => 'string',
      $instance->keys['end_time'] => 'string',
      $instance->keys['end_timestamp'] => 'string',
      $instance->keys['hide_time_display'] => 'boolean',
      $instance->keys['hide_end_time_display'] => 'boolean',
      $instance->keys['virtual_event'] => 'boolean',
      $instance->keys['virtual_url'] => 'string',
      $instance->keys['is_recurring'] => 'boolean',
      $instance->keys['use_frequency'] => 'boolean',
      $instance->keys['freq'] => 'string',
      $instance->keys['freq_days'] => 'array',
      $instance->keys['freq_mo_schedule'] => 'string',
      $instance->keys['freq_mo_day'] => 'string',
      $instance->keys['freq_mo_date'] => 'string',
      $instance->keys['freq_consecutive_buffer'] => 'integer',
      $instance->keys['freq_consecutive_count'] => 'integer',
      $instance->keys['freq_end_type'] => 'string',
      $instance->keys['freq_end_date'] => 'string',
      $instance->keys['freq_end_after_x'] => 'integer',
      $instance->keys['recur_desc'] => 'string',
      $instance->keys['custom_occurrences'] => 'object',
      $instance->keys['omissions'] => 'array',
      $instance->keys['remove_recurring'] => 'string',
      $instance->keys['child_deny_override'] => 'string',
      $instance->keys['location_ids'] => 'array',
      $instance->keys['series_ids'] => 'array',
    ]; 
  }

	

}
  