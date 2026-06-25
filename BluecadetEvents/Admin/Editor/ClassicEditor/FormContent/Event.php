<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor\FormContent;
use BluecadetEvents\Admin\Editor\ClassicEditor\FormContent\MetaBoxPatterns;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Meta\MetaKeys;
use BluecadetEvents\Plugin\Hooks;


/**
 * Create Metabox form components for Events post type
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class Event {
  private MetaBoxPatterns $patterns;
  private \WP_Post $post;
  private DatabaseHelpers $DB_HELPERS;
  private bool|array $is_recurring_parent;
  private array $keys;
  private bool $use_locations;
  private bool $use_series;

  public function __construct() {
    global $post;

    $this->post = $post;
    $this->patterns = new MetaBoxPatterns($post);
    $this->DB_HELPERS = DatabaseHelpers::get_instance();  
    $this->is_recurring_parent = $this->DB_HELPERS->is_recurring_parent($this->post->ID);
    $this->keys = MetaKeys::get_keys();
    $this->use_locations = Hooks::hook_filter_use_event_locations();
    $this->use_series = Hooks::hook_filter_use_event_series();

    $this->create_form();
  }

  private function create_form() {
    
    ?>
    <div class="bc-event-details">
      
      <div class="bc-events__flex-fieldset">
        <?php
          $this->patterns->StartEndDate(
            $this->keys['start_date'],
            $this->keys['start_time'],
            $this->keys['start_timestamp'],
            $this->keys['end_date'],
            $this->keys['end_time'],
            $this->keys['end_timestamp']
          );
        ?>
      </div>

      <div class="bc-event-dates__divider"></div>

      <div class="bc-events__flex-fieldset">
        <?php
          $this->patterns->BasicCheckbox(
            $this->keys['hide_time_display'],
            get_post_meta($this->post->ID, $this->keys['hide_time_display'], true) === '1' ? true : false,
            'Hide Time Display'
          );

          $this->patterns->BasicCheckbox(
            $this->keys['hide_end_time_display'],
            get_post_meta($this->post->ID, $this->keys['hide_end_time_display'], true) === '1' ? true : false,
            'Hide End Time Display'
          );
        ?>
        
      </div>
    </div>

    <?php 
      if ( $this->use_locations ) {
        
      }

      if ( $this->use_series ) {
        
      }
    ?>

  }


}