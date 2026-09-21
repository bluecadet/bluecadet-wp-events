<?php

namespace BluecadetEvents\Admin\Save\Recur\Background;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Utils\Logger;
use BluecadetEvents\Admin\Save\Recur\Objects\EventClone;
use BluecadetEvents\Admin\Save\EventsSaveAction;
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

    // This class is the sole writer of child rows (via upsert_child). Suppress
    // the generic wp_after_insert_post save path for the duration so it does not
    // also write/clobber the child's bc_events row.
    EventsSaveAction::$generating = true;

    try {
      if ( $this->item->child_id ) {
        $this->update_only();
      } else {
        $this->update_or_add();
      }
    } 
    finally {
      EventsSaveAction::$generating = false;
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
    $this->DB_HELPERS->update_child_modified($this->item->child_id);
    
  }



  private function update_or_add() : void {  

    // Reuse the post that already holds this occurrence, so regenerating a series
    // keeps unchanged dates on their existing post id and permalink instead of
    // replacing every occurrence with a fresh post.
    $reuse_id = $this->item->event_slug
      ? $this->DB_HELPERS->check_child_events_for_date_slug((string) $this->item->event_slug, $this->item->parent_id)
      : false;

    if ( $reuse_id ) {
      $this->copy_to_id       = $reuse_id;
      $this->item->post['ID'] = $reuse_id;

      $this->insert_post();
      $this->DB_HELPERS->upsert_child($this->item, $reuse_id);
    } else {
      $this->add_post();
    }

    $this->copy_data();
    
  }


  private function add_post() {
    $this->copy_to_id = $this->insert_post();
    $this->DB_HELPERS->upsert_child($this->item, $this->copy_to_id);
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

    // The post is already inserted/updated by insert_post() in every path that
    // reaches here; copying meta/taxonomies is all that's left. A second
    // wp_insert_post() here (with no ID in the add path) would create a duplicate
    // published post with no meta and no bc_events row.

    // Update Meta
    foreach ( $this->item->meta as $key => $value ) {
      update_post_meta( $this->copy_to_id, $key, $value );
    }

    foreach ( $this->item->taxonomies as $taxonomy => $terms ) {
      wp_set_object_terms( $this->copy_to_id, $terms, $taxonomy );
    }

    update_post_meta( $this->copy_to_id, 'bc_events_is_child', 1 );
  }




}