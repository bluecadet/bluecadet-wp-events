<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
// use BluecadetEvents\Admin\Editor\ClassicEditor\MetaBoxPatterns;


/**
 * Create Metabox form components for Events post type
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class EditorScreen {

  private $post;
  private $is_recurring_child;
  private $is_recurring_parent;
  private $parent_link;


  public function __construct() {

    Settings::__init();

    add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );

    // if ( class_exists('ACF') ) {
    //   add_action('acf/validate_save_post', [$this, 'disable_acf_validation'], 10, 0);
    // }

  }


  /**
 	 * Render Custom Meta Boxes
 	 */
 	public function edit_form_after_title() {

    global $post, $wp_meta_boxes, $current_screen;

    $db_helpers                  = DatabaseHelpers::get_instance();
    $this->post                 = $post;
    $this->is_recurring_child   = $db_helpers->is_recurring_child($post->ID);
    $this->is_recurring_parent  = $db_helpers->is_recurring_parent($post->ID);

    if ( in_array( $current_screen->id, [
      Settings::$events_machine_name, Settings::$locations_machine_name, Settings::$series_machine_name
    ] ) ) {
      ?>
        <div id="bc-event-metabox-container" class="bc-event-field">
          <?php
            $this->html_before_meta_form();
            do_meta_boxes( get_current_screen(), 'primary', $post );
          ?>
        </div>
      <?php

      unset( $wp_meta_boxes[ get_post_type( $post ) ]['primary'] );
    }
  }


  private function html_before_meta_form() {
    global $post, $wp_meta_boxes;

    if ( $this->is_recurring_parent ) { ?>
      <div id="bc-notice-events-is-parent-message" class="bc-notice bc-notice--info">
        <p>This event is the data source for other recurring events.</p>
      </div>
      <?php
    }

    if ( $this->is_recurring_child ) {
      $this->parent_link = get_edit_post_link($this->is_recurring_child);
      $this->child_event_details();
    }
  }


  private function child_event_details() {

    ?>

    <div class="bc-child-event-details__notice">
      <div class="bc-child-event-details__notice-text">
        <p><strong><u>This is a child recurring event.</u></strong> Content changes made in the Parent Event will overwrite content changes made to this event.</p>
      </div>
      <div class="bc-child-event-details__notice-action">
        <p class="bc-child-event-details__notice-edit"><a class="bc-child-event-details__notice-button" href="<?= $this->parent_link ?>">Edit the "<?= get_the_title() ?>" Parent Event</a></p>
      </div>
    </div>

    <?php


  }

}