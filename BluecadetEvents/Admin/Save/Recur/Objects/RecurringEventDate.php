<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;

class RecurringEventDate {

  public function __construct(
    public readonly \DateTime $start_date,
    public readonly \DateTime $end_date,
    public readonly string $slug,
  ) {}

}