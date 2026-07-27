<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;

class EventPost {

  public function __construct(
    public readonly string $modified = '',
    public readonly int $post_id = 0,
    public readonly string $post_slug = '',
    public readonly int $event_start = 0,
    public readonly int $event_end = 0,
    public readonly int $parent_ID = 0,
    public readonly bool $is_parent = false,
  ) {

  }
}