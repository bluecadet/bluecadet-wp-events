<?php

namespace BluecadetEvents\Admin\Meta\Register;
use BluecadetEvents\Admin\Meta\Register\AbstractRegisterMeta;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Plugin\Hooks;


class RegisterEventMeta extends AbstractRegisterMeta {

  public function register() : void {
    $this->keys = EventsMetaKeys::get_keys();
  }

  public function register_meta() : void {
    $meta_type = Settings::$events_machine_name;

    // DATES //
    $this->field( $meta_type, 'start_date', $this->string_args, [
      'description' => __( 'Event start date (YYYY-MM-DD)', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'start_time', $this->string_args, [
      'description' => __( 'Event start time (HH:MM)', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'start_timestamp', $this->int_args, [
      'description' => __( 'Event start Unix timestamp', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'end_date', $this->string_args, [
      'description' => __( 'Event end date (YYYY-MM-DD)', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'end_time', $this->string_args, [
      'description' => __( 'Event end time (HH:MM)', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'end_timestamp', $this->int_args, [
      'description' => __( 'Event end Unix timestamp', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'start_month_year', $this->string_args, [
      'description' => __( 'Event month and year', 'basecadet' ),
    ] );


    // OPTIONS //
    $this->field( $meta_type, 'hide_time_display', $this->bool_args, [
      'description' => __( 'Hide time display on frontend', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'hide_end_time_display', $this->bool_args, [
      'description' => __( 'Hide end time display on frontend', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'virtual_event', $this->bool_args, [
      'description' => __( 'Event is virtual', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'virtual_url', $this->string_args, [
      'description' => __( 'Virtual event URL', 'basecadet' ),
    ] );


    // RECURRING //
    $this->field( $meta_type, 'is_recurring', $this->bool_args, [
      'description' => __( 'Event is recurring', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'is_recurring_was', $this->bool_args, [
      'description' => __( 'Event is recurring before post alteration', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'use_frequency', $this->bool_args, [
      'description' => __( 'Show frequency options for recurring events', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'freq', $this->string_args, [
      'description' => __( 'Event recurrence frequency', 'basecadet' ),
      'default' => 'daily',
      'sanitize_callback' => function( $value ) {
        $allowed = ['none', 'daily', 'weekly', 'monthly', 'consecutive'];
        return in_array( $value, $allowed ) ? $value : 'none';
      },
    ] );


    // Weekly - Days of Week (array of 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun')
    $this->field( $meta_type, 'freq_days', $this->array_args, [
      'description' => __( 'Event weekly recurring days', 'basecadet' ),
      'sanitize_callback' => function( $value ) {
        if ( ! is_array( $value ) ) {
          return [];
        }
        $allowed = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        return array_filter( $value, fn( $item ) => in_array( $item, $allowed ) );
      },
    ] );

    // Monthly - Schedule (array of 'first', 'second', 'third', 'fourth', 'last', 'every_other', 'date')
    $this->field( $meta_type, 'freq_mo_schedule', $this->string_args, [
      'description' => __( 'Event monthly recurring schedule', 'basecadet' ),
      'sanitize_callback' => function( $value ) {
        $allowed = ['first', 'last', 'second', 'third', 'fourth', 'every_other', 'date'];
        return in_array( $value, $allowed ) ? $value : 'first';
      },
    ] );

    // Monthly - Day of Month (array of 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun')
    $this->field( $meta_type, 'freq_mo_day', $this->string_args, [
      'description' => __( 'Event monthly recurring day', 'basecadet' ),
      'sanitize_callback' => function( $value ) {
        $allowed = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        return in_array( $value, $allowed ) ? $value : 'sunday';
      },
    ] );

    $this->field( $meta_type, 'freq_mo_date', $this->string_args, [
      'description' => __( 'Monthly repeating date, single day of the month (01-31)', 'basecadet' ),
      'sanitize_callback' => function( $value ) {
        if ( ! is_string( $value ) ) {
          return '';
        }
        return preg_match( '/^(0[1-9]|[12][0-9]|3[01])$/', $value ) ? $value : '';
      },
    ] );

    // Consecutive Offset
    $this->field( $meta_type, 'freq_consecutive_buffer', $this->int_args, [
      'description' => __( 'Event recurrence offset - time between occurrences', 'basecadet' ),
      'default' => 0,
      'sanitize_callback' => function( $value ) {
        return is_numeric( $value ) ? intval( $value ) : 0;
      },
    ] );

    // Consecutive Count
    $this->field( $meta_type, 'freq_consecutive_count', $this->int_args, [
      'description' => __( 'Event recurrence count - number of occurrences', 'basecadet' ),
      'default' => 2,
      'sanitize_callback' => function( $value ) {
        return is_numeric( $value ) ? intval( $value ) : 2;
      },
    ] );

    // Recurrence End Options
    $this->field( $meta_type, 'freq_end_type', $this->string_args, [
      'description' => __( 'Event recurrence end type', 'basecadet' ),
      'default' => 'on_date',
      'sanitize_callback' => function( $value ) {
        $allowed = ['on_date', 'after_x'];
        return in_array( $value, $allowed ) ? $value : 'on_date';
      },
    ] );

    // Recurring End date (YYYY-MM-DD)
    $this->field( $meta_type, 'freq_end_date', $this->string_args, [
      'description' => __( 'Event recurrence end date (YYYY-MM-DD)', 'basecadet' ),
    ] );

    // Recurring End After X
    $this->field( $meta_type, 'freq_end_after_x', $this->int_args, [
      'description' => __( 'Event recurrence end after X occurrences', 'basecadet' ),
      'sanitize_callback' => function( $value ) {
        return is_numeric( $value ) ? intval( $value ) : 1;
      },
    ] );

    // Recurring Description
    $this->field( $meta_type, 'recur_desc', $this->string_args, [
      'description' => __( 'Event recurrence description', 'basecadet' ),
      'sanitize_callback' => function( $value ) {
        return is_string( $value ) ? $value : '';
      },
    ] );


    $occurence_props = [
      'start_date' => [ 'type' => 'string' ],
      'customize' => ['type' => 'boolean', 'default' => false],
      'end_date' => [ 'type' => 'string'],
      'start_time' => [ 'type' => 'string' ],
      'end_time' => [ 'type' => 'string' ],
    ];

    // Recurring Custom Occurrences
    $this->field( $meta_type, 'custom_occurrences', $this->array_args, [
      'description'   => __( 'Custom event recurrence occurrences (array of timestamps)', 'basecadet' ),
      'show_in_rest'  => [
        'schema' => [
          'type'  => 'array',
          'items' => [
            'type' => 'object',
            'properties' => $occurence_props,
          ],
        ],
      ],
    ] );

    // Recurring Omit Dates (array of timestamps to omit from recurrence)
    $this->field( $meta_type, 'omissions', $this->array_args, [
      'description'   => __( 'Custom event recurrence omit dates (array of timestamps)', 'basecadet' ),
      'sanitize_callback' => function( $value ) {
        if ( ! is_array( $value ) ) {
          return [];
        }
        // only use values formatted as YYYY-MM-DD
        return array_filter( $value, fn( $item ) => preg_match( '/^\d{4}-\d{2}-\d{2}$/', $item ) );
      },
    ] );


    // Removing Recurring Events
    $this->field( $meta_type, 'remove_recurring', $this->string_args, [
      'description' => __( 'How to remove recurring events', 'basecadet' ),
      'default' => 'delete',
      'sanitize_callback' => function( $value ) {
        $allowed = ['delete', 'to_posts'];
        return in_array( $value, $allowed ) ? $value : 'delete';
      },
    ] );


    // Previous recurrence strategy snapshot
    $this->field( $meta_type, 'recur_strategy_was', $this->object_args, [
      'description'   => __( 'Custom event recurrence occurrences (array of timestamps)', 'basecadet' ),
      'show_in_rest'  => [
        'schema' => [
          'type'                 => 'object',
          'additionalProperties' => true,
          'properties' => [
            'use_frequency'        => ['type' => 'boolean'],
            'frequency'            => ['type' => 'string'],
            'weekly_days'          => ['type' => 'array', 'items' => ['type' => 'string']],
            'month_schedule'       => ['type' => 'string'],
            'month_day'            => ['type' => 'string'],
            'month_date'           => ['type' => 'string'],
            'end_type'             => ['type' => 'string'],
            'end_date'             => ['type' => 'string'],
            'end_after_x'          => ['type' => 'integer'],
            'start_date_timestamp' => ['type' => 'string'],
            'occurences'           => ['type' => 'array', 'items' => ['type' => 'object', 'additionalProperties' => true]],
            'omissions'            => ['type' => 'array', 'items' => ['type' => 'string']],
            'primary_start_date'   => ['type' => 'string'],
            'primary_start_time'   => ['type' => 'string'],
            'primary_end_date'     => ['type' => 'string'],
            'primary_end_time'     => ['type' => 'string'],
          ],
        ]
      ],
    ] );


    // Parent/Child Meta
    $this->field( $meta_type, 'is_parent', $this->bool_args, [
      'description' => __( 'If event is a parent', 'basecadet' ),
      'default' => false,
    ] );

    $this->field( $meta_type, 'is_child', $this->bool_args, [
      'description' => __( 'If event is a child', 'basecadet' ),
      'default' => false,
    ] );

    $this->field( $meta_type, 'parent_id', $this->int_args, [
      'description' => __( 'Parent event ID', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'date_slug', $this->string_args, [
      'description' => __( 'Date slug for child events', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'child_deny_override', $this->bool_args, [
      'description' => __( 'If parent content should not override child content', 'basecadet' ),
      'default' => false,
    ] );


    if ( Hooks::hook_filter_use_event_locations() ) {
      // Locations
      $this->field( $meta_type, 'location_ids', $this->array_args, [
        'description'   => __( 'Locations where event takes place', 'basecadet' ),
        'show_in_rest'  => [
          'schema' => [
            'type'  => 'array',
            'items' => [
              'type' => 'integer',
            ],
          ],
        ],
      ] );
    }


    if ( Hooks::hook_filter_use_event_series() ) {
      // Series
      $this->field( $meta_type, 'series_ids', $this->array_args, [
        'description'   => __( 'Series where event belongs', 'basecadet' ),
        'show_in_rest'  => [
          'schema' => [
            'type'  => 'array',
            'items' => [
              'type' => 'integer',
            ],
          ],
        ],
      ] );
    }


  }
}
