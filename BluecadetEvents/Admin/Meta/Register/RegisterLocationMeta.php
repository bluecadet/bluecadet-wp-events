<?php

namespace BluecadetEvents\Admin\Meta\Register;
use BluecadetEvents\Admin\Meta\Register\AbstractRegisterMeta;
use BluecadetEvents\Admin\Meta\Keys\LocationMetaKeys; 
use BluecadetEvents\Plugin\Settings;

class RegisterLocationMeta extends AbstractRegisterMeta {
  
  public function register() : void {
    $this->keys = LocationMetaKeys::get_keys();
  }

  public function register_meta() : void {
    $meta_type = Settings::$locations_machine_name;

		register_post_meta( $meta_type, $this->keys['address'], array_merge( $this->string_args, [
			'description' => __( 'Location address', 'basecadet' ),
		] ) );

    register_post_meta( $meta_type, $this->keys['description'], array_merge( $this->string_args, [
			'description' => __( 'Location description', 'basecadet' ),
		] ) );

    register_post_meta( $meta_type, $this->keys['website'], array_merge( $this->string_args, [
			'description' => __( 'Location website', 'basecadet' ),
		] ) );

  }
}
