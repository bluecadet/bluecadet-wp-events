<?php

function bce__get_options() {
  $date_format   = BluecadetEvents\Plugin\Hooks::hook_filter_date_display_format();
  $time_format   = BluecadetEvents\Plugin\Hooks::hook_filter_time_display_format();
  $date_time_sep = BluecadetEvents\Plugin\Hooks::hook_filter_date_time_sep_format();

  return [
    'date_format'   => esc_html($date_format),
    'time_format'   => esc_html($time_format),
    'date_time_sep' => esc_html($date_time_sep),
  ];
}


function bce__get_start_date($post = null) {
  if (!$post) {
    global $post;
  }

  if (is_int($post)) {
    $id = $post;
    $post = new stdClass();
    $post->ID = $id;
  }

  $settings = bce__get_options();

  if ( $field = get_post_meta($post->ID, 'bc_events_start', true) ) {
    $timezone = wp_timezone();
    $fDate = new DateTime('', $timezone);
    $fDate->setTimestamp($field);
    return $fDate->format($settings['date_format']);
  }

  return false;
}


function bce__get_end_date($post = null) {
  if (!$post) {
    global $post;
  }

  if (is_int($post)) {
    $id = $post;
    $post = new stdClass();
    $post->ID = $id;
  }

  $settings = bce__get_options();

  if ( $field = get_post_meta($post->ID, 'bc_events_end', true) ) {
    $timezone = wp_timezone();
    $fDate = new DateTime('', $timezone);
    $fDate->setTimestamp($field);
    return $fDate->format($settings['date_format']);
  }

  return false;
}
