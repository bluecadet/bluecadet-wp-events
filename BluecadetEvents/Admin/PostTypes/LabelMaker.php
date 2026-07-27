<?php

namespace BluecadetEvents\Admin\PostTypes;

/**
 * Generate Labels for Custom Post Types and Taxonomies
 * ----------------------------------------------------
 *
 * @package BluecadetEvents
 * @since  1.0.0
 */

class LabelMaker {

  /**
   * @param string $name
   * @param string $singular
   * @param string $plural
   * @return array Label array neecessary for CPT
   */
  public static function create_labels(string $name, string | null | false $singular = false, string | null | false $plural = false) : array {
    $singular = $singular ? $singular : $name;
    $plural = $plural ? $plural : $name;

    return [
      'name'                  => $name,
      'singular_name'         => $singular,
      'menu_name'             => $name,
      'name_admin_bar'        => $name,
      'parent_item_colon'     => 'Parent ' . $singular . ':',
      'all_items'             => 'All ' . $plural,
      'add_new_item'          => 'Add New ' . $singular,
      'add_new'               => 'Add New',
      'new_item'              => 'New ' . $singular,
      'edit_item'             => 'Edit ' . $singular,
      'update_item'           => 'Update ' . $singular,
      'view_item'             => 'View ' . $singular,
      'search_items'          => 'Search ' . $singular,
      'not_found'             => 'Not found',
      'not_found_in_trash'    => 'Not found in Trash',
      'items_list'            => $plural . ' list',
      'items_list_navigation' => $plural . ' list navigation',
      'filter_items_list'     => 'Filter ' . $plural . ' list'
    ];
  }

}
