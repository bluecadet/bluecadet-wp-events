<?php

namespace BluecadetEvents\Admin\Save\Recur\Background;

/**
 * Events and Event Locations forms
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class BackgroundUpdateEvent extends \WP_Background_Process {

  protected $action = 'update_new_bc_event';

  protected $meta_copier;

  public function __construct() {
    parent::__construct();

    $this->meta_copier = new Clone_Meta;
  }


  /**
   * Task
   *
   * Override this method to perform any actions required on each
   * queue item. Return the modified item for further processing
   * in the next pass through. Or, return false to remove the
   * item from the queue.
   *
   * @param mixed $item Queue item to iterate over
   *
   * @return mixed
   */
  protected function task( $item ) {
    $updated_event_id = \wp_insert_post( $item['wp']  );

    if (class_exists('ACF') && isset($item['meta']['_acf_post_id']) && !empty($item['meta']['_acf_post_id'])) {
      $item['meta']['_acf_post_id'] = $updated_event_id;
    }

    Clone_Meta::copy_meta($updated_event_id, $item['meta']);

    if ( isset($item['taxonomies']) ) {
      foreach ($item['taxonomies'] as $tax => $terms) {
        $terms = \wp_set_object_terms( $updated_event_id, $terms, $tax );
      }
    }

    $db_helpers = \BluecadetEvents\Admin\Admin_Utils\Database_Helpers::get_instance();
    $db_helpers->update_recurring_child($item['parent_id'], $updated_event_id);

    $start = isset($item['meta']['_bc_events_start_timestamp']) ? $item['meta']['_bc_events_start_timestamp'] : get_post_meta($updated_event_id, '_bc_events_start_timestamp', true);
    $end = isset($item['meta']['_bc_events_end_timestamp']) ? $item['meta']['_bc_events_end_timestamp'] : get_post_meta($updated_event_id, '_bc_events_end_timestamp', true);

    if ( $start && $end ) {
      $db_helpers->update_event($updated_event_id, intval($start), intval($end), $item['wp']['post_title']);
    }

    if ( isset($item['meta']['bc_events_location']) && !empty($item['meta']['bc_events_location']) ) {
      $value  = \maybe_unserialize($item['meta']['bc_events_location']);
      $exists = $db_helpers->get_meta_type_event_ids($updated_event_id, 'location');
      foreach ($value as $id) {
        if ( $exists ) {
          if ( !in_array($id, $exists) ) {
            $db_helpers->write_meta_type_event(intval($id), 'location', $updated_event_id);
          }
        } else {
          $db_helpers->write_meta_type_event(intval($id), 'location', $updated_event_id);
        }
      }
    }

    if ( isset($item['meta']['bc_events_series']) && !empty($item['meta']['bc_events_series']) ) {
      $value  = \maybe_unserialize($item['meta']['bc_events_series']);
      $exists = $db_helpers->get_meta_type_event_ids($updated_event_id, 'series');
      if ( is_array($value) && !empty($value) ){
        foreach ($value as $id) {
          if ( $exists ) {
            if ( !in_array($id, $exists) ) {
              $db_helpers->write_meta_type_event(intval($id), 'series', $updated_event_id);
            }
          } else {
            $db_helpers->write_meta_type_event(intval($id), 'series', $updated_event_id);
          }
        }
      }
    }

    if ( isset($item['meta']['bc_events_contact']) && !empty($item['meta']['bc_events_contact']) ) {
      $value  = \maybe_unserialize($item['meta']['bc_events_contact']);
      $exists = $db_helpers->get_meta_type_event_ids($updated_event_id, 'contact');
      foreach ($value as $id) {
        if ( $exists ) {
          if ( !in_array($id, $exists) ) {
            $db_helpers->write_meta_type_event(intval($id), 'contact', $updated_event_id);
          }
        } else {
          $db_helpers->write_meta_type_event(intval($id), 'contact', $updated_event_id);
        }
      }
    }

    if ( class_exists('ACF') && isset($item['acf']) && !empty($item['acf']) ) {
      foreach ($item['acf'] as $key => $value) {
        \update_field($key, $value, $updated_event_id);
      }
    }


    // if (class_exists('ACF') && isset($item['meta']['acf'] && !empty($item['meta']['acf']))) {
    //   $exclude_acf_keys = Plugin\Hooks::hook_filter_exclude_acf_keys();

    //   if (!is_array($exclude_acf_keys) ) {
    //     $exclude_acf_keys = [];
    //   }

    //   foreach ($groups as $group) {
    //     $fields = acf_get_fields($group['key']);
    //     foreach($fields as $field) {
    //       if ( in_array($field['key'], $exclude_acf_keys) ) {
    //         $exclude_keys[] = $field['name'];
    //         $exclude_keys[] = '_' . $field['name'];
    //       }
    //     }
    //   }
    // }

    do_action('bc_events_after_update_event', $updated_event_id, $item['parent_id']);
    do_action('bc_events_after_clone_event', $updated_event_id, $item['parent_id']);

    return false;
  }

  /**
   * Complete
   *
   * Override if applicable, but ensure that the below actions are
   * performed, or, call parent::complete().
   */
  protected function complete() {
    parent::complete();

    // Show notice to user or perform some other arbitrary task...
  }

}
