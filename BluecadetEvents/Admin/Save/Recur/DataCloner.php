<?php

namespace BluecadetEvents\Admin\Save\Recur;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Meta\MetaKeys;
use BluecadetEvents\Admin\Utils\Logger;

class DataCloner {
  public int $parent_id;
  private \WP_Post $parent_post;
  private array $keys = [];
  private array $parent_meta_all;
  public array $clone_post;
  public array $clone_meta;
  public array $clone_taxonomies = [];
  public int|false $clone_thumbnail;

  public function __construct(int $parent_id, \WP_Post $parent_post, array $parent_meta_all) {
    Logger::log('CONSTRUCT START');
    $this->parent_id = $parent_id;
    $this->parent_post = $parent_post;
    $this->parent_meta_all = $parent_meta_all;
    $this->keys = MetaKeys::get_keys();
    Logger::log('CONSTRUCT METHODS');
    $this->create_clone_post();
    $this->create_clone_taxonomies();
    $this->create_clone_meta();
    $this->create_clone_misc();
  }


  private function create_clone_post() : void {

    $this->clone_post = [
      'post_title'     => $this->parent_post->post_title,
      'post_content'   => $this->parent_post->post_content,
      'post_excerpt'   => $this->parent_post->post_excerpt,
      'post_status'    => $this->parent_post->status,
      'post_type'      => $this->parent_post->post_type,
      'post_author'    => $this->parent_post->post_author,
      'post_password'  => $this->parent_post->post_password,
      'post_parent'    => $this->parent_post->post_parent,
      'menu_order'     => $this->parent_post->menu_order,
      'comment_status' => $this->parent_post->comment_status,
      'ping_status'    => $this->parent_post->ping_status,
    ];

    $this->clone_meta = $this->parent_meta_all;
  }


  private function create_clone_taxonomies() {
    $taxonomies = get_object_taxonomies( $this->parent_post->post_type );
 
    foreach ( $taxonomies as $taxonomy ) {
      $terms = wp_get_object_terms( $this->parent_id, $taxonomy, [ 'fields' => 'ids' ] );
      $this->clone_taxonomies[$taxonomy] = $terms;
    }
  }


  private function create_clone_meta() {
    $exclude_keys = Hooks::hook_filter_exclude_meta_keys();
    $exclude_wp_keys = [
      '_edit_lock',
      '_crdt_document',
      '_edit_last',
      '_wp_old_slug',
      '_wp_old_date',
      '_wp_trash_meta_status',
      '_wp_trash_meta_time',
      '_wp_desired_post_slug',
      '_wp_has_been_previewed',
      // '_yoast_wpseo_canonical'
      // '_yoast_wpseo_meta-robots-noindex'
      // '_yoast_wpseo_sitemap-include'
      // '_yoast_wpseo_title'      // debatable — some prefer to copy these
      // '_yoast_wpseo_metadesc'
    ];

    $final_explude = array_merge($exclude_keys, $exclude_wp_keys);

    foreach ( $this->parent_meta_all as $key => $value ) {

      if ( $key === '_yoast_wpseo_canonical' ) {
        $value = get_permalink($this->parent_id);
      } elseif ( in_array($key, $final_explude) ) {
        continue;
      } else {
        if ( is_array($value) && count($value) === 1 ) {
          $this->clone_meta[$key] = maybe_unserialize($value[0]);
        } else {
          $this->clone_meta[$key] = $value;
        }
      }
    }
  }


  private function create_clone_misc() {
    $this->clone_thumbnail = get_post_thumbnail_id( $this->parent_id );
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
      if ( isset($this->clone_meta[$this->keys[$key]]) ) {
        unset($this->clone_meta[$this->keys[$key]]);
      }
    }
  }


  public function set_parent_id() {
    $this->clone_meta[$this->keys['parent_id']] = $this->parent_id;
  }


  public function set_dates(\DateTime $start, \DateTime $end) {
    $this->clone_meta[$this->keys['start_timestamp']] = $start->getTimestamp();
    $this->clone_meta[$this->keys['end_timestamp']] = $end->getTimestamp();
    $this->clone_meta[$this->keys['start_date']] = $start->format('Y-m-d');
    $this->clone_meta[$this->keys['start_time']] = $start->format('H:i');
    $this->clone_meta[$this->keys['end_date']] = $end->format('Y-m-d');
    $this->clone_meta[$this->keys['end_time']] = $end->format('H:i');
    $this->clone_meta[$this->keys['start_month_year']] = $start->format('F Y');

  }


}