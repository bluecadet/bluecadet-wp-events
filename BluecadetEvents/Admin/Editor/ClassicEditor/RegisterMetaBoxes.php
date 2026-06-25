<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor;
use BluecadetEvents\Admin\Editor\ClassicEditor\FormContent;
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
class RegisterMetaBoxes {

  public function __construct() {
    Settings::__init();
    
    new EditorScreen;

    add_action( 'add_meta_boxes', [$this, 'register_meta_boxes'], 10, 2 );  
  }

  

  public function register_meta_boxes(string $post_type, \WP_Post|int|null $post) {

    if ( ! $post instanceof \WP_Post ) {
        return;
    }

    if ( use_block_editor_for_post( $post ) ) {
        return;
    }

    if ( !in_array( $post_type, [
      Settings::$events_machine_name, Settings::$locations_machine_name, Settings::$series_machine_name
    ] ) ) {
      return;
    }

    // Events
    add_meta_box(
			Settings::$events_machine_name . '-metadata',
			'Event Details',
      [$this, 'handle_events_metabox'],
			Settings::$events_machine_name,
      'primary',
      'high',
		);
  }


  public function handle_events_metabox() {
    $is_child_event = DatabaseHelpers::get_instance()->is_recurring_child(get_the_ID());
    ?>
    <div class="bc-event-dates bc-event-dates--classic">
      <div class="bc-event-dates__container">
        <div class="bc-event-details bc-event__content-section">
          <?php if ( $is_child_event ) {
            new FormContent\ChildEvent();
          } else {
            new FormContent\Event();
          } ?>
        </div>
      </div>
    </div>
    <?php
    
  }
  

}