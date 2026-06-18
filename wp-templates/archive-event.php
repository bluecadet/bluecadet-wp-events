<?php

global $wp_query;

$view   = bce__get_view();
$status = isset($wp_query->bce_status) ? ucfirst($wp_query->bce_status) . ' ' : '';

get_header();

?>

<div class="bce-header">
  <div class="bce-container">
    <h1 class="bce-archive-title"><?= $status ?>Events</h1>
  </div>
</div>

<?php
  $filter_template = bce__get_template_part('archive/filters.php');
?>

<div class="bce-loader bce-hide-on-fetch" aria-hidden="true">
  <div id="bce-loader-title" class="bce-loader__title">Loading...</div>
</div>

<div class="bce-container">
  <?php
    if ( $view === 'week' ) {
      $template = bce__get_template_part('archive/week-view.php');
    } elseif ( $view === 'day' ) {
      $template = bce__get_template_part('archive/day-view.php');
    } elseif ( $view === 'month' ) {
      $template = bce__get_template_part('archive/month-view.php');
    } else {
      $template = bce__get_template_part('archive/list-view.php');
    }
  ?>
</div>

<?php get_footer(); ?>
