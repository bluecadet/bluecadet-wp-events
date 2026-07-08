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


}