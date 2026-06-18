<?php

namespace BluecadetEvents\Admin\Meta\Locations;
use BluecadetEvents\Admin\Meta\Locations\MetaKeys;
use BluecadetEvents\Plugin\Settings;

class RegisterMeta {
  private $keys;

  public function __construct() {
    $this->keys = MetaKeys::get_keys();
    add_action( 'init', [$this, 'register_meta'] );
  }

  public function register_meta() {
    $this->register_event_meta();
  }

  private function register_event_meta() {
    $meta_type = Settings::$locations_machine_name;

    $auth = static fn() => current_user_can( 'edit_posts' );

		$string_args = [
			'type'          => 'string',
			'single'        => true,
			'show_in_rest'  => true,
			'default'       => '',
			'auth_callback' => $auth,
		];

		register_post_meta( $meta_type, $this->keys['address'], array_merge( $string_args, [
			'description' => __( 'Location address', 'basecadet' ),
		] ) );

    register_post_meta( $meta_type, $this->keys['description'], array_merge( $string_args, [
			'description' => __( 'Location description', 'basecadet' ),
		] ) );

    register_post_meta( $meta_type, $this->keys['website'], array_merge( $string_args, [
			'description' => __( 'Location website', 'basecadet' ),
		] ) );

  }

  // private function register_location_meta() {
  //   $meta_ns = Settings::$locations_meta_ns;

  //   // Location Details
  //   register_post_meta( Plugin\Settings::$locations_machine_name, $meta_ns . 'location_details', [
  //     'type' => 'array',
  //     'single' => true,
  //     'show_in_rest' => true,
  //     'auth_callback' => function() {
  //       return current_user_can('edit_posts');
  //     }
  //   ] );
  // }

  // private function register_series_meta() {
  //   $meta_ns = Settings::$series_meta_ns;

  //   // Series Details
  //   register_post_meta( Plugin\Settings::$series_machine_name, $meta_ns . 'series_details', [
  //     'type' => 'array',
  //     'single' => true,
  //     'show_in_rest' => true,
  //     'auth_callback' => function() {
  //       return current_user_can('edit_posts');
  //     }
  //   ] );
  // }

  // private function register_contact_meta() {
  //   $meta_ns = Settings::$contact_meta_ns;

  //   // Contact Details
  //   register_post_meta( Plugin\Settings::$contact_machine_name, $meta_ns . 'contact_details', [
  //     'type' => 'array',
  //     'single' => true,
  //     'show_in_rest' => true,
  //     'auth_callback' => function() {
  //       return current_user_can('edit_posts');
  //     }
  //   ] );
  // }
}
