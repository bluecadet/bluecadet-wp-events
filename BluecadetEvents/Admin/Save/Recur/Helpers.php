<?php

namespace BluecadetEvents\Admin\Save\Recur;

class Helpers {

  public static function timestamp_to_date_object(int $timestamp, \DateTimeZone $timezone) : \DateTime {
    $date = new \DateTime();
    $date->setTimestamp($timestamp);
    $date->setTimezone($timezone);
    return $date;
  }


  public static function get_date_diff(\DateTime $start, \DateTime $end) {
    $interval = $start->diff($end);
    return $interval;
  }


  public static function add_date_diff_to_date(\DateTime $start, \DateTime $end, \DateTime $date) : \DateTime {
    $interval = $start->diff($end);
    $new_date = clone $date;
    $new_date->add($interval);
    return $new_date;
  }

}