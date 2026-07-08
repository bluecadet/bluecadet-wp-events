<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor\FormContent;
use BluecadetEvents\Plugin;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;

/**
 * Metabox form component kinda-sorta-api thing.
 *
 * Each method here mirrors a React component used by the block editor
 * (editor/components/_formParts, _sections, SectionToggle) so the classic
 * editor renders the same markup. Leaf "form part" methods take explicit
 * params (like React props); "section" methods read meta directly from the
 * post (like the React components that pull from the data store).
 *
 * Every saveable input carries the meta key as its id/name so the values can
 * be written back to post meta when the classic editor form is submitted.
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class MetaBoxPatterns {
  private \WP_Post $post;
  private array $keys;

  public function __construct(\WP_Post $post) {
    $this->post = $post;
    $this->keys = EventsMetaKeys::get_keys();
  }


  /* -----------------------------------------------------------------------
   * Helpers
   * --------------------------------------------------------------------- */

  /**
   * Read a single meta value for the current post.
   */
  private function meta(string $key) {
    return get_post_meta($this->post->ID, $key, true);
  }

  /**
   * Read a boolean-ish meta value (block editor stores these as '1').
   */
  private function meta_bool(string $key): bool {
    return $this->meta($key) === '1';
  }

  private static function frequency_options(): array {
    return [
      'daily'   => __( 'Daily', 'bluecadet-events' ),
      'weekly'  => __( 'Weekly', 'bluecadet-events' ),
      'monthly' => __( 'Monthly', 'bluecadet-events' ),
    ];
  }

  private static function day_of_week_options(): array {
    return [
      'sunday'    => __( 'Sunday', 'bluecadet-events' ),
      'monday'    => __( 'Monday', 'bluecadet-events' ),
      'tuesday'   => __( 'Tuesday', 'bluecadet-events' ),
      'wednesday' => __( 'Wednesday', 'bluecadet-events' ),
      'thursday'  => __( 'Thursday', 'bluecadet-events' ),
      'friday'    => __( 'Friday', 'bluecadet-events' ),
      'saturday'  => __( 'Saturday', 'bluecadet-events' ),
    ];
  }

  private static function monthly_frequency_options(): array {
    return [
      'first'       => __( 'Every First', 'bluecadet-events' ),
      'second'      => __( 'Every Second', 'bluecadet-events' ),
      'third'       => __( 'Every Third', 'bluecadet-events' ),
      'fourth'      => __( 'Every Fourth', 'bluecadet-events' ),
      'last'        => __( 'Every Last', 'bluecadet-events' ),
      'every_other' => __( 'Every Other', 'bluecadet-events' ),
      'date'        => __( 'On a Specific Date', 'bluecadet-events' ),
    ];
  }


  /* -----------------------------------------------------------------------
   * Leaf form parts (mirror editor/components/_formParts/*)
   * --------------------------------------------------------------------- */

  /**
   * Mirrors _formParts/BasicCheckbox.
   */
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
          name="<?php echo esc_attr( $id ); ?>"
          value="1"
          <?php echo $checked ? 'checked' : ''; ?>
        />
        <?php if ( !$flip ) { ?>
          <label class="bc-event-dates__label <?php echo $asToggle ? 'bc-event-dates__checkbox--toggle-label' : 'bc-event-dates__checkbox-label'; ?>" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
        <?php } ?>
      </div>
    <?php
  }


  /**
   * Mirrors _formParts/DateRow. Both inputs carry the meta key as id/name.
   */
  public function DateRow(
    string $dateID,
    string $dateValue,
    string $dateLabel,
    ?string $dateMin,
    string $timeID,
    string $timeValue,
    string $timeLabel,
    ?string $timeMin = null,
    bool $required = false
  ) {
    ?>
      <div class="bc-event-dates__date-row<?php echo $required ? ' bc-event-dates__date-row--required' : ''; ?>">
        <div class="bc-event-dates__date-row-date">
          <label class="bc-event-dates__label" for="<?php echo esc_attr( $dateID ); ?>"><?php echo esc_html( $dateLabel ); ?><?php echo $required ? '<span class="req">*</span>' : ''; ?></label>
          <input
            class="bc-event-dates__input"
            type="date"
            id="<?php echo esc_attr( $dateID ); ?>"
            name="<?php echo esc_attr( $dateID ); ?>"
            value="<?php echo esc_attr( $dateValue ); ?>"
            <?php echo $dateMin ? 'min="' . esc_attr( $dateMin ) . '"' : ''; ?>
            <?php echo $required ? 'required' : ''; ?>
          />
        </div>
        <div class="bc-event-dates__date-row-time">
          <label class="bc-event-dates__label" for="<?php echo esc_attr( $timeID ); ?>"><?php echo esc_html( $timeLabel ); ?><?php echo $required ? '<span class="req">*</span>' : ''; ?></label>
          <input
            class="bc-event-dates__input"
            type="time"
            id="<?php echo esc_attr( $timeID ); ?>"
            name="<?php echo esc_attr( $timeID ); ?>"
            value="<?php echo esc_attr( $timeValue ); ?>"
            <?php echo $timeMin ? 'min="' . esc_attr( $timeMin ) . '"' : ''; ?>
            <?php echo $required ? 'required' : ''; ?>
          />
        </div>
      </div>
    <?php
  }


  /**
   * Mirrors _formParts/StartEndDate. Renders the start and end date/time
   * groups, plus hidden inputs that carry the computed timestamps (populated
   * client-side later, but preserved on save here).
   */
  public function StartEndDate(string $startDateKey, string $startTimeKey, string $startTimestampKey, string $endDateKey, string $endTimeKey, string $endTimestampKey) {
    $startDate = (string) $this->meta( $startDateKey );
    $startTime = (string) $this->meta( $startTimeKey );
    $endDate   = (string) $this->meta( $endDateKey );
    $endTime   = (string) $this->meta( $endTimeKey );
    ?>
      <div class="bc-event-dates__group bc-event-dates__group--start">
        <?php
          $this->DateRow(
            $startDateKey,
            $startDate,
            __( 'Start Date', 'bluecadet-events' ),
            null,
            $startTimeKey,
            $startTime,
            __( 'Start Time', 'bluecadet-events' ),
            null,
            true
          );
        ?>
      </div>
      <div class="bc-event-dates__group bc-event-dates__group--end">
        <?php
          $this->DateRow(
            $endDateKey,
            $endDate,
            __( 'End Date', 'bluecadet-events' ),
            $startDate ?: null,
            $endTimeKey,
            $endTime,
            __( 'End Time', 'bluecadet-events' ),
            ( $endDate === $startDate && $startTime ) ? $startTime : null,
            true
          );
        ?>
      </div>
      <input type="hidden" id="<?php echo esc_attr( $startTimestampKey ); ?>" name="<?php echo esc_attr( $startTimestampKey ); ?>" value="<?php echo esc_attr( $this->meta( $startTimestampKey ) ); ?>" class="js-bc-event-timestamp js-bc-start-timestamp" />
      <input type="hidden" id="<?php echo esc_attr( $endTimestampKey ); ?>" name="<?php echo esc_attr( $endTimestampKey ); ?>" value="<?php echo esc_attr( $this->meta( $endTimestampKey ) ); ?>" class="js-bc-event-timestamp js-bc-end-timestamp" />
    <?php
  }


  /**
   * Mirrors _formParts/BasicSelect. $options is [ value => label ].
   */
  public function BasicSelect(string $id, string $value, array $options, string $label, string $className = '') {
    ?>
      <div class="bc-event-dates__select-group bc-event-dates__input-row <?php echo esc_attr( $className ); ?>">
        <label class="bc-event-dates__label bc-event-dates__select-group-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
        <select
          class="bc-event-dates__input bc-event-dates__select bc-event-dates__select-group-select"
          id="<?php echo esc_attr( $id ); ?>"
          name="<?php echo esc_attr( $id ); ?>"
        >
          <?php foreach ( $options as $opt_value => $opt_label ) { ?>
            <option value="<?php echo esc_attr( $opt_value ); ?>" <?php selected( (string) $opt_value, $value ); ?>><?php echo esc_html( $opt_label ); ?></option>
          <?php } ?>
        </select>
      </div>
    <?php
  }


  /**
   * Mirrors _formParts/BasicText.
   */
  public function BasicText(string $id, string $value, string $label, string $className = '') {
    ?>
      <div class="bc-event-dates__text-group bc-event-dates__input-row <?php echo esc_attr( $className ); ?>">
        <label class="bc-event-dates__label bc-event-dates__text-group-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
        <input
          type="text"
          class="bc-event-dates__input bc-event-dates__text bc-event-dates__text-group-text"
          id="<?php echo esc_attr( $id ); ?>"
          name="<?php echo esc_attr( $id ); ?>"
          value="<?php echo esc_attr( $value ); ?>"
        />
      </div>
    <?php
  }


  /**
   * Mirrors _formParts/BasicTextArea.
   */
  public function BasicTextArea(string $id, string $value, string $label, string $className = '', int $rows = 2) {
    ?>
      <div class="bc-event-dates__text-group bc-event-dates__input-row <?php echo esc_attr( $className ); ?>">
        <label class="bc-event-dates__label bc-event-dates__text-group-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
        <textarea
          class="bc-event-dates__input bc-event-dates__text bc-event-dates__text-group-text"
          id="<?php echo esc_attr( $id ); ?>"
          name="<?php echo esc_attr( $id ); ?>"
          rows="<?php echo esc_attr( $rows ); ?>"
        ><?php echo esc_textarea( $value ); ?></textarea>
      </div>
    <?php
  }


  /**
   * Mirrors _formParts/CheckboxButton. The toggle is a button (styled like the
   * React version); a sibling hidden input carries the meta value for saving.
   */
  public function CheckboxButton(string $id, bool $value, string $label, ?string $pressedLabel = null, bool $smallOnChecked = false, bool $smallButton = false) {
    $pressedLabelText = $pressedLabel ?: $label;
    $classes = $smallButton
      ? 'bc-events__checkbox-button bc-events__button bc-events__button--secondary bc-events__button--medium'
      : 'bc-events__checkbox-button bc-events__button bc-events__button--secondary';
    $checkedClasses = $smallOnChecked
      ? 'bc-events__checkbox-button bc-events__button bc-events__button--secondary bc-events__button--small'
      : $classes;
    ?>
      <input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" value="<?php echo $value ? '1' : ''; ?>" class="js-bc-checkbox-button-value" />
      <button
        type="button"
        class="<?php echo esc_attr( $value ? $checkedClasses : $classes ); ?>"
        aria-pressed="<?php echo $value ? 'true' : 'false'; ?>"
        data-target="<?php echo esc_attr( $id ); ?>"
        data-label="<?php echo esc_attr( $label ); ?>"
        data-pressed-label="<?php echo esc_attr( $pressedLabelText ); ?>"
      >
        <?php echo esc_html( $value ? $pressedLabelText : $label ); ?>
      </button>
    <?php
  }


  /**
   * Mirrors _formParts/CheckboxFormGroup. $values is the saved array, $options
   * is [ value => label ]. Submits as $key[].
   */
  public function CheckboxFormGroup(string $id, array $values, array $options, string $label) {
    ?>
      <div class="bc-event-dates__checkbox-form-group">
        <div class="bc-event-dates__label bc-event-dates__checkbox-form-group-title"><?php echo esc_html( $label ); ?></div>
        <div class="bc-event-dates__checkbox-form-group-inner">
          <?php foreach ( $options as $opt_value => $opt_label ) { ?>
            <div class="bc-event-dates__checkbox-form-group-option">
              <input
                class="bc-event-dates__checkbox--toggle bc-event-dates__checkbox-form-group-checkbox"
                type="checkbox"
                id="<?php echo esc_attr( $id . '-' . $opt_value ); ?>"
                name="<?php echo esc_attr( $id ); ?>[]"
                value="<?php echo esc_attr( $opt_value ); ?>"
                <?php checked( in_array( $opt_value, $values, true ) ); ?>
              />
              <label class="bc-event-dates__label bc-event-dates__checkbox-form-group-label" for="<?php echo esc_attr( $id . '-' . $opt_value ); ?>"><?php echo esc_html( $opt_label ); ?></label>
            </div>
          <?php } ?>
        </div>
      </div>
    <?php
  }


  /**
   * Mirrors _formParts/RecurringButton. Button toggles UI; the hidden input
   * carries the is_recurring meta value for saving.
   */
  public function RecurringButton(string $key, bool $value) {
    ?>
      <input type="hidden" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo $value ? '1' : ''; ?>" class="js-bc-recurring-value" />
      <button
        type="button"
        class="bce-events-recurring-button <?php echo $value ? 'is-pressed' : ''; ?>"
        aria-pressed="<?php echo $value ? 'true' : 'false'; ?>"
        data-target="<?php echo esc_attr( $key ); ?>"
      >
        <?php echo esc_html( $value ? __( 'Recurring Event Settings', 'bluecadet-events' ) : __( 'Add Recurring Dates', 'bluecadet-events' ) ); ?>
      </button>
    <?php
  }


  /**
   * Mirrors _formParts/PostPicker. The underlying native <select> is rendered
   * with only the currently-selected option (so the value/title persist and
   * submit with the form); Tom Select enhances it into a searchable combobox
   * that loads more results via the REST API as the user types (see
   * assets/classicEditor/src/events.js). $name allows array-style names
   * (e.g. "key[]") for repeating pickers.
   */
  public function PostPicker(string $postType, string|int $value, string $name) {
    $selected_title = $value ? get_the_title( (int) $value ) : '';
    ?>
      <select
        class="bc-event-post-picker__select js-bc-post-picker"
        name="<?php echo esc_attr( $name ); ?>"
        data-post-type="<?php echo esc_attr( $postType ); ?>"
        data-placeholder="<?php echo esc_attr__( 'Select a post…', 'bluecadet-events' ); ?>"
      >
        <?php if ( $value ) { ?>
          <option value="<?php echo esc_attr( $value ); ?>" selected><?php echo esc_html( $selected_title !== '' ? $selected_title : '#' . $value ); ?></option>
        <?php } ?>
      </select>
    <?php
  }


  /* -----------------------------------------------------------------------
   * Sections (mirror editor/components/_sections/* and SectionToggle)
   * --------------------------------------------------------------------- */

  /**
   * Mirrors SectionToggle/SectionToggle.
   */
  public function SectionToggle(string $title, string $titleTag = 'h3', ?string $className = null, bool $asTitle = false) {
    ?>
      <div class="bc-events__section-toggle open">
        <?php if ( $titleTag === 'h2' ) { ?>
          <h2 class="bce-sr-only"><?php echo esc_html( $title ); ?></h2>
        <?php } else { ?>
          <h3 class="bce-sr-only"><?php echo esc_html( $title ); ?></h3>
        <?php } ?>
        <button type="button" class="bc-events__section-toggle-button bc-events-title-h1 open<?php echo $className ? ' ' . esc_attr( $className ) : ''; ?>">
          <span class="bc-event-dates__toggle-text"><?php echo esc_html( $title ); ?></span>
          <span class="bc-event-dates__toggle-icon" aria-hidden="true">
            <?php $this->caret_icon(); ?>
          </span>
        </button>
      </div>
    <?php
  }


  /**
   * Mirrors _sections/EventDetails.
   */
  public function EventDetails() {
    ?>
      <div class="bc-event-details bc-event__content-section">
        <div class="bc-events__flex-fieldset">
          <?php
            $this->StartEndDate(
              $this->keys['start_date'],
              $this->keys['start_time'],
              $this->keys['start_timestamp'],
              $this->keys['end_date'],
              $this->keys['end_time'],
              $this->keys['end_timestamp']
            );
          ?>
        </div>

        <div class="bc-event-dates__divider"></div>

        <div class="bc-events__flex-fieldset">
          <div class="js-bc-hide-time-control">
            <?php
              $this->BasicCheckbox(
                $this->keys['hide_time_display'],
                $this->meta_bool( $this->keys['hide_time_display'] ),
                __( 'Hide Time Display', 'bluecadet-events' )
              );
            ?>
          </div>
          <div class="js-bc-hide-end-time">
            <?php
              $this->BasicCheckbox(
                $this->keys['hide_end_time_display'],
                $this->meta_bool( $this->keys['hide_end_time_display'] ),
                __( 'Hide End Time Display', 'bluecadet-events' )
              );
            ?>
          </div>
        </div>
      </div>
    <?php
  }


  /**
   * Mirrors _sections/RepeatingPostPicker.
   */
  public function RepeatingPostPicker(string $id, string $sectionTitle, string $metaKey, string $postType, string $addButtonTitle = 'Add', string $removeButtonText = 'Remove') {
    $values = $this->meta( $metaKey );
    if ( !is_array( $values ) ) {
      $values = [];
    }
    ?>
      <div class="bc-event-repeating-picker__section">
        <?php $this->SectionToggle( $sectionTitle, 'h3' ); ?>
        <div class="bc-event__content-section">
          <?php foreach ( $values as $postID ) { ?>
            <div class="bc-event-repeating-picker__selection">
              <div class="bc-event-dates__input-row">
                <?php $this->PostPicker( $postType, $postID, $metaKey . '[]' ); ?>
              </div>
              <div class="bc-event-repeating-picker__section-remove">
                <button type="button" class="bc-events__button bc-events__button--small bc-events__button--warning js-bc-repeating-remove"><?php echo esc_html( $removeButtonText ); ?></button>
              </div>
            </div>
          <?php } ?>
          <div class="bc-event-repeating-picker__section-add">
            <button type="button" class="bc-events__button bc-events__button--secondary js-bc-repeating-add" data-meta-key="<?php echo esc_attr( $metaKey ); ?>" data-post-type="<?php echo esc_attr( $postType ); ?>"><?php echo esc_html( $addButtonTitle ); ?></button>
          </div>

          <template class="js-bc-repeating-template">
            <div class="bc-event-repeating-picker__selection">
              <div class="bc-event-dates__input-row">
                <?php $this->PostPicker( $postType, '', $metaKey . '[]' ); ?>
              </div>
              <div class="bc-event-repeating-picker__section-remove">
                <button type="button" class="bc-events__button bc-events__button--small bc-events__button--warning js-bc-repeating-remove"><?php echo esc_html( $removeButtonText ); ?></button>
              </div>
            </div>
          </template>
        </div>
      </div>
    <?php
  }


  /**
   * Mirrors _sections/EventLocations.
   */
  public function EventLocations() {
    $this->RepeatingPostPicker(
      'event-locations',
      __( 'Event Locations', 'bluecadet-events' ),
      $this->keys['location_ids'],
      'bc-events-locations',
      __( 'Add Location', 'bluecadet-events' )
    );
  }


  /**
   * Mirrors _sections/EventSeries.
   */
  public function EventSeries() {
    $this->RepeatingPostPicker(
      'event-series',
      __( 'Event Series', 'bluecadet-events' ),
      $this->keys['series_ids'],
      'bc-events-series',
      __( 'Add Series', 'bluecadet-events' )
    );
  }


  /**
   * Mirrors _sections/Recurring.
   */
  public function Recurring() {
    $wasRecurring = $this->meta_bool( $this->keys['is_recurring_was'] );
    ?>
      <div class="bc-event-recurring" data-was-recurring="<?php echo $wasRecurring ? '1' : '0'; ?>">
        <div class="bc-event__content-section">
          <?php $this->RecurringButton( $this->keys['is_recurring'], $this->meta_bool( $this->keys['is_recurring'] ) ); ?>
        </div>
        <div class="bc-event-recurring__inner">
          <?php $this->RecurringAltered(); ?>

          <div class="bc-event-recurring__section">
            <?php $this->SectionToggle( __( 'Frequency Options', 'bluecadet-events' ), 'h3' ); ?>
            <div class="bc-event__content-section">
              <?php $this->Frequency(); ?>
            </div>
          </div>

          <div class="bc-event-recurring__section">
            <?php $this->SectionToggle( __( 'Specific Dates', 'bluecadet-events' ), 'h3' ); ?>
            <div class="bc-event__content-section">
              <?php $this->CustomOccurences(); ?>
            </div>
          </div>

          <div class="bc-event-recurring__section">
            <?php $this->SectionToggle( __( 'Exclusions', 'bluecadet-events' ), 'h3' ); ?>
            <div class="bc-event__content-section">
              <?php $this->OmitDates(); ?>
            </div>
          </div>
        </div>

        <?php $this->RemoveRecurring(); ?>
      </div>
    <?php
  }


  /**
   * Mirrors _sections/Recurring/RecurringAltered. Static notice; JS toggles
   * visibility based on whether the recurring pattern changed.
   */
  public function RecurringAltered() {
    // The saved recurrence "strategy" snapshot from the last save. The notice
    // only appears when the current strategy differs from this (handled in JS),
    // mirroring _sections/Recurring/RecurringAltered. New posts have no snapshot,
    // so the notice never shows for them.
    $recurWas     = $this->meta( $this->keys['recur_strategy_was'] );
    $recurWasJson = ( is_array( $recurWas ) && ! empty( $recurWas ) ) ? wp_json_encode( $recurWas ) : '';
    ?>
      <div class="bc-events__recurring-notice js-bc-recurring-notice" data-recur-was="<?php echo esc_attr( $recurWasJson ); ?>" style="display:none;">
        <div class="bc-events__recurring-notice-inner">
          <p class="bc-event-dates__description"><strong><?php echo esc_html__( 'The recurring event pattern has changed.', 'bluecadet-events' ); ?></strong></p>
          <p class="bc-event-dates__description"><?php echo esc_html__( 'Existing child events with dates that are not in the current recurring pattern will be deleted. This may include customized child events. Proceed with caution.', 'bluecadet-events' ); ?></p>
        </div>
      </div>
    <?php
  }


  /**
   * Mirrors _sections/Frequency.
   */
  public function Frequency() {
    $useFrequency  = $this->meta_bool( $this->keys['use_frequency'] );
    $frequency     = (string) $this->meta( $this->keys['freq'] );
    $weeklyDays    = $this->meta( $this->keys['freq_days'] );
    $weeklyDays    = is_array( $weeklyDays ) ? $weeklyDays : [];
    $monthlySched  = (string) $this->meta( $this->keys['freq_mo_schedule'] );
    $monthlyDay    = (string) $this->meta( $this->keys['freq_mo_day'] );
    $monthlyDate   = (string) $this->meta( $this->keys['freq_mo_date'] );
    $endType       = (string) $this->meta( $this->keys['freq_end_type'] ) ?: 'on_date';
    $endDate       = (string) $this->meta( $this->keys['freq_end_date'] );
    $endAfterX     = $this->meta( $this->keys['freq_end_after_x'] );
    $endAfterX     = ( $endAfterX === '' || $endAfterX === false ) ? 2 : $endAfterX;
    $startDate     = (string) $this->meta( $this->keys['start_date'] );
    ?>
      <div class="bc-event-dates__frequency">
        <p class="bc-event-dates__description"><?php echo esc_html__( 'If your event recurs on a specific schedule, you can set the frequency here.', 'bluecadet-events' ); ?></p>

        <div class="bc-events__flex-fieldset js-bc-frequency-options">
          <div class="bc-event-dates__frequency-row">
            <?php
              $this->BasicSelect(
                $this->keys['freq'],
                $frequency,
                self::frequency_options(),
                __( 'Frequency', 'bluecadet-events' ),
                'js-bc-freq-select'
              );
            ?>
          </div>

          <div class="bc-event-dates__frequency-row js-bc-frequency-weekly">
            <?php
              $this->CheckboxFormGroup(
                $this->keys['freq_days'],
                $weeklyDays,
                self::day_of_week_options(),
                __( 'Day(s) of the week', 'bluecadet-events' )
              );
            ?>
          </div>

          <div class="js-bc-frequency-monthly">
            <div class="bc-event-dates__frequency-row">
              <?php
                $this->BasicSelect(
                  $this->keys['freq_mo_schedule'],
                  $monthlySched,
                  self::monthly_frequency_options(),
                  __( 'Schedule', 'bluecadet-events' ),
                  'js-bc-freq-mo-schedule'
                );
              ?>
            </div>

            <div class="bc-event-dates__frequency-row bc-event-dates__input-row js-bc-frequency-monthly-date">
              <label class="bc-event-dates__label" for="<?php echo esc_attr( $this->keys['freq_mo_date'] ); ?>"><?php echo esc_html__( 'Day of Month', 'bluecadet-events' ); ?></label>
              <input
                class="bc-event-dates__input"
                type="number"
                id="<?php echo esc_attr( $this->keys['freq_mo_date'] ); ?>"
                name="<?php echo esc_attr( $this->keys['freq_mo_date'] ); ?>"
                value="<?php echo esc_attr( $monthlyDate ); ?>"
                step="1"
                min="1"
                max="31"
              />
            </div>

            <div class="bc-event-dates__frequency-row js-bc-frequency-monthly-day">
              <?php
                $this->BasicSelect(
                  $this->keys['freq_mo_day'],
                  $monthlyDay,
                  self::day_of_week_options(),
                  __( 'Weekday', 'bluecadet-events' )
                );
              ?>
            </div>
          </div>

          <div class="bc-event-dates__frequency-row">
            <?php
              $this->BasicSelect(
                $this->keys['freq_end_type'],
                $endType,
                [
                  'on_date' => __( 'On Selected Date', 'bluecadet-events' ),
                  'after_x' => __( 'After [X] Events', 'bluecadet-events' ),
                ],
                __( 'Ends', 'bluecadet-events' ),
                'js-bc-freq-end-type'
              );
            ?>
          </div>

          <div class="bc-event-dates__frequency-row bc-event-dates__input-row js-bc-frequency-end-date">
            <label class="bc-event-dates__label" for="<?php echo esc_attr( $this->keys['freq_end_date'] ); ?>"><?php echo esc_html__( 'At the end of day:', 'bluecadet-events' ); ?></label>
            <input
              class="bc-event-dates__input"
              type="date"
              id="<?php echo esc_attr( $this->keys['freq_end_date'] ); ?>"
              name="<?php echo esc_attr( $this->keys['freq_end_date'] ); ?>"
              value="<?php echo esc_attr( $endDate ); ?>"
              <?php echo $startDate ? 'min="' . esc_attr( $startDate ) . '"' : ''; ?>
            />
          </div>

          <div class="bc-event-dates__frequency-row bc-event-dates__input-row js-bc-frequency-end-after-x">
            <label class="bc-event-dates__label" for="<?php echo esc_attr( $this->keys['freq_end_after_x'] ); ?>"><?php echo esc_html__( 'After', 'bluecadet-events' ); ?></label>
            <input
              class="bc-event-dates__input"
              type="number"
              id="<?php echo esc_attr( $this->keys['freq_end_after_x'] ); ?>"
              name="<?php echo esc_attr( $this->keys['freq_end_after_x'] ); ?>"
              value="<?php echo esc_attr( $endAfterX ); ?>"
              min="1"
            />
            <span class="bc-event-dates__label bc-event-dates__recurring-end-after-x-label"><?php echo esc_html__( 'events', 'bluecadet-events' ); ?></span>
          </div>
        </div>

        <div class="bc-event-dates__frequency-row bc-event-dates__frequency-toggle">
          <?php
            $this->CheckboxButton(
              $this->keys['use_frequency'],
              $useFrequency,
              __( 'Set a Frequency', 'bluecadet-events' ),
              __( 'Remove Frequency', 'bluecadet-events' ),
              true
            );
          ?>
        </div>
      </div>
    <?php
  }


  /**
   * Mirrors _sections/CustomOccurences. Renders saved occurrence rows, each as
   * $key[i][field]; the Add button is wired up with JS later.
   */
  public function CustomOccurences() {
    $key    = $this->keys['custom_occurrences'];
    $values = $this->meta( $key );
    $values = is_array( $values ) ? $values : [];
    ?>
      <div class="bc-event-dates__custom-occurences" data-meta-key="<?php echo esc_attr( $key ); ?>">
        <p class="bc-event-dates__description"><?php echo esc_html__( 'If your event occurs on days or times that do not fit a regular schedule, you can add the individual dates here. Individual dates can be added in addition to setting a frequency, or on their own.', 'bluecadet-events' ); ?></p>
        <p class="bc-event-dates__description"><?php echo esc_html__( 'Start Date is the only required field for each set, but you can configure end date and times as needed.', 'bluecadet-events' ); ?></p>

        <div class="js-bc-occurence-list">
          <?php foreach ( $values as $index => $occurence ) {
            $this->occurence_row( $key, (int) $index, (array) $occurence );
          } ?>
        </div>

        <div class="bc-event-dates__custom-occurence-add">
          <button type="button" class="bc-events__button bc-events__button--secondary js-bc-occurence-add" data-meta-key="<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Add Specific Date', 'bluecadet-events' ); ?></button>
        </div>

        <template class="js-bc-occurence-template">
          <?php $this->occurence_row( $key, '__INDEX__', [] ); ?>
        </template>
      </div>
    <?php
  }


  /**
   * A single custom occurrence row (mirrors OccurenceRow inside CustomOccurences).
   */
  private function occurence_row(string $key, string|int $index, array $occurence) {
    $name       = $key . '[' . $index . ']';
    $startDate  = (string) ( $occurence['start_date'] ?? '' );
    $customize  = !empty( $occurence['customize'] );
    $startTime  = (string) ( $occurence['start_time'] ?? '' );
    $endDate    = (string) ( $occurence['end_date'] ?? '' );
    $endTime    = (string) ( $occurence['end_time'] ?? '' );
    ?>
      <div class="bc-event-dates__custom-occurence">
        <div class="bc-event-dates__custom-occurence-inputs">
          <div class="bc-event-dates__custom-occurence-dates">
            <div class="bc-events__flex-fieldset">
              <div class="bc-event-dates__date-row-date">
                <label class="bc-event-dates__label" for="<?php echo esc_attr( $name . '[start_date]' ); ?>"><?php echo esc_html__( 'Start Date', 'bluecadet-events' ); ?></label>
                <input
                  class="bc-event-dates__input"
                  type="date"
                  id="<?php echo esc_attr( $name . '[start_date]' ); ?>"
                  name="<?php echo esc_attr( $name . '[start_date]' ); ?>"
                  value="<?php echo esc_attr( $startDate ); ?>"
                />
              </div>

              <div class="bc-event-dates__custom-occurence-customize">
                <?php $this->BasicCheckbox( $name . '[customize]', $customize, __( 'Add custom times', 'bluecadet-events' ) ); ?>
              </div>

              <div class="js-bc-occurence-custom">
                <div class="bc-event-dates__date-row-date">
                  <label class="bc-event-dates__label" for="<?php echo esc_attr( $name . '[start_time]' ); ?>"><?php echo esc_html__( 'Start Time', 'bluecadet-events' ); ?></label>
                  <input
                    class="bc-event-dates__input"
                    type="time"
                    id="<?php echo esc_attr( $name . '[start_time]' ); ?>"
                    name="<?php echo esc_attr( $name . '[start_time]' ); ?>"
                    value="<?php echo esc_attr( $startTime ); ?>"
                  />
                </div>

                <div class="bc-event-dates__date-row-date">
                  <label class="bc-event-dates__label" for="<?php echo esc_attr( $name . '[end_date]' ); ?>"><?php echo esc_html__( 'End Date', 'bluecadet-events' ); ?></label>
                  <input
                    class="bc-event-dates__input"
                    type="date"
                    id="<?php echo esc_attr( $name . '[end_date]' ); ?>"
                    name="<?php echo esc_attr( $name . '[end_date]' ); ?>"
                    value="<?php echo esc_attr( $endDate ); ?>"
                  />
                </div>

                <div class="bc-event-dates__date-row-date">
                  <label class="bc-event-dates__label" for="<?php echo esc_attr( $name . '[end_time]' ); ?>"><?php echo esc_html__( 'End Time', 'bluecadet-events' ); ?></label>
                  <input
                    class="bc-event-dates__input"
                    type="time"
                    id="<?php echo esc_attr( $name . '[end_time]' ); ?>"
                    name="<?php echo esc_attr( $name . '[end_time]' ); ?>"
                    value="<?php echo esc_attr( $endTime ); ?>"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="bc-event-dates__custom-occurence-remove">
            <button type="button" class="bc-event-dates__remove-occurence bc-events__button bc-events__button--small bc-events__button--warning js-bc-occurence-remove"><?php echo esc_html__( 'Remove', 'bluecadet-events' ); ?></button>
          </div>
        </div>
      </div>
    <?php
  }


  /**
   * Mirrors _sections/OmitDates. Renders saved exclusion dates as $key[].
   */
  public function OmitDates() {
    $key    = $this->keys['omissions'];
    $values = $this->meta( $key );
    $values = is_array( $values ) ? $values : [];
    ?>
      <div class="bc-event-dates__exclusions">
        <p class="bc-event-dates__description"><?php echo esc_html__( 'If your event has specific dates that should be excluded from a schedule, you can add them here.', 'bluecadet-events' ); ?></p>
        <div class="bc-event-dates__exclusions-dates">
          <?php foreach ( $values as $index => $omitDate ) {
            $field_id = $key . '-' . $index;
          ?>
            <div class="bc-event-dates__exclusion">
              <div class="bc-event-dates__input-row">
                <label class="u-sr-only" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( sprintf( __( 'Omit Date %d', 'bluecadet-events' ), (int) $index + 1 ) ); ?></label>
                <input
                  class="bc-event-dates__input"
                  type="date"
                  id="<?php echo esc_attr( $field_id ); ?>"
                  name="<?php echo esc_attr( $key . '[]' ); ?>"
                  value="<?php echo esc_attr( $omitDate ); ?>"
                />
              </div>
              <button type="button" class="bc-events__button bc-events__button--small bc-events__button--warning js-bc-omit-remove"><?php echo esc_html__( 'Remove', 'bluecadet-events' ); ?></button>
            </div>
          <?php } ?>
        </div>
        <button type="button" class="bc-events__button bc-events__button--secondary js-bc-omit-add" data-meta-key="<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Add Exclusion', 'bluecadet-events' ); ?></button>

        <template class="js-bc-omit-template">
          <div class="bc-event-dates__exclusion">
            <div class="bc-event-dates__input-row">
              <label class="u-sr-only"><?php echo esc_html__( 'Omit Date', 'bluecadet-events' ); ?></label>
              <input
                class="bc-event-dates__input"
                type="date"
                name="<?php echo esc_attr( $key . '[]' ); ?>"
                value=""
              />
            </div>
            <button type="button" class="bc-events__button bc-events__button--small bc-events__button--warning js-bc-omit-remove"><?php echo esc_html__( 'Remove', 'bluecadet-events' ); ?></button>
          </div>
        </template>
      </div>
    <?php
  }


  /**
   * Mirrors _sections/RemoveRecurring. Static warning shown (via JS) when a
   * previously-recurring event is being made non-recurring.
   */
  public function RemoveRecurring() {
    $userAllowDelete = $this->meta_bool( $this->keys['remove_recurring'] );
    ?>
      <div class="bc-event__content-section js-bc-remove-recurring">
        <div class="bc-event__remove-recurring">
          <div class="bc-event__remove-recurring-content bc-event__content-section">
            <h2 class="bc-events-title-h1"><?php echo esc_html__( 'Remove Recurring Events', 'bluecadet-events' ); ?></h2>
            <p class="bc-event-dates__description"><?php echo esc_html__( "It seems like you're trying to remove recurring settings from an event that was previously set as recurring. This action will not take place until you save the post.", 'bluecadet-events' ); ?></p>

            <?php
              $this->CheckboxButton(
                $this->keys['is_recurring'] . '__reapply',
                $this->meta_bool( $this->keys['is_recurring'] ),
                __( 'Reset Recurring Settings', 'bluecadet-events' ),
                null,
                false,
                true
              );
            ?>
          </div>

          <div class="bc-event__remove-recurring-action bc-event__content-section">
            <div class="bc-event__remove-recurring-desc">
              <p class="bc-event__remove-recurring-desc-title"><?php echo esc_html__( 'This action cannot be undone.', 'bluecadet-events' ); ?></p>
              <p><?php echo esc_html__( 'Once saved:', 'bluecadet-events' ); ?></p>
              <ul class="bc-event__remove-recurring-list">
                <li><?php echo esc_html__( 'All child recurring events will be deleted', 'bluecadet-events' ); ?></li>
                <li><?php echo esc_html__( 'All existing recurring settings will be reset to default settings', 'bluecadet-events' ); ?></li>
              </ul>
              <p class="bc-event__important"><?php echo esc_html__( 'To continue, confirm by checking below:', 'bluecadet-events' ); ?></p>
            </div>

            <?php
              $this->BasicCheckbox(
                $this->keys['remove_recurring'],
                $userAllowDelete,
                __( 'I understand that this action cannot be undone and I want to proceed.', 'bluecadet-events' )
              );
            ?>
          </div>
        </div>
      </div>
    <?php
  }


  /**
   * Caret icon (mirrors components/icons/CaretIcon used by SectionToggle).
   */
  private function caret_icon() {
    ?>
      <span class="bce-caret-icon"></span>
    <?php
  }

}
