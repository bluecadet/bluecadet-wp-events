<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor\Forms;
use BluecadetEvents\Plugin;

/**
 * Metabox form component kinda-sorta-api thing
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class MetaBoxPatternsOld {

  protected static \WP_Post $post;
  protected static string $label_require;

  protected static function setup(\WP_Post $post_object) {
    self::$post = $post_object;
    self::$label_require = '<span class="bc-events__required-label">*</span>';
  }



  /**
   * Date Time field group
   *
   * @param string $dateKey
   * @param string $timeKey
   * @param string $label_prefix
   * @return html
   */
  protected static function date_time_group(string $dateKey, string $timeKey, string $label_prefix) {
    $date_value = get_post_meta( self::$post->ID, $dateKey, true );
    $time_value = get_post_meta( self::$post->ID, $timeKey, true );

    $tz  = \wp_timezone();
    $now = new \DateTime('now', $tz);

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__datetime-group js-datetime-<?= strtolower($label_prefix) ?>">
      <div class="bc-events-metabox__datetime-group-date">
        <label class="bc-events-metabox__form-label" for="<?= $dateKey ?>_display"><?= $label_prefix ?> Date<?= self::$label_require ?></label>
        <?php self::datepicker_wrapper($dateKey, $date_value, $label_prefix . ' Date'); ?>
      </div>
      <div class="bc-events-metabox__datetime-group-time bc-events-metabox__timepicker-wrapper">
        <label class="bc-events-metabox__form-label" for="<?= $timeKey ?>"><?= $label_prefix ?> Time<?= self::$label_require ?></label>
        <input
          id="<?= $timeKey ?>"
          name="<?= $timeKey ?>"
          type="time"
          value="<?= $time_value ?>"
          required
          pattern="[0-9]{2}:[0-9]{2}"
        >
        <div class="bc-events-metabox__error bc-events-metabox__error--time">
          <?= $label_prefix ?> Time is required and should be formatted as HH:MM am/pm.
        </div>
        <div class="bc-events-metabox__description bc-events-metabox__description--time">
          format as <?= $now->format('h:i a') ?>
        </div>
      </div>
    </div>
    <?php
  }



  /**
   * Date Time field group
   *
   * @param string $dateKey
   * @param string $timeKey
   * @param string $label_prefix
   * @return html
   */
  protected static function date_time_full_group(string $startDateKey, string $startTimeKey, string $endDateKey, string $endTimeKey) {
    $start_date_value = get_post_meta( self::$post->ID, $startDateKey, true );
    $start_time_value = get_post_meta( self::$post->ID, $startTimeKey, true );
    $end_date_value = get_post_meta( self::$post->ID, $endDateKey, true );
    $end_time_value = get_post_meta( self::$post->ID, $endTimeKey, true );

    $show_end_date = $start_date_value !== $end_date_value;

    $tz  = \wp_timezone();
    $now = new \DateTime('now', $tz);

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__datetime-group-full js-datetime-full">
      <div class="bc-events-metabox__datetime-group-full-vis">
        <div class="bc-events-metabox__datetime-group-full-start-date bc-events-metabox__datetime-group-full-field">
          <label class="bc-events-metabox__form-label" for="<?= $startDateKey ?>_display">Start Date<?= self::$label_require ?></label>
          <?php self::datepicker_wrapper($startDateKey, $start_date_value, 'Start Date'); ?>
        </div>
        <div class="bc-events-metabox__datetime-group-full-start-time bc-events-metabox__datetime-group-full-field">
          <label class="bc-events-metabox__form-label" for="<?= $startTimeKey ?>">Start Time<?= self::$label_require ?></label>
          <input
            id="<?= $startTimeKey ?>"
            name="<?= $startTimeKey ?>"
            type="time"
            value="<?= $start_time_value ?>"
            required
            pattern="[0-9]{2}:[0-9]{2}"
          >
          <div class="bc-events-metabox__error bc-events-metabox__error--time">
            Start Time is required and should be formatted as HH:MM am/pm.
          </div>
          <div class="bc-events-metabox__description bc-events-metabox__description--time">
            format as <?= $now->format('h:i a') ?>
          </div>
        </div>
        <div class="bc-events-metabox__datetime-group-full-end-time bc-events-metabox__datetime-group-full-field">
          <label class="bc-events-metabox__form-label" for="<?= $endTimeKey ?>">End Time<?= self::$label_require ?></label>
          <input
            id="<?= $endTimeKey ?>"
            name="<?= $endTimeKey ?>"
            type="time"
            value="<?= $end_time_value ?>"
            required
            pattern="[0-9]{2}:[0-9]{2}"
          >
          <div class="bc-events-metabox__error bc-events-metabox__error--time">
            End Time is required and should be formatted as HH:MM am/pm.
          </div>
          <div class="bc-events-metabox__description bc-events-metabox__description--time">
            format as <?= $now->format('h:i a') ?>
          </div>
        </div>
        <div class="bc-events-metabox__datetime-group-full-options-show">
          <div class="bc-events-metabox__checkbox-button">
            <input id="date-time-show-options" name="date-time-show-options js-show-date-options" type="checkbox"<?php if ($show_end_date) { echo ' checked';} ?>>
            <label for="date-time-show-options" class="bc-events-metabox__form-label">
              <span class="bc-events-metabox__form-label--off">Edit End Date</span>
              <span class="bc-events-metabox__form-label--on">
                <span class="bc-events-metabox__form-label--on-content">
                  Close End Date
                </span>
              </span>
            </label>
          </div>
        </div>
      </div>
      <div id="date-time-show-panel" class="bc-events-metabox__datetime-group-full-options bc-events-metabox__datetime-group-full-field js-date-options-panel<?php if ($show_end_date) { echo ' is-visible';} ?>">
        <div class="bc-events-metabox__datetime-group-full-options-content">
          <label class="bc-events-metabox__form-label" for="<?= $endDateKey ?>_display">End Date<?= self::$label_require ?></label>
          <?php self::datepicker_wrapper($endDateKey, $end_date_value, 'End Date'); ?>
        </div>
        <div class="bc-events-metabox__datetime-group-full-options-message">
          <p><strong>Note: </strong>End date will always update to the value of Start Date whenever Start Date is changed.</p>
        </div>
      </div>
    </div>
    <?php
  }



  /**
   * Full width checkbox group
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function full_width_checkbox(string $key, string $label, string $class = '', bool $required = false, false|string $description = false) {
    $value = get_post_meta( self::$post->ID, $key, true );
    ?>
    <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--checkbox <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper bc-events-metabox__fw-checkbox-label">
        <label for="<?= $key ?>"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper bc-events-metabox__fw-checkbox-input bc-events-metabox__checkbox-toggle">
        <input id="<?= $key ?>" name="<?= $key ?>" type="checkbox" <?php if ($value && $value === 'on') { echo 'checked'; }?> value="on">
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>
    <?php
  }



  /**
   * Full width text field group
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function full_width_text_field(string $key, string $label, string $class = '', bool $required = false, false|string $description = false, false|string $default = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    if ( $default && (!$value || $value === '') ) {
      $value = $default;
    }
    ?>
    <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--text <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <input id="<?= $key ?>" name="<?= $key ?>" class="bc-events-metabox__form-input bc-events-metabox__form-input--text" type="text" value="<?= $value ?>">
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }



  /**
   * Full width text field group
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function basic_text_field(string $key, string $label, string $class = '', bool $required = false, false|string $description = false, false|string $default = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    if ( $default && (!$value || $value === '') ) {
      $value = $default;
    }
    ?>
    <div class="bc-events-metabox__group bc-events-metabox__basic-group bc-events-metabox__basic-group--text <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <input id="<?= $key ?>" name="<?= $key ?>" class="bc-events-metabox__form-input bc-events-metabox__form-input--text" type="text" value="<?= $value ?>">
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }



  /**
   * Full width text field group
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function full_width_textarea(string $key, string $label, string $class = '', bool $required = false, false|string $description = false, $rows = 4) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--textarea <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <textarea
          id="<?= $key ?>"
          name="<?= $key ?>"
          class="bc-events-metabox__form-input bc-events-metabox__form-input--textarea"
          rows="<?= $rows ?>"
        ><?= $value ?></textarea>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }


  /**
   * Full width number field group
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function full_width_number(string $key, string $label, string $class = '', bool $required = false, false|string $description = false, $after_input = false, $error = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--number <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <input id="<?= $key ?>" name="<?= $key ?>" class="bc-events-metabox__form-input bc-events-metabox__form-input--text" type="number" value="<?= $value ?>" step="1">
        <?php if ( $after_input ) { ?>
          <div class="bc-events-metabox__input-wrapper-after">
            <?= $after_input ?>
          </div>
        <?php } ?>
        <?php if ( $error ) { ?>
          <div class="bc-events-metabox__error bc-events-metabox__error--number">
            <?= $error ?>
          </div>
        <?php } ?>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>

    </div>

    <?php

  }


  /**
   * Full width text field group
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function full_width_date_field(string $key, string $label, string $class = '', bool $required = false, false|string $description = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--datepicker <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>_display" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <?php self::datepicker_wrapper($key, $value, $label, $description); ?>
      </div>
    </div>

    <?php

  }


  protected static function datepicker_wrapper($key, $value, $label, $description = false) {
    $id = 'bc-events-minical-' . rand(100, 10000);
    $format = self::get_date_formats(); // 'MM/DD/YYYY';

    ?>
    <div class="bc-events-metabox__datepicker-wrapper js-bc-datepicker-wrapper">
      <input
        class="bc-events-metabox__form-input bc-events-metabox__form-input--date js-bc-datepicker-input-visible"
        id="<?= $key ?>_display"
        name="<?= $key ?>_display"
        value=""
        data-format="<?= $format['dayjs'] ?>"
        data-loadfromvalue="<?= $key ?>"
        type="text"
        placeholder="<?= $format['display_example'] ?>"
      >
      <button type="button" class="bc-events-metabox__datepicker-toggle js-bc-datepicker-toggle" aria-controls="<?= $id ?>">
        <span class="bc-events-metabox__datepicker-toggle-icon" aria-hidden="true">
          <svg width="96" height="92" viewBox="0 0 96 92" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M83.4 6.3H73.8V4.1C73.8 2 72.1 0.300003 70 0.300003C67.9 0.300003 66.2 2 66.2 4.1V6.3H29.6V4.1C29.6 2 27.9 0.300003 25.8 0.300003C23.7 0.300003 22.1 2 22.1 4.1V6.3H12.5C5.8 6.3 0.400002 11.7 0.400002 18.4V79.6C0.400002 86.3 5.8 91.7 12.5 91.7H66.1C70.2 91.7 74.1 90.1 77 87.2L91 73.2C93.9 70.3 95.5 66.4 95.5 62.3V18.4C95.5 11.7 90.1 6.3 83.4 6.3ZM12.6 13.8H22.2V16C22.2 18.1 23.9 19.8 26 19.8C28.1 19.8 29.8 18.1 29.8 16V13.8H66.5V16C66.5 18.1 68.2 19.8 70.3 19.8C72.4 19.8 74.1 18.1 74.1 16V13.8H83.7C86.2 13.8 88.3 15.9 88.3 18.4V27.7H8V18.4C8 15.8 10.1 13.8 12.6 13.8ZM8 79.5V35.1H88V60.4H73C68.2 60.4 64.3 64.3 64.3 69.1V84.1H12.6C10.1 84.1 8 82.1 8 79.5Z" fill="currentColor"/>
          </svg>
        </span>
        <span class="u-sr-only">Open Datepicker</span>
      </button>
      <div id="<?= $id ?>" aria-expanded="false" class="bc-events-metabox__datepicker-container js-bc-datepicker-container"></div>
      <input id="<?= $key ?>" name="<?= $key ?>" type="hidden" value="<?= $value ?>" class="js-bc-datepicker-input-hidden">
      <div class="bc-events-metabox__error bc-events-metabox__error--date">
        <div class="bc-events-metabox__error--date--required">
          This field is required.
        </div>
        <div class="bc-events-metabox__error--date--format">
          The format for <?= $label ?> is entered incorrectly. It should match the <a href="https://www.php.net/manual/en/datetime.format.php" target="blank">PHP date format</a> of <?= $format['display'] ?>. Use the datepicker to select a specic date.
        </div>
      </div>
      <div class="bc-events-metabox__description bc-events-metabox__description--date">
        format as: <?= $format['display_example'] ?>
        <?php if ($description) { ?>
          <div class="bc-events-metabox__description-inner"><?= $description ?></div>
        <?php } ?>
      </div>
    </div>
    <?php
  }



  /**
   * Checkbox Button
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function checkbox_button(string $key, string $label, string $on_label, string $class = '', bool $required = false, false|string $description = false) {
    $value = get_post_meta( self::$post->ID, $key, true );
    ?>
    <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--checkbox-button <?= $class ?>">
      <div class="bc-events-metabox__checkbox-button">
        <input id="<?= $key ?>" name="<?= $key ?>" type="checkbox" <?php if ($value && $value === 'on') { echo 'checked'; }?> value="on">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label">
          <span class="bc-events-metabox__form-label--off"><?= $label ?></span>
          <span class="bc-events-metabox__form-label--on">
            <span class="bc-events-metabox__form-label--on-content">
              <span class="bc-events-metabox__form-label--on-remove"></span>
              <?= $on_label ?>
            </span>
          </span>
        </label>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php
  }


  /**
   * Full Width Checkbox Group
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function full_width_checkbox_group(string $key, string $label, array $options, string $class = '', bool $required = false, false|string $description = false, false|string $error = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <fieldset class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--checkbox-group <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <legend class="bc-events-metabox__form-label"><?= $label ?></legend>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <ul class="bc-events-metabox__checkbox-group">
          <?php
            $count = 0;
            foreach ($options as $opt_value => $label ) { ?>
              <li class="bc-events-metabox__checkbox-group-item bc-events-metabox__checkbox-toggle">
                <input
                  id="<?= $key . $count ?>"
                  name="<?= $key . '[]' ?>"
                  type="checkbox"
                  value="<?= $opt_value ?>"
                  <?php if ( $value && !empty($value) && is_array($value) && in_array($opt_value, $value) ) { echo 'checked'; } ?>
                >
                <label for="<?= $key . $count ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
              </li>
              <?php $count++;
            }
          ?>
        </ul>
        <?php if ( $error ) { ?>
          <div class="bc-events-metabox__error bc-events-metabox__error--checkbox-group">
            <?= $error ?>
          </div>
        <?php } ?>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
      </fieldset>

    <?php

  }



  /**
   * Full Width Radio Group
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function full_width_radio_group(string $key, string $label, array $options, string $class = '', bool $required = false, false|string $description = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <fieldset class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--radio-group <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <legend class="bc-events-metabox__form-label"><?= $label ?></legend>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <ul class="bc-events-metabox__radio-group">
          <?php
            $count = 1;
            foreach ($options as $opt_value => $label ) { ?>
              <li class="bc-events-metabox__radio-group-item">
                <input
                  id="<?= $key . $count ?>"
                  name="<?= $key ?>"
                  type="radio"
                  value="<?= $opt_value ?>"
                  <?php if ( $value && !empty($value) && $value === $opt_value ) { echo 'checked'; } ?>
                >
                <label for="<?= $key . $count ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
              </li>
              <?php
              $count++;
            }
          ?>
        </ul>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
      </fieldset>

    <?php

  }



  /**
   * Hidden Input
   *
   * @param string $key
   * @return html
   */
  protected static function hidden_field(string $key, string|null $value_override = null) {
    $value = $value_override ? $value_override : get_post_meta( self::$post->ID, $key, true ); ?>
    <input type="hidden" value="<?= $value ?>">

    <?php

  }



  /**
   * Full Width Select
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function full_width_select(string $key, string $label, array $options, string $class = '', bool $required = false, false|string $description = false, false|string $empty_option = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--select <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <select id="<?= $key ?>" name="<?= $key ?>">
          <?php if ( $empty_option ) { ?>
            <option value="" ><?= $empty_option ?></option>
          <?php } ?>
          <?php foreach( $options as $opt_value => $label ) { ?>
            <option value="<?= $opt_value ?>" <?php if ( $opt_value === $value ) { echo 'selected'; } ?>><?= $label ?></option>
          <?php } ?>
        </select>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }



  /**
   * Basic Width Select
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function basic_select_field(string $key, string $label, array $options, string $class = '', bool $required = false, false|string $description = false, false|string $empty_option = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__basic-group bc-events-metabox__basic-group--select <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <select id="<?= $key ?>" name="<?= $key ?>">
          <?php if ( $empty_option ) { ?>
            <option value="" ><?= $empty_option ?></option>
          <?php } ?>
          <?php foreach( $options as $opt_value => $label ) { ?>
            <option value="<?= $opt_value ?>" <?php if ( $opt_value === $value ) { echo 'selected'; } ?>><?= $label ?></option>
          <?php } ?>
        </select>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }



  /**
   * Basic Width Phone
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function basic_phone_field(string $key, string $label, string $class = '', bool $required = false, false|string $description = false, string|false $pattern = false) {
    $value = get_post_meta( self::$post->ID, $key, true );
    ?>
    <div class="bc-events-metabox__group bc-events-metabox__basic-group bc-events-metabox__basic-group--phone <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
      <input id="<?= $key ?>" name="<?= $key ?>" class="bc-events-metabox__form-input bc-events-metabox__form-input--text" type="phone" value="<?= $value ?>"<?php if ( $pattern ) { echo ' pattern=" ' . $pattern . '"';} ?>>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }



  /**
   * Basic Width Email
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function basic_email_field(string $key, string $label, string $class = '', bool $required = false, false|string $description = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__basic-group bc-events-metabox__basic-group--email <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
      <input id="<?= $key ?>" name="<?= $key ?>" class="bc-events-metabox__form-input bc-events-metabox__form-input--text" type="email" value="<?= $value ?>">
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }



  /**
   * Basic Width Url
   *
   * @param string $key
   * @param string $label
   * @param string $class
   * @param boolean $required
   * @param false|string $description
   * @return void
   */
  protected static function basic_url_field(string $key, string $label, string $class = '', bool $required = false, false|string $description = false) {
    $value = get_post_meta( self::$post->ID, $key, true );

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__basic-group bc-events-metabox__basic-group--url <?= $class ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label for="<?= $key ?>" class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
      <input id="<?= $key ?>" name="<?= $key ?>" class="bc-events-metabox__form-input bc-events-metabox__form-input--text" type="url" value="<?= $value ?>">
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }



  /**
   * Post select field
   *
   * @param string $key
   * @param string $label
   * @param array $args
   * @param false|string $description
   * @return void
   */
  protected static function post_select_field(string $key, string $label, array $args = ['post_type' => 'bc-events-locations', 'link_text' => 'Create a new location'], false|string $description = false ) {
    $value = get_post_meta( self::$post->ID, $key, true );

    if ( !is_array($value) ) {
      $value = [0];
    }

    $is_multiple = isset($args['multiple']) ? $args['multiple'] : false;
    $options = [];
    $archive_link = \get_admin_url(null, 'post-new.php?post_type=' . $args['post_type']);

    if ( isset($args['post_type']) ) {

      $query_args = ['post_type' => $args['post_type'], 'post_per_page' => -1, 'orderby' => 'title', 'order'   => 'DESC'];
      $query = new \WP_Query($query_args);
      if ( $query->have_posts() ) {
        foreach( $query->posts as $p ) {
          $options[$p->ID] = \get_the_title($p);
        }
        \wp_reset_postdata();
      }
    }

    ?>
    <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--post-select <?php if (isset($args['class']) ) { echo $args['class']; } ?>">
      <div class="bc-events-metabox__label-wrapper">
        <label <?php if (!$is_multiple ) { ?>for="<?= $key ?>" <?php } ?>class="bc-events-metabox__form-label"><?= $label ?></label>
      </div>
      <div class="bc-events-metabox__input-wrapper">
        <div class="bc-events-metabox__post-select-content">
          <div class="bc-events-metabox__post-select-selection">
            <?php if ( $is_multiple ) {
              ?>
              <div class="bc-events-metabox__repeater js-bc-repeater-parent">
                <div class="bc-events-metabox__repeater-fields js-bc-repeater-content" data-key="<?= $key ?>" data-targetinput="select">
                  <?php self::multi_post_select($key, $args, $options, $value); ?>
                </div>
                <div class="bc-events-metabox__hidden js-bc-repeater-copy">
                  <?php self::select_repeater_field($key, $args, $options, '', true); ?>
                </div>
              </div>
            <?php } else {
              self::single_post_select($key . '[0]', $args, $options, $value[0]);
            } ?>
          </div>
          <div class="bc-events-metabox__post-select-linkout">
            <a href="<?= $archive_link ?>" class="bc-events-metabox__post-select-link js-bc-create-new-link" target="blank"><?= $args['link_text'] ?></a>
          </div>
        </div>
      </div>
      <?php if ( $description ) { ?>
        <div class="bc-events-metabox__description">
          <div class="bc-events-metabox__description-inner">
            <?= $description ?>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php

  }




  private static function multi_post_select(string $key, array $args, array $options, array $value_array) {
    $count = 0;

    if ( $value_array && is_array($value_array) && !empty($value_array)) {

      foreach ($value_array as $value) {
        $key_mod = $key . '[' . $count . ']';
        self::select_repeater_field($key_mod, $args, $options, $value);
        $count++;
      }
    } else {
      $value = '';
      self::select_repeater_field($key . '[' . $count . ']', $args, $options, $value);
      $count++;
    }
  }



  private static function select_repeater_field($key, $args, $options, $value, $use_attrs = false) {
    ?>
    <div class="bc-events-metabox__repeater-field js-bc-repeater-field">
      <?php self::single_post_select($key, $args, $options, $value, $use_attrs); ?>
      <div class="bc-events-metabox__repeater-controls">
        <button type="button" class="bc-events-metabox__repeater-control bc-events-metabox__repeater-control--add js-bc-repeater-add">Add</button>
        <button type="button" class="bc-events-metabox__repeater-control bc-events-metabox__repeater-control--remove js-bc-repeater-remove">Remove</button>
      </div>
    </div>
    <?php
  }


  private static function single_post_select(string $key, array $args, array $options, string $value, bool $use_attrs = false) {
    ?>
    <select
      class="bc-events-metabox__form-post-select js-bc-events-post-select-field <?php if ( $use_attrs ) { ?>is-copy-element<?php } ?>"
      data-type="<?= $args['post_type'] ?>"
      <?php if ( !$use_attrs ) { ?>
        name="<?= $key ?>"
        id="<?= $key ?>"
      <?php } ?>
    >
      <?php if ( !empty($options) ) {
        if ( isset($args['empty_option']) ) { ?>
          <option value=""><?= $args['empty_option'] ?></option>
        <?php }
        foreach ($options as $id => $title) { ?>
          <option
            value="<?= $id ?>"
            <?php if (intval($value) === $id) {
              echo 'selected';
            } ?>
          ><?= $title ?></option>
        <?php }
      } ?>
    </select>

    <?php
  }


  protected static function add_custom_occurences(string $key, false|string $description = false) {
    $value = get_post_meta( self::$post->ID, $key, true );
    ?>
      <fieldset class="bc-events__metabox-fieldset bc-events__custom-occurences">
        <div class="bc-events-metabox__group bc-events-metabox__fw-group bc-events-metabox__fw-group--custom-occurences">
          <legend class="bc-events-metabox__label-wrapper">Custom Occurences</legend>
          <div class="bc-events__custom-occurences-content-wrapper">
            <div class="bc-events-metabox__custom-occurences">
              <ul class="bc-events-metabox__custom-occurence-list js-bc-occurence-field-list">
                <?php if ($value) {
                  foreach ($value as $idx => $values) {
                    self::occurence_field($key, false, $idx, $values['save_date'], $values['start_time'], $values['end_time']);
                  }
                } ?>
              </ul>
              <div class="bc-events-metabox__add-occurence-button">
                <button class="bc__add-occurence-button js-bc-occurence-field-add" type="button">Add Custom Occurence</button>
              </div>
              <div class="bc-events-metabox__custom-occurences-clone js-bc-occurence-clone" hidden>
                <?php self::occurence_field($key, true); ?>
              </div>
            </div>
            <?php if ( $description ) { ?>
              <div class="bc-events-metabox__description">
                <div class="bc-events-metabox__description-inner">
                  <?= $description ?>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </fieldset>
    <?php
  }


  private static function occurence_field(string $key, bool $is_clone = false, int $index = 0, string $date_value = '', string $start_time = '', string $end_time = '' ) {
    $format        = self::get_date_formats(); // 'MM/DD/YYYY';
    $name_prefix   = $key . '[' . $index . ']';
    $display_id    = $key . '_display_date_' . $index;
    $save_id       = $key . '_save_date_' . $index;
    $start_time_id = $key . '_start_time_' . $index;
    $end_time_id   = $key . '_end_time_' . $index;
    $data_prefix   = $is_clone ? 'data-' : '';

    ?>
      <li class="bc-events-metabox__occurence-field js-bc-occurence-field" data-index="<?= $index ?>">
        <div class="bc-events-metabox__occurence-field-inner">
          <div class="bc-events-metabox__occurence-field-content">
            <div class="bc-events-metabox__occurence-field-date">
              <label class="js-bc-occurence-date-label" <?= $data_prefix ?>for="<?= $display_id ?>">Date</label>
              <div class="bc-events-metabox__datepicker-wrapper js-bc-datepicker-wrapper">
                <input
                  class="bc-events-metabox__form-input bc-events-metabox__form-input--date js-bc-datepicker-input-visible"
                  <?= $data_prefix ?>id="<?= $display_id ?>"
                  <?= $data_prefix ?>name="<?= $name_prefix ?>[display_date]"
                  <?= $data_prefix ?>value=""
                  data-format="<?= $format['dayjs'] ?>"
                  data-loadfromvalue="<?= $key ?>"
                  type="text"
                  placeholder="<?= $format['display_example'] ?>"
                >
                <button type="button" class="bc-events-metabox__datepicker-toggle js-bc-datepicker-toggle" aria-controls="occurence-field-<?= $index ?>">
                  <span class="bc-events-metabox__datepicker-toggle-icon" aria-hidden="true">
                    <svg width="96" height="92" viewBox="0 0 96 92" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M83.4 6.3H73.8V4.1C73.8 2 72.1 0.300003 70 0.300003C67.9 0.300003 66.2 2 66.2 4.1V6.3H29.6V4.1C29.6 2 27.9 0.300003 25.8 0.300003C23.7 0.300003 22.1 2 22.1 4.1V6.3H12.5C5.8 6.3 0.400002 11.7 0.400002 18.4V79.6C0.400002 86.3 5.8 91.7 12.5 91.7H66.1C70.2 91.7 74.1 90.1 77 87.2L91 73.2C93.9 70.3 95.5 66.4 95.5 62.3V18.4C95.5 11.7 90.1 6.3 83.4 6.3ZM12.6 13.8H22.2V16C22.2 18.1 23.9 19.8 26 19.8C28.1 19.8 29.8 18.1 29.8 16V13.8H66.5V16C66.5 18.1 68.2 19.8 70.3 19.8C72.4 19.8 74.1 18.1 74.1 16V13.8H83.7C86.2 13.8 88.3 15.9 88.3 18.4V27.7H8V18.4C8 15.8 10.1 13.8 12.6 13.8ZM8 79.5V35.1H88V60.4H73C68.2 60.4 64.3 64.3 64.3 69.1V84.1H12.6C10.1 84.1 8 82.1 8 79.5Z" fill="currentColor"/>
                    </svg>
                  </span>
                  <span class="u-sr-only">Open Datepicker</span>
                </button>
                <div id="occurence-field-<?= $index ?>" aria-expanded="false" class="bc-events-metabox__datepicker-container js-bc-datepicker-container"></div>
                <input
                  <?= $data_prefix ?>id="<?= $key ?>_save_date_<?= $index ?>"
                  <?= $data_prefix ?>name="<?= $name_prefix ?>[save_date]"
                  <?= $data_prefix ?>value="<?= $date_value ?>"
                  type="hidden"
                  class="js-bc-datepicker-input-hidden"
                >
              </div>
            </div>

            <div class="bc-events-metabox__occurence-field-time-start">
              <label class="bc-events-metabox__form-label js-bc-occurence-start-label" <?= $data_prefix ?>for="<?= $start_time_id ?>">Start Time</label>
              <input
                <?= $data_prefix ?>id="<?= $start_time_id ?>"
                <?= $data_prefix ?>name="<?= $name_prefix ?>[start_time]"
                <?= $data_prefix ?>value="<?= $start_time ?>"
                type="time"
                class="js-bc-occurence-start"
                required
                pattern="[0-9]{2}:[0-9]{2}"
              >
            </div>

            <div class="bc-events-metabox__occurence-field-time-end">
              <label class="bc-events-metabox__form-label js-bc-occurence-end-label" <?= $data_prefix ?>for="<?= $end_time_id ?>">End Time</label>
              <input
                <?= $data_prefix ?>id="<?= $end_time_id ?>"
                <?= $data_prefix ?>name="<?= $name_prefix ?>[end_time]"
                <?= $data_prefix ?>value="<?= $end_time ?>"
                type="time"
                class="js-bc-occurence-end"
                required
                pattern="[0-9]{2}:[0-9]{2}"
              >
            </div>
          </div>
          <div class="bc-events-metabox__occurence-controls">
            <div class="bc-events-metabox__occurence-instructions">Leave time fields empty to use the default values.</div>
            <button type="button" class="bc-events-metabox__occurence-remove js-bc-occurence-field-remove">Remove Event</button>
          </div>
        </div>
      </li>
    <?php
  }



  private static function get_date_formats() {
    $php_format = Plugin\Hooks::hook_filter_date_display_format();

    $DAYJS_MATCHING = array(
      // Day
      'd' => 'DD',
      'D' => 'ddd',
      'j' => 'D',
      'l' => 'dddd',
      'N' => '',
      'S' => '',
      'w' => '',
      'z' => '',
      // Week
      'W' => '',
      // Month
      'F' => 'MMMM',
      'm' => 'MM',
      'M' => 'MMM',
      'n' => 'M',
      't' => '',
      // Year
      'L' => '',
      'o' => '',
      'Y' => 'YYYY',
      'y' => 'YY',

      // Time not needed as we are only checking dates
      'a' => '',
      'A' => '',
      'B' => '',
      'g' => '',
      'G' => '',
      'h' => '',
      'H' => '',
      'i' => '',
      's' => '',
      'u' => ''
    );

    $dayjs_format = "";

    $escaping = false;
    for($i = 0; $i < strlen($php_format); $i++) {
      $char = $php_format[$i];

      if ($char === '\\') { // PHP date format escaping character
        $i++;
        if ($escaping) {
          $dayjs_format .= $php_format[$i];
        } else {
          $dayjs_format .= '\'' . $php_format[$i];
        }
        $escaping = true;

      } else {
        if ($escaping) {
          $dayjs_format .= "'"; $escaping = false;
        }

        if ( isset($DAYJS_MATCHING[$char]) ) {
          $dayjs_format .= $DAYJS_MATCHING[$char];
        } else {
          $dayjs_format .= $char;
        }
      }
    }

    $tz = \wp_timezone();
    $now = new \DateTime('now', $tz);

    return [
      'dayjs' => $dayjs_format,
      'display' => $php_format,
      'display_example' => $now->format($php_format),
    ];
  }



  public static function create_form(\WP_Post $post) {
    // Return HTML
  }

}
