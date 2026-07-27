<?php

namespace BluecadetEvents\Admin\Meta\Register;
use BluecadetEvents\Admin\Utils\AbstractService;

abstract class AbstractRegisterMeta extends AbstractService {
  
  protected array $keys;

  protected \Closure $auth;

  protected array $string_args;

  protected array $int_args;

  protected array $bool_args;

  protected array $array_args;

  protected array $object_args;

  public function __construct() {
    $this->auth = static fn() => current_user_can( 'edit_posts' );

    $this->string_args = [
      'type'          => 'string',
      'single'        => true,
      'show_in_rest'  => true,
      'default'       => '',
      'auth_callback' => $this->auth,
    ];

    $this->int_args = [
      'type'          => 'integer',
      'single'        => true,
      'show_in_rest'  => true,
      'default'       => 0,
      'auth_callback' => $this->auth,
    ];

    $this->bool_args = [
      'type'          => 'boolean',
      'single'        => true,
      'show_in_rest'  => true,
      'default'       => false,
      'auth_callback' => $this->auth,
    ];

    $this->array_args = [
      'type'          => 'array',
      'single'        => true,
      'show_in_rest'  => [
        'schema' => [
          'type'  => 'array',
          'items' => [ 'type' => 'string' ],
        ],
      ],
      'default'       => [],
      'auth_callback' => $this->auth,
    ];

    $this->object_args = [
      'type'          => 'object',
      'single'        => true,
      'auth_callback' => $this->auth,
    ];
  }

  public function register(): void {}

  public function boot(): void {
    add_action( 'init', [$this, 'register_meta'] );
  }

  abstract public function register_meta() : void;

  /**
   * Register a single meta field for a post type.
   *
   * Collapses the repeated register_post_meta( ..., array_merge( $base, $extra ) )
   * pattern into one call. $base is one of the arg templates ($string_args,
   * $int_args, …); $extra overrides/adds keys such as description, default,
   * sanitize_callback, or a custom show_in_rest schema.
   *
   * @param string $meta_type Post type machine name.
   * @param string $key       Logical key name resolved via $this->keys.
   * @param array  $base      Arg template to start from.
   * @param array  $extra     Per-field overrides merged on top of $base.
   * @return void
   */
  protected function field( string $meta_type, string $key, array $base, array $extra = [] ) : void {
    register_post_meta( $meta_type, $this->keys[$key], array_merge( $base, $extra ) );
  }


}