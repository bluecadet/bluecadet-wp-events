<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;

class EventClone {
  public int $parent_id;
  public int|false $child_id = false;
  public array $post = [];
  public array $meta = [];
  public array $taxonomies = [];
  public int|false $thumbnail = false;
  public \DateTime $start_date;
  public \DateTime $end_date;
  public string|false $event_slug = false;
  public array $misc = [];

  public function __construct(int $parent_id) {
    $this->parent_id = $parent_id;
  }

}