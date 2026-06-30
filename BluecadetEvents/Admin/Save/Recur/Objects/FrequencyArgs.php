<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;


class FrequencyArgs {
  
  /**
   * Whether to use frequency
   *
   * @var boolean
   */
  public bool $use_frequency;

  /**
   * The frequency of the event
   *
   * @var string
   */
  public string $frequency;

  /**
   * The days of the week for weekly frequency
   *
   * @var array
   */
  public array $weekly_days;

  /**
   * The schedule for monthly frequency
   *
   * @var string
   */
  public string $month_schedule;

  /**
   * The day of the month for monthly frequency
   *
   * @var string
   */
  public string $month_day;

  /**
   * The date of the month for monthly frequency
   *
   * @var string
   */
  public string $month_date;

  /**
   * The end type for the frequency
   *
   * @var string
   */
  public string $end_type;

  /**
   * The end date for the frequency
   *
   * @var string
   */
  public string $end_date;

  /**
   * The number of occurrences for the frequency
   *
   * @var integer
   */
  public int $end_after_x;

  /**
   * The start date timestamp for the frequency
   *
   * @var string
   */
  public string $start_date_timestamp;


  public function __construct() {
    $this->use_frequency = false;
    $this->frequency = '';
    $this->weekly_days = [];
    $this->month_schedule = '';
    $this->month_day = '';
    $this->month_date = '';
    $this->end_type = '';
    $this->end_date = '';
    $this->end_after_x = 0;
    $this->start_date_timestamp = '';
  }


  public function setUseFrequency(bool $value) {
    $this->use_frequency = $value;
  }

  public function setFrequency(string $value) {
    $this->frequency = $value;
  }

  public function setWeeklyDays(array $value) {
    $this->weekly_days = $value;
  }

  public function setMonthSchedule(string $value) {
    $this->month_schedule = $value;
  }

  public function setMonthDay(string $value) {
    $this->month_day = $value;
  }

  public function setMonthDate(string $value) {
    $this->month_date = $value;
  }

  public function setEndType(string $value) {
    $this->end_type = $value;
  }

  public function setEndDate(string $value) {
    $this->end_date = $value;
  }

  public function setEndAfterX(int $value) {
    $this->end_after_x = $value;
  }

  public function setStartDateTimestamp(string $value) {
    $this->start_date_timestamp = $value;
  }


  public function to_array() {
    return [
      'use_frequency'         => $this->use_frequency,
      'frequency'             => $this->frequency,
      'weekly_days'           => $this->weekly_days,
      'month_schedule'        => $this->month_schedule,
      'month_day'             => $this->month_day,
      'month_date'            => $this->month_date,
      'end_type'              => $this->end_type,
      'end_date'              => $this->end_date,
      'end_after_x'           => $this->end_after_x,
      'start_date_timestamp'  => $this->start_date_timestamp,
    ];
  }






  // 'use_frequency'         => (bool) ($this->RDATE->get_meta('use_frequency') ?: false),
  // 'frequency'             => (string) ($this->RDATE->get_meta('freq') ?: ''),
  // 'weekly_days'           => (array)  ($this->RDATE->get_meta('freq_days') ?: []),
  // 'month_schedule'        => (string) ($this->RDATE->get_meta('freq_mo_schedule') ?: ''),
  // 'month_day'             => (string) ($this->RDATE->get_meta('freq_mo_day') ?: ''),
  // 'month_date'            => (string) ($this->RDATE->get_meta('freq_mo_date') ?: ''),
  // 'end_type'              => (string) ($this->RDATE->get_meta('freq_end_type') ?: ''),
  // 'end_date'              => (string) ($this->RDATE->get_meta('freq_end_date') ?: ''),
  // 'end_after_x'           => (string) ($this->RDATE->get_meta('freq_end_after_x') ?: ''),
  // 'start_date_timestamp'  => (string) ($this->RDATE->get_meta('start_timestamp') ?: ''),



}