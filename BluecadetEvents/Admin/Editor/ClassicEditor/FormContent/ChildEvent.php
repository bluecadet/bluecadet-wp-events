<?php

namespace BluecadetEvents\Admin\Editor\ClassicEditor\FormContent;
use BluecadetEvents\Admin\Editor\ClassicEditor\FormContent\MetaBoxPatterns;
use BluecadetEvents\Admin\Utils\DatabaseHelpers;
use BluecadetEvents\Admin\Meta\MetaKeys;


/**
 * Create Metabox form components for Events post type
 *
 * @package BluecadetEvents
 * @since  1.0.0
 *
 */
class ChildEvent {
  private MetaBoxPatterns $patterns;
  private \WP_Post $post;
  private DatabaseHelpers $db_helpers;
  private array $keys;

  public function __construct() {
    global $post;

    $this->post = $post;
    $this->patterns = new MetaBoxPatterns($post);
    $this->db_helpers = DatabaseHelpers::get_instance(); 
    $this->keys = MetaKeys::get_keys();

    $this->create_form();
  }

  private function date_string_to_YMD(string $date) {
    $date = strtotime($date);
    return date('F j, Y', $date);
  }

  private function create_form() {
    $parent        = $this->db_helpers->get_recurring_parent($this->post->ID);
    $parent_title  = $parent->post_title;
    $parent_url    = get_edit_post_link($parent->ID);
    $startDate     = get_post_meta($this->post->ID, $this->keys['start_date'], true);
    $startTime     = get_post_meta($this->post->ID, $this->keys['start_time'], true);
    $endDate       = get_post_meta($this->post->ID, $this->keys['end_date'], true);
    $endTime       = get_post_meta($this->post->ID, $this->keys['end_time'], true);
    $DENY_OVERRIDE = get_post_meta($this->post->ID, $this->keys['child_deny_override'], true) === '1' ? true : false;
    
    ?>
    <div class="bc-events__flex-fieldset">
      <div class="bc-event__child-content">
        <p class="bc-event__child-content-title"><strong>This is a child event of <a href="<?= esc_url( $parent_url ) ?>" target="_blank" rel="noopener noreferrer"><?= esc_html( $parent_title ) ?></a></strong></p> 
      </div>
      <div class="bc-event__child-overview">
        <p class="bc-event-dates__description bc-events__child-overview-row">
          Event Start: <span><?= $startDate ? esc_html( $this->date_string_to_YMD( $startDate ) ) : 'N/A' ?> <?= $startTime ? 'at ' . esc_html( $startTime ) : '' ?></span>
        </p>     
        <p class="bc-event-dates__description bc-events__child-overview-row">
          Event End: <span><?= $endDate ? esc_html( $this->date_string_to_YMD( $endDate ) ) : 'N/A' ?> <?= $endTime ? 'at ' . esc_html( $endTime ) : '' ?></span>
        </p>     
      </div>
      <div class="bc-event__child-content">
        <p class="bc-event-dates__description">By default, content from <?= esc_html( $parent_title ) ?> will overwrite all content changes made in the editor for this event. You can override this behavior by checking the option below.</p>
        <div class="bc-events__flex-fieldset">
          <?php
            $this->patterns->BasicCheckbox(
              $this->keys['child_deny_override'],
              $DENY_OVERRIDE,
              'Override content for this event'
            );
          ?>
        </div>
        <div class="bc-event__child-content-notes">
          <p class="bc-event-dates__description"><strong>Note that by checking this option:</strong></p>
          <ul>
            <li class="bc-event-dates__description">Content will not longer sync from the parent. Any changes from the parent will need to be added manually.</li>
            <li class="bc-event-dates__description">This will only effect content from the editor. Taxonomies and other meta values will still be overwritten by parent data.</li>
            <li class="bc-event-dates__description">If unchecked, content will not be synced until the <strong>parent</strong> event is saved.</li>
          </ul>
        </div>
      </div>
    </div>

    <?php

  }


}