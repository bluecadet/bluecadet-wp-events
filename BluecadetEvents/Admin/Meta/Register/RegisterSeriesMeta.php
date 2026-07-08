<?php

namespace BluecadetEvents\Admin\Meta\Register;
use BluecadetEvents\Admin\Meta\Register\AbstractRegisterMeta;
use BluecadetEvents\Admin\Meta\Keys\SeriesMetaKeys;
use BluecadetEvents\Plugin\Settings;

class RegisterSeriesMeta extends AbstractRegisterMeta {

  public function register() : void {
    $this->keys = SeriesMetaKeys::get_keys();
  }

  public function register_meta() : void {
    $meta_type = Settings::$series_machine_name;

		register_post_meta( $meta_type, $this->keys['display_name'], array_merge( $this->string_args, [
			'description' => __( 'Series display name', 'basecadet' ),
		] ) );

    register_post_meta( $meta_type, $this->keys['plural_name'], array_merge( $this->string_args, [
			'description' => __( 'Series plural name', 'basecadet' ),
		] ) );

    register_post_meta( $meta_type, $this->keys['singular_name'], array_merge( $this->string_args, [
			'description' => __( 'Series singular name', 'basecadet' ),
		] ) );

    register_post_meta( $meta_type, $this->keys['description'], array_merge( $this->string_args, [
			'description' => __( 'Series description', 'basecadet' ),
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
