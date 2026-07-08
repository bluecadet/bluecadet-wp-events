<?php

namespace BluecadetEvents\Admin\Meta\Keys;
use BluecadetEvents\Plugin\Settings;

abstract class AbstractMetaKeys {

  /**
   * Abstract Instances
   * 
   * @var array<string, static> $instances
   */
  private static array $instances = [];
  
  /**
   * Array where the key is a 'nicename' or abbreviation and the value is the actual meta key
   * 
   * Example: 
   * [
   *    'nice_name' => 'some_meta_key_thats_really_long_not_a_nice_name',
   *    'thing' => 'long_namespace_thing',
   *    ...
   * ]
   *
   * @var array
   */
  protected array $keys;

  final private function __construct() {
    Settings::init();
    $this->keys = $this->generate_keys();
  }

  abstract protected function generate_keys(): array;

  public static function get_instance(): static {
    return self::$instances[static::class] ??= new static();
  }


  /**
   * Call this method to get the array of keys
   *
   * @return array
   */
  public static function get_keys(): array {
    return static::get_instance()->keys;
  }
}