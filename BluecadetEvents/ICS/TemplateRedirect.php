<?php

namespace BluecadetEvents\ICS;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Meta\MetaKeys;

class TemplateRedirect {

  public function __construct() {
    add_action('init', [$this, 'handle_init']);
    add_filter('redirect_canonical', [$this, 'handle_redirect_canonical'], 10, 1);
    add_action('template_redirect', [$this, 'handle_template_redirect']);
  }


  /**
   * Prevent redirect on bce_ical param page
   *
   * @param string $redirect_url
   * @return string|false
   */
  public function handle_redirect_canonical(string $redirect_url) : string|false {
    if (isset($_GET[Settings::$ics_param_name])) {
      return false;
    }

    return $redirect_url;
  }


  /**
   * Add query vars
   *
   * @return void
   */
  public function handle_init() {
    add_filter('query_vars', function(array $vars): array {
      $vars[] = Settings::$ics_param_name;
      return $vars;
    });
  }



  /**
   * If ICS Param Name is present in an event url, create an ICS file
   *
   * @return void
   */
  public function handle_template_redirect() : void {
    if (!isset($_GET[Settings::$ics_param_name])) return;

    $post = get_queried_object();
    if (!$post || $post->post_type !== Settings::$events_machine_name) return;

    $ics = $this->build_ics($post);
    if (!$ics) return;

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . sanitize_title($post->post_title) . '.ics"');
    echo $ics;
    exit;
  }


  /**
   * Undocumented function
   *
   * @param \WP_Post $post
   * @return string
   */
  public static function get_ics_link(\WP_Post $post): string {
    return add_query_arg(Settings::$ics_param_name, '1', get_permalink($post));
  }

  private function build_ics(\WP_Post $post): string {
    $keys  = MetaKeys::get_keys();
    $start = (int) get_post_meta($post->ID, $keys['start_timestamp'], true);
    $end   = (int) get_post_meta($post->ID, $keys['end_timestamp'], true);

    if (!$start || !$end) return '';

    $uid     = $post->ID . '@' . parse_url(home_url(), PHP_URL_HOST);
    $now     = gmdate('Ymd\THis\Z');
    $title   = Hooks::hook_filter_ics_event_title(get_the_title($post), $post->ID);
    $summary = $this->escape($title);
    $desc    = $this->escape(wp_strip_all_tags(get_the_excerpt($post)));
    $url     = get_permalink($post);
    $organizer = Hooks::hook_filter_ics_default_organizer();

    return implode("\r\n", [
      'BEGIN:VCALENDAR',
      'VERSION:2.0',
      'PRODID:-//' . \get_bloginfo('name') . '//Events//EN',
      'CALSCALE:GREGORIAN',
      'METHOD:PUBLISH',
      'BEGIN:VEVENT',
      'UID:'         . $uid,
      'DTSTAMP:'     . $now,
      'DTSTART:'     . gmdate('Ymd\THis\Z', $start),
      'DTEND:'       . gmdate('Ymd\THis\Z', $end),
      'SUMMARY:'     . $summary,
      'DESCRIPTION:' . $desc,
      'URL:'         . $url,
      'ORGANIZER;CN="'   . $organizer . '"',
      'END:VEVENT',
      'END:VCALENDAR',
      '',
    ]);
  }

  private function escape(string $str): string {
    return addcslashes(preg_replace('/\r?\n/', '\\n', $str), ',;\\');
  }

}