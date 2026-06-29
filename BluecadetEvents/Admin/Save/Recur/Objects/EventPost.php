<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;

class EventPost {

  public function __construct(
    public string $modified = '',
    public int $post_id = 0,
    public string $post_slug = '',
    public int $event_start = 0,
    public int $event_end = 0,
    public int $parent_ID = 0,
    public bool $is_parent = false,
  ) {
    
  }
}