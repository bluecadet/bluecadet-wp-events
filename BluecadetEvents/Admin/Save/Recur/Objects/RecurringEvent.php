<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;
use BluecadetEvents\Admin\Save\Recur\Objects\EventClone;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;


class RecurringEvent {
  
  /**
   * Parent/Saved Post ID
   *
   * @var integer
   */
  public readonly int $parent_post_id;

  /**
   * Parent/Saved Post Object
   *
   * @var \WP_Post
   */
  public readonly \WP_Post $parent_post;

  /**
  * Whether this is an update to an existing post or a new post
  *
  * @var boolean
  */
  public readonly bool $parent_update;

  /**
   * Timezone for the event
   *
   * @var \DateTimeZone
   */
  public readonly \DateTimeZone $timezone;

  /**
   * Meta keys for the event
   *
   * @var array
   */
  public readonly array $keys;

  /**
   * Whether the event is recurring
   *
   * @var boolean
   */
  public bool $is_recurring;

  /**
   * Whether the event was previously recurring
   *
   * @var boolean
   */
  public bool $is_recurring_was;

  /**
   * Whether the event was previously recurring
   *
   * @var null|string
   */
  public null|string $recurring_delete;

  /**
   * Whether the event is a parent event
   *
   * @var boolean|array
   */
  public bool|array $is_parent = false;

  /**
   * Master post id when this event is a recurring child, false otherwise.
   *
   * @var int|false
   */
  public int|false $is_child = false;

  /**
   * Frequency arguments for the event
   *
   * @var array
   */
  public array $freq_args = [];

  /**
   * Previous recurrence strategy
   *
   * @var array
   */
  public array $recur_strategy_was = [];

  /**
   * All meta data for the event
   *
   * @var array
   */
  public array $all_meta;

  /**
   * Event meta data
   *
   * @var array
   */
  public array $event_meta = [];

  /**
   * Recurring dates for the event
   *
   * @var RecurringEventDate[]
   */
  public array $recurring_dates = [];

  /**
   * Omitted dates for the event
   *
   * @var array
   */
  public array $omit_dates = [];


  
  /**
   * Construct
   *
   * @param integer $id - Parent/Saved Post ID
   * @param \WP_Post $post - Parent/Saved Post Object
   * @param boolean $update - Whether this is an update to an existing post or a new post
   */
  public function __construct(int $id, \WP_Post $post, bool $update) {
    $DB_HELPERS = DatabaseHelpers::get_instance();

    $this->parent_post_id = $id;
    $this->parent_post = $post;
    $this->parent_update = $update;
    $this->timezone = wp_timezone();
    $this->keys = EventsMetaKeys::get_keys();
    $this->is_recurring = get_post_meta($this->parent_post_id, $this->keys['is_recurring'], true);
    $this->is_recurring_was = get_post_meta($this->parent_post_id, $this->keys['is_recurring_was'], true);
    $this->recurring_delete = get_post_meta($this->parent_post_id, $this->keys['remove_recurring'], true);
    $this->is_parent = $DB_HELPERS->is_recurring_parent($id);
    $this->is_child = $DB_HELPERS->is_recurring_child($id);
  }


  /**
   * Get meta value by MetaKeys key name
   *
   * @param string $key
   * @return string|array|boolean
   */
  public function get_meta(string $key): string|array|bool {
    // Check if using a short key
    $meta_key = isset($this->keys[$key]) ? $this->keys[$key] : $key;


    if ( isset($this->event_meta[$meta_key]) ) {
      // No Value or empty  
      if ( is_array($this->event_meta[$meta_key]) && empty($this->event_meta[$meta_key]) ) {
        return false;
      }

      // Boolean
      if ( $this->event_meta[$meta_key] == 1 ) {
        return true;
      }

      // String or array value
      return $this->event_meta[$meta_key];
    }

    return false;
  }

}