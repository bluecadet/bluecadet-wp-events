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

    $this->field( $meta_type, 'address', $this->string_args, [
      'description' => __( 'Location address', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'description', $this->string_args, [
      'description' => __( 'Location description', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'website', $this->string_args, [
      'description' => __( 'Location website', 'basecadet' ),
    ] );

  }
}
