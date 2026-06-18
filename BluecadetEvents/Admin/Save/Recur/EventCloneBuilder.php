<?php

namespace BluecadetEvents\Admin\Save\Recur;
use BluecadetEvents\Admin\Save\Recur\Objects\RecurringEvent;
use BluecadetEvents\Admin\Save\Recur\Objects\EventClone;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Admin\Utils\Logger;

class EventCloneBuilder {
  private RecurringEvent|null $RDATE;
  public EventClone $clone_data;
  
  public function __construct(RecurringEvent $RDATE) {
    $this->RDATE = $RDATE;

    $this->clone_data = new EventClone($this->RDATE->parent_post_id);
  
    $this->create_clone_post();
    $this->create_clone_taxonomies();
    $this->create_clone_meta();
    $this->create_clone_misc();
  }


  private function create_clone_post() : void {

    $this->clone_data->post = [
      'post_title'     => $this->RDATE->parent_post->post_title,
      'post_content'   => $this->RDATE->parent_post->post_content,
      'post_excerpt'   => $this->RDATE->parent_post->post_excerpt,
      'post_status'    => $this->RDATE->parent_post->post_status,
      'post_type'      => $this->RDATE->parent_post->post_type,
      'post_author'    => $this->RDATE->parent_post->post_author,
      'post_password'  => $this->RDATE->parent_post->post_password,
      'post_parent'    => $this->RDATE->parent_post->post_parent,
      'menu_order'     => $this->RDATE->parent_post->menu_order,
      'comment_status' => $this->RDATE->parent_post->comment_status,
      'ping_status'    => $this->RDATE->parent_post->ping_status,
    ];

  }


  private function create_clone_taxonomies() {
    $taxonomies = get_object_taxonomies( $this->RDATE->parent_post->post_type );
 
    foreach ( $taxonomies as $taxonomy ) {
      $terms = wp_get_object_terms( $this->RDATE->parent_post_id, $taxonomy, [ 'fields' => 'ids' ] );
      $this->clone_data->taxonomies[$taxonomy] = $terms;
    }
  }


  private function create_clone_meta() {
    $exclude_keys = Hooks::hook_filter_exclude_meta_keys();
    $always_exclude = [
      '_edit_lock',
      '_crdt_document',
      '_edit_last',
      '_wp_old_slug',
      '_wp_old_date',
      '_wp_trash_meta_status',
      '_wp_trash_meta_time',
      '_wp_desired_post_slug',
      '_wp_has_been_previewed',
      $this->RDATE->keys['parent_id'],
      $this->RDATE->keys['child_deny_override'],
      $this->RDATE->keys['date_slug'],
      $this->RDATE->keys['start_month_year'],
      $this->RDATE->keys['is_child'],
      $this->RDATE->keys['is_parent'],
      // '_yoast_wpseo_canonical'
      // '_yoast_wpseo_meta-robots-noindex'
      // '_yoast_wpseo_sitemap-include'
      // '_yoast_wpseo_title'      // debatable — some prefer to copy these
      // '_yoast_wpseo_metadesc'
    ];

    $final_exclude = array_merge($exclude_keys, $always_exclude);

    foreach ( $this->RDATE->all_meta as $key => $value ) {

      if ( $key === '_yoast_wpseo_canonical' ) {
        $value = get_permalink($this->RDATE->parent_post_id);
      } elseif ( in_array($key, $final_exclude) ) {
        continue;
      } else {
        if ( is_array($value) && count($value) === 1 ) {
          $this->clone_data->meta[$key] = maybe_unserialize($value[0]);
        } else {
          $this->clone_data->meta[$key] = $value;
        }
      }
    }
  }


  private function create_clone_misc() {
    $this->clone_data->thumbnail = get_post_thumbnail_id( $this->RDATE->parent_post_id );
  }


  public function clear_recurring() {
    $recur_keys = [
      'is_recurring',
      'is_recurring_was',
      'use_frequency',
      'freq',
      'freq_days',
      'freq_mo_schedule',
      'freq_mo_day',
      'freq_mo_date',
      'freq_end_type',
      'freq_end_date',
      'freq_end_after_x',
      'custom_occurrences',
      'omissions',
      'remove_recurring',
      'recur_strategy_was',
      'is_parent',
    ];

    foreach ($recur_keys as $key) {
      if ( isset($this->clone_data->meta[$this->RDATE->keys[$key]]) ) {
        unset($this->clone_data->meta[$this->RDATE->keys[$key]]);
      }
    }
  }


  // Update only - don't overwrite date related meta
  public function clear_dates_meta() {
    $recur_keys = [
      'is_recurring',
      'is_recurring_was',
      'use_frequency',
      'freq',
      'freq_days',
      'freq_mo_schedule',
      'freq_mo_day',
      'freq_mo_date',
      'freq_end_type',
      'freq_end_date',
      'freq_end_after_x',
      'custom_occurrences',
      'omissions',
      'remove_recurring',
      'recur_strategy_was',
      'is_parent',
      'start_timestamp',
      'end_timestamp',
      'start_date',
      'start_time',
      'end_date',
      'end_time',
      'start_month_year',
    ];

    foreach ($recur_keys as $key) {
      if ( isset($this->clone_data->meta[$this->RDATE->keys[$key]]) ) {
        unset($this->clone_data->meta[$this->RDATE->keys[$key]]);
      }
    }
  }


  public function set_parent_id_meta() {
    $this->clone_data->meta[$this->RDATE->keys['parent_id']] = $this->RDATE->parent_post_id;
  }


  public function set_child_update_meta(int $child_id) {
    $this->clone_data->post['ID'] = (int)$child_id;
    $this->clone_data->child_id = (int)$child_id;

    if ( $date_slug = get_post_meta( $child_id, $this->RDATE->keys['date_slug'], true ) ) {
      $this->clone_data->post['post_name'] = $this->RDATE->parent_post->post_name . '--' . $date_slug;
    }
  }


  public function set_dates(\DateTime $start, \DateTime $end, string $date_slug) {
    $this->clone_data->meta[$this->RDATE->keys['start_timestamp']] = $start->getTimestamp();
    $this->clone_data->meta[$this->RDATE->keys['end_timestamp']] = $end->getTimestamp();
    $this->clone_data->meta[$this->RDATE->keys['start_date']] = $start->format('Y-m-d');
    $this->clone_data->meta[$this->RDATE->keys['start_time']] = $start->format('H:i');
    $this->clone_data->meta[$this->RDATE->keys['end_date']] = $end->format('Y-m-d');
    $this->clone_data->meta[$this->RDATE->keys['end_time']] = $end->format('H:i');
    $this->clone_data->meta[$this->RDATE->keys['start_month_year']] = $start->format('F Y');
    $this->clone_data->meta[$this->RDATE->keys['date_slug']] = $date_slug;
    $this->clone_data->start_date = $start;
    $this->clone_data->end_date = $end;
    $this->clone_data->event_slug = $date_slug;

    // trim slug base so that, when slug suffix is added, it is always less than 200 chars
    $slug_suffix = $date_slug ? '--' . $date_slug : '';
    $slug_base = $this->RDATE->parent_post->post_name;

    if ( strlen($slug_base) + strlen($slug_suffix) > 200 ) {
      $slug_base = substr($slug_base, 0, 200 - strlen($slug_suffix));
    }

    $this->clone_data->post['post_name'] = $slug_base . $slug_suffix;
  }


  public function get_clone() {
    return $this->clone_data;
  }

}