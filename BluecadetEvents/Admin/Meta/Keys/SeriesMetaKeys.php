<?php

namespace BluecadetEvents\Admin\Meta\Keys;
use BluecadetEvents\Plugin\Settings;

class SeriesMetaKeys extends AbstractMetaKeys {
	
  protected function generate_keys(): array {
    return [
      'display_name' => Settings::$series_meta_ns . 'display_name',
      'plural_name' => Settings::$series_meta_ns . 'plural_name',
      'singular_name' => Settings::$series_meta_ns . 'singular_name',
			'description' => Settings::$series_meta_ns . 'description',
    ];
  }
}