<?php

namespace BluecadetEvents\Helpers;

use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Admin\Meta\MetaKeys;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;

class TemplateHelpers {

  private static ?TemplateHelpers $instance = null;
  private array $options = [];
  private array $keys = [];

  private function __construct() {
    $this->keys = MetaKeys::get_keys();
  }

  public static function getInstance() : self {
    if ( self::$instance === null ) {
      self::$instance = new self();
    }
    return self::$instance;
  }



  private function resolve_post_id( null|int|\WP_Post $post ) : int|false {
    if ( !$post ) {
      global $post;
    }

    if ( is_int( $post ) ) {
      return $post;
    }

    if ( isset( $post->ID ) ) {
      return (int) $post->ID;
    }

    return false;
  }



  /**
   * Get date display options
   *
   * @return array
   */
  public function get_date_display_options() : array {
    if ( empty( $this->options ) ) {
      $this->options = [
        'date_format'   => esc_html( Hooks::hook_filter_date_display_format() ),
        'time_format'   => esc_html( Hooks::hook_filter_time_display_format() ),
        'date_time_sep' => esc_html( Hooks::hook_filter_date_time_sep_format() ),
      ];
    }
    return $this->options;
  }



  // ==========================
  //      START DATE STUFF
  // ==========================

  public function get_start_timestamp( null|int|\WP_Post $post = null ) : int|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( $field = get_post_meta( $post_id, $this->keys['start_timestamp'], true ) ) {
      return (int) $field;
    }

    return false;
  }



  /**
   * Get formatted start date
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_start_date( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['start_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['date_format'] );
    }

    return false;
  }



  /**
   * Get formatted start time
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_start_time( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['start_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['time_format'] );
    }

    return false;
  }



  /**
   * Get formatted start date and time
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_start_date_time( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['start_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['date_format'] . $settings['date_time_sep'] . $settings['time_format'] );
    }

    return false;
  }



  // ==========================
  //       END DATE STUFF
  // ==========================



  /**
   * Get end timestamp
   *
   * @param null|int|\WP_Post $post
   * @return int|false
   */
  public function get_end_timestamp( null|int|\WP_Post $post = null ) : int|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( $field = get_post_meta( $post_id, $this->keys['end_timestamp'], true ) ) {
      return (int) $field;
    }

    return false;
  }



  /**
   * Get formatted end date
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_end_date( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['end_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['date_format'] );
    }

    return false;
  }



  /**
   * Get formatted end time
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_end_time( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['end_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['time_format'] );
    }

    return false;
  }



  /**
   * Get formatted end date and time
   *
   * @param null|int|\WP_Post $post
   * @return string|false
   */
  public function get_formatted_end_date_time( null|int|\WP_Post $post = null ) : string|false {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $settings = $this->get_date_display_options();

    if ( $field = get_post_meta( $post_id, $this->keys['end_timestamp'], true ) ) {
      $fDate = new \DateTime( '', \wp_timezone() );
      $fDate->setTimestamp( $field );
      return $fDate->format( $settings['date_format'] . $settings['date_time_sep'] . $settings['time_format'] );
    }

    return false;
  }



  // ==========================
  //       FORMATTING STUFF
  // ==========================


  /**
   * Given a timestamp, format a date string to event settings
   *
   * @param integer $timestamp
   * @return string
   */
  public function date_from_timestamp( int $timestamp ) : string {
    $settings = $this->get_date_display_options();
    $fDate    = new \DateTime( '', \wp_timezone() );
    $fDate->setTimestamp( $timestamp );
    return $fDate->format( $settings['date_format'] );
  }



  // ==========================
  //      RECURRING STUFF
  // ==========================

  /**
   * Is parent
   * 
   * @param null|int|\WP_Post $post
   * @return bool
   */
  public function is_parent( null|int|\WP_Post $post = null ) : bool {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( get_post_meta( $post_id, $this->keys['is_parent'], true ) ) {
      return true;
    }

    return false;
  }



  /**
   * Is child
   * 
   * @param null|int|\WP_Post $post
   * @return bool
   */
  public function is_child( null|int|\WP_Post $post = null ) : bool {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( get_post_meta( $post_id, $this->keys['parent_id'], true ) ) {
      return true;
    }

    return false;
  }



  /**
   * Get Recurring Child IDs
   *
   * @param null|int|\WP_Post $post
   * @return false|array
   */
  public function get_child_ids( null|int|\WP_Post $post = null ) : false|array {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    $db_helpers = DatabaseHelpers::get_instance();
    $children = $db_helpers->get_recurring_child_ids($post_id);

    if ( $children && is_array($children) && !empty($children) ) {
      return $children;
    }

    return false;
  }



  /**
   * Check if the post is a child with custom content (i.e. content that differs from the parent)
   *
   * @param null|int|\WP_Post $post
   * @return bool
   */
  public function is_child_custom( null|int|\WP_Post $post = null ) : bool {
    if ( !$post_id = $this->resolve_post_id( $post ) ) {
      return false;
    }

    if ( get_post_meta( $post_id, $this->keys['custom_content'], true ) ) {
      return true;
    }

    return false;
  }

}
