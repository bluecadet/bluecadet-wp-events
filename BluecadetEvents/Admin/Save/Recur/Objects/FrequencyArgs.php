<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;

/**
 * Immutable value object describing a recurrence frequency.
 *
 * Built once (with named args) from event meta and flattened via to_array()
 * for RRuleBuilder and the recur_strategy_was comparison snapshot.
 */
final class FrequencyArgs {

  public function __construct(
    public readonly bool   $use_frequency = false,
    public readonly string $frequency = '',
    public readonly array  $weekly_days = [],
    public readonly string $month_schedule = '',
    public readonly string $month_day = '',
    public readonly string $month_date = '',
    public readonly int    $consecutive_buffer = 0,
    public readonly int    $consecutive_count = 0,
    public readonly string $end_type = '',
    public readonly string $end_date = '',
    public readonly int    $end_after_x = 0,
    public readonly string $start_date_timestamp = '',
    public readonly string $end_date_timestamp = '',
  ) {}

  public function to_array() : array {
    return [
      'use_frequency'         => $this->use_frequency,
      'frequency'             => $this->frequency,
      'weekly_days'           => $this->weekly_days,
      'month_schedule'        => $this->month_schedule,
      'month_day'             => $this->month_day,
      'month_date'            => $this->month_date,
      'consecutive_buffer'    => $this->consecutive_buffer,
      'consecutive_count'     => $this->consecutive_count,
      'end_type'              => $this->end_type,
      'end_date'              => $this->end_date,
      'end_after_x'           => $this->end_after_x,
      'start_date_timestamp'  => $this->start_date_timestamp,
      'end_date_timestamp'    => $this->end_date_timestamp,
    ];
  }

}
