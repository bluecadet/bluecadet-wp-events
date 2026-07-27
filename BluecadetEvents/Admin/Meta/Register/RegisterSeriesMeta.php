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

    $this->field( $meta_type, 'display_name', $this->string_args, [
      'description' => __( 'Series display name', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'plural_name', $this->string_args, [
      'description' => __( 'Series plural name', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'singular_name', $this->string_args, [
      'description' => __( 'Series singular name', 'basecadet' ),
    ] );

    $this->field( $meta_type, 'description', $this->string_args, [
      'description' => __( 'Series description', 'basecadet' ),
    ] );

  }
}
