<?php

namespace BluecadetEvents\Admin\Meta\Keys;
use BluecadetEvents\Plugin\Settings;

class LocationMetaKeys extends AbstractMetaKeys {
	

  protected function generate_keys(): array {
    return [
      'address' => Settings::$locations_meta_ns . 'address',
      'description' => Settings::$locations_meta_ns . 'description',
      'website' => Settings::$locations_meta_ns . 'website',
    ];
  }

	

}
  