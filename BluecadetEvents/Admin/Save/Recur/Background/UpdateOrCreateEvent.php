<?php

namespace BluecadetEvents\Admin\Save\Recur\Background;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Utils\Logger;
use BluecadetEvents\Admin\Save\Recur\Objects\EventClone;
use BluecadetEvents\Plugin\Settings;

/**
 * Events and Event Locations forms
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class UpdateOrCreateEvent {

  private DatabaseHelpers $DB_HELPERS;
  private object $item;
  private false|int $copy_to_id = false;

  public function __construct(object $item) {
    $this->DB_HELPERS = DatabaseHelpers::get_instance();
    $this->item = $item;
    
    if ( $this->item->child_id ) {
      $this->update_only();
    } else {
      $this->update_or_add();
    }
  }


  /**
   * Only update a post
   *
   * @return void
   */
  private function update_only() : void {
    if ( !$this->item->child_id ) {
      Logger::log('No child ID found for update. Exiting update process.');
      return;
    }

    $this->copy_to_id = $this->item->child_id;

    // Set the ID to the existing post so that wp_insert_post will update instead of create
    $this->item->post['ID'] = $this->item->child_id;
    $this->insert_post();
    $this->copy_data();

    // Update modified date
    $this->DB_HELPERS->update_existing_recurring_child_modified($this->item->child_id);
    
  }



  private function update_or_add() : void {  

    if ( $this->item->event_slug ) {
      $check_slugs = $this->DB_HELPERS->check_child_events_for_date_slug($this->item->event_slug);

      if ( is_array($check_slugs) && !empty($check_slugs) ) {
        $this->copy_to_id = $check_slugs[0]->child_ID;
        $this->item->post['ID'] = $this->copy_to_id;

        // Update the post
        $this->insert_post();
        $this->DB_HELPERS->update_existing_recurring_child($this->item, $this->copy_to_id);

      } else {
        $this->add_post();  
      }
      
    } else {
      $this->add_post();
    }

    $this->copy_data();
    
  }


  private function add_post() {
    $this->copy_to_id = $this->insert_post();
    $this->DB_HELPERS->write_new_recurring_child($this->item, $this->copy_to_id);
  }



  private function insert_post() : int|\WP_Error {
    if ( isset($this->item->post['ID']) ) {
      $do_not_override = get_post_meta( $this->item->post['ID'], Settings::$events_meta_ns . 'child_deny_override', true );
  
      if ( $do_not_override ) {
        $existing = get_post( $this->item->post['ID'], ARRAY_A );
        $this->item->post['post_content'] = $existing['post_content'];
      }
    }

    $post_id = \wp_insert_post( $this->item->post, true );

    if ( is_wp_error( $post_id ) ) {
      Logger::log('Error creating event: ' . $post_id->get_error_message());
      return $post_id;
    }

    return $post_id;

  }



  private function copy_data() {

    // Copy post data
    $post = \wp_insert_post( $this->item->post, true );

    if ( is_wp_error( $post ) ) {
      Logger::log('Error creating/updating event: ' . $post->get_error_message());
      return;
    }


    // Update Meta
    foreach ( $this->item->meta as $key => $value ) {
      update_post_meta( $this->copy_to_id, $key, $value );
    }

    // $taxonomies = get_object_taxonomies( Settings::$events_machine_name );


    foreach ( $this->item->taxonomies as $taxonomy => $terms ) {
      wp_set_object_terms( $this->copy_to_id, $terms, $taxonomy );
    }
  }




}