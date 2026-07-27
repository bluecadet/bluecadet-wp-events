<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;

class EventClone {
  // Mutable builder target: EventCloneBuilder populates the fields below after
  // construction (and set_dates() rewrites them per occurrence), so only the
  // parent id is set-once.
  public int|false $child_id = false;
  public array $post = [];
  public array $meta = [];
  public array $taxonomies = [];
  public int|false $thumbnail = false;
  public \DateTime $start_date;
  public \DateTime $end_date;
  public string|false $event_slug = false;
  public array $misc = [];

  public function __construct(
    public readonly int $parent_id,
  ) {}

}