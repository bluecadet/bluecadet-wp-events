<?php

namespace BluecadetEvents\Admin\Save\Recur\Objects;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;

class RecurringEventDate {

  public \DateTime $start_date;
  public \DateTime $end_date;
  public string $slug;


  public function __construct(\DateTime $start_date, \DateTime $end_date, string $slug) {
    $this->start_date = $start_date;
    $this->end_date = $end_date;
    $this->slug = $slug;
  }

}