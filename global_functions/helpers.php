<?php

function bce__get_formatted_start_date($post = null) {
  $getter = BluecadetEvents\Helpers\TemplateHelpers::getInstance();
  return $getter->get_formatted_start_date($post);
}


function bce__get_formatted_end_date($post = null) {
  $getter = BluecadetEvents\Helpers\TemplateHelpers::getInstance();
  return $getter->get_formatted_end_date($post);
}



/**
 * Get date
 *
 * @param integer $timestamp
 * @return void
 */
function bce__date_from_timestamp(int $timestamp) {
  $getter = BluecadetEvents\Helpers\TemplateHelpers::getInstance();
  return $getter->date_from_timestamp($timestamp);
}
