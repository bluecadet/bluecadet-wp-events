<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor\FormContent;
use BluecadetEvents\Plugin;

/**
 * Metabox form component kinda-sorta-api thing
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class MetaBoxPatterns {
  private \WP_Post $post;

  public function __construct(\WP_Post $post) {
    $this->post = $post;
  }

  public function BasicCheckbox(string $id, bool $checked, string $label, bool $flip = false, bool $asToggle = true) {
    ?>
      <div class="bc-event-dates__checkbox-group bc-event-dates__input-row<?php echo $flip ? ' bc-event-dates__checkbox-group--flip' : ''; ?> js-bc-event-basic-checkbox">
        <?php if ( $flip ) { ?>
          <label class="bc-event-dates__label <?php echo $asToggle ? 'bc-event-dates__checkbox--toggle-label' : 'bc-event-dates__checkbox-label'; ?>" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
        <?php } ?>
        <input
          class="<?php echo $asToggle ? 'bc-event-dates__checkbox--toggle' : 'bc-event-dates__checkbox'; ?>"
          type="checkbox"
          id="<?php echo esc_attr( $id ); ?>"
          <?php echo $checked ? 'checked' : ''; ?>
        />
        <?php if ( !$flip ) { ?>
          <label class="bc-event-dates__label <?php echo $asToggle ? 'bc-event-dates__checkbox--toggle-label' : 'bc-event-dates__checkbox-label'; ?>" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
        <?php } ?>
      </div>
    <?php
  }



  public function StartEndDate(string $startDateKey, string $startTimeKey, string $startTimestampKey, string $endDateKey, string $endTimeKey, string $endTimestampKey) {
    ?>
      <div class="bc-event-dates__input-row">
        <label class="bc-event-dates__label" for="<?php echo esc_attr( $startDateKey ); ?>"><?php echo esc_html__( 'Start Date', 'bluecadet-events' ); ?></label>
        <input
          class="bc-event-dates__date js-bc-event-dates-input"
          type="date"
          id="<?php echo esc_attr( $startDateKey ); ?>"
          name="<?php echo esc_attr( $startDateKey ); ?>"
          value="<?php echo esc_attr( get_post_meta( $this->post->ID, $startDateKey, true ) ); ?>"
        />
        <input
          class="bc-event-dates__time js-bc-event-dates-input"
          type="time"
          id="<?php echo esc_attr( $startTimeKey ); ?>"
          name="<?php echo esc_attr( $startTimeKey ); ?>"
          value="<?php echo esc_attr( get_post_meta( $this->post->ID, $startTimeKey, true ) ); ?>"
        />
      </div>
      <div class="bc-event-dates__input-row">
        <label class="bc-event-dates__label" for="<?php echo esc_attr( $endDateKey ); ?>"><?php echo esc_html__( 'End Date', 'bluecadet-events' ); ?></label>
        <input
          class="bc-event-dates__date js-bc-event-dates-input"
          type="date"
          id="<?php echo esc_attr( $endDateKey ); ?>"
          name="<?php echo esc_attr( $endDateKey ); ?>"
          value="<?php echo esc_attr( get_post_meta( $this->post->ID, $endDateKey, true ) ); ?>"
        />
        <input
          class="bc-event-dates__time js-bc-event-dates-input"
          type="time"
          id="<?php echo esc_attr( $endTimeKey ); ?>"
          name="<?php echo esc_attr( $endTimeKey ); ?>"
          value="<?php echo esc_attr( get_post_meta( $this->post->ID, $endTimeKey, true ) ); ?>"
        />
      </div>
    <?php
  }



}

