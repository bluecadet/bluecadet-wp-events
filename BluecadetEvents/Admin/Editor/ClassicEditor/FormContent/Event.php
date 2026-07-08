<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor\FormContent;
use BluecadetEvents\Admin\Editor\ClassicEditor\FormContent\MetaBoxPatterns;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;
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
    $this->keys = EventsMetaKeys::get_keys();
    $this->use_locations = Hooks::hook_filter_use_event_locations();
    $this->use_series = Hooks::hook_filter_use_event_series();

    $this->create_form();
  }

  /**
   * Render the classic editor form. Mirrors the non-child path of
   * editor/blocks/src/event-dates/edit.js: an Event Details section, optional
   * Locations / Series pickers, and the Recurring section.
   */
  private function create_form() {
    wp_nonce_field( 'bc_save_meta', 'bc_meta_nonce' );

    $this->render_managed_keys();

    // Validation notices are rendered into this container by JS, mirroring the
    // block editor's <Validation /> (which locks post saving and computes
    // errors). Empty until the JS evaluates the current state.
    ?>
    <div class="bc-event-dates__validation js-bc-validation"></div>

    <?php
    // $this->patterns->SectionToggle( __( 'Event Details', 'bluecadet-events' ), 'h2', null, true );

    // Single content sibling so the Event Details toggle collapses everything
    // below it as one unit (mirrors edit.js `!isCondensed && <>…</>`).
    ?>
    <div class="bc-event-dates__details-content">
      <?php

        $this->patterns->EventDetails();

        if ( $this->use_locations ) {
          $this->patterns->EventLocations();
        }

        if ( $this->use_series ) {
          $this->patterns->EventSeries();
        }

        $this->patterns->Recurring();
      ?>
    </div>
    <?php
  }


  /**
   * Declare which meta keys this form is responsible for, so the save handler
   * (Admin\Save\Events) knows a missing value means "removed" and can reset it
   * to its default — instead of leaving stale data. Must stay in sync with the
   * sections rendered in create_form(); the conditional location/series keys
   * are only declared when their sections are shown.
   */
  private function render_managed_keys() {
    $managed = [
      // EventDetails
      $this->keys['start_date'],
      $this->keys['start_time'],
      $this->keys['start_timestamp'],
      $this->keys['end_date'],
      $this->keys['end_time'],
      $this->keys['end_timestamp'],
      $this->keys['hide_time_display'],
      $this->keys['hide_end_time_display'],
      // Recurring
      $this->keys['is_recurring'],
      $this->keys['use_frequency'],
      $this->keys['freq'],
      $this->keys['freq_days'],
      $this->keys['freq_mo_schedule'],
      $this->keys['freq_mo_day'],
      $this->keys['freq_mo_date'],
      $this->keys['freq_end_type'],
      $this->keys['freq_end_date'],
      $this->keys['freq_end_after_x'],
      $this->keys['custom_occurrences'],
      $this->keys['omissions'],
      $this->keys['remove_recurring'],
    ];

    if ( $this->use_locations ) {
      $managed[] = $this->keys['location_ids'];
    }

    if ( $this->use_series ) {
      $managed[] = $this->keys['series_ids'];
    }

    foreach ( $managed as $key ) {
      printf( '<input type="hidden" name="bc_managed_keys[]" value="%s" />', esc_attr( $key ) );
    }
  }


}