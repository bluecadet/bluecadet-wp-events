<?php

namespace BluecadetEvents\Abilities;
use BluecadetEvents\Admin\Utils\AbstractService;
use BluecadetEvents\Admin\Editor\RestRoutes;
use BluecadetEvents\Admin\Meta\Keys\EventsMetaKeys;
use BluecadetEvents\Plugin\Settings;
use BluecadetEvents\Plugin\Hooks;

/**
 * Abilities API integration
 *
 * Registers `bc-events/*` abilities so MCP clients (through the WordPress MCP
 * Adapter) can look up, create and update events the same way the editor does:
 * timestamps are derived server-side, the input is validated with the editor's
 * rules, and the save goes through wp_insert_post so the recurrence engine runs.
 *
 * Schemas are built from the site's filters, so anything a site has switched off
 * (frequencies, locations, series, recurring description, excerpt/thumbnail
 * support) never reaches the client. Taxonomies are discovered at call time,
 * since the plugin doesn't know which ones a site attaches to events.
 *
 * @package BluecadetEvents
 * @since  1.2.0
 */
class Abilities extends AbstractService {

  const CATEGORY = 'bc-events';

  const DAYS = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

  const STATUSES = ['draft', 'pending', 'publish', 'private'];

  /** Fields owned by the recurring parent; a child occurrence can't set them. */
  const RECUR_FIELDS = [
    'is_recurring', 'use_frequency', 'freq', 'freq_days', 'freq_mo_schedule', 'freq_mo_day',
    'freq_mo_date', 'freq_consecutive_buffer', 'freq_consecutive_count', 'freq_end_type',
    'freq_end_date', 'freq_end_after_x', 'recur_desc', 'custom_occurrences', 'omissions',
    'location_ids', 'series_ids',
  ];

  const DATE_FIELDS = ['start_date', 'start_time', 'end_date', 'end_time'];

  private const DATE_PATTERN = '^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$';
  private const TIME_PATTERN = '^([01]\d|2[0-3]):[0-5]\d$';


  public function boot() : void {
    add_action( 'wp_abilities_api_categories_init', [$this, 'register_category'] );
    add_action( 'wp_abilities_api_init', [$this, 'register_abilities'] );
  }


  public function register_category() : void {
    wp_register_ability_category( self::CATEGORY, [
      'label'       => __( 'Events', 'basecadet' ),
      'description' => __( 'Look up, create and update events.', 'basecadet' ),
    ] );
  }


  public function register_abilities() : void {
    $can_edit = fn() => current_user_can( 'edit_posts' );
    $paging   = [
      'per_page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20],
      'page'     => ['type' => 'integer', 'minimum' => 1, 'default' => 1],
    ];

    $this->add( 'get-settings', __( 'Get Event Settings', 'basecadet' ),
      __( 'Call this first. Returns which event options this site has enabled: frequencies, locations, series, the recurring description, post supports, and the taxonomies attached to events. Options that are off are not accepted by create-event/update-event.', 'basecadet' ),
      ['type' => 'object', 'default' => []],
      [$this, 'get_settings'], $can_edit, true
    );

    $this->add( 'list-events', __( 'List Events', 'basecadet' ),
      __( 'List events ordered by start date. Recurring occurrences (children) are hidden unless include_children is true or parent_id is set.', 'basecadet' ),
      [
        'type'       => 'object',
        'default'    => [],
        'properties' => [
          'search'           => ['type' => 'string'],
          'status'           => ['type' => 'string', 'enum' => array_merge( ['any'], self::STATUSES ), 'default' => 'any'],
          'starts_after'     => ['type' => 'string', 'pattern' => self::DATE_PATTERN, 'description' => 'Only events starting on or after this date (YYYY-MM-DD).'],
          'include_children' => ['type' => 'boolean', 'default' => false],
          'parent_id'        => ['type' => 'integer', 'description' => 'Only the occurrences of this recurring event.'],
        ] + $paging,
        'additionalProperties' => false,
      ],
      [$this, 'list_events'], $can_edit, true
    );

    $this->add( 'get-event', __( 'Get Event', 'basecadet' ),
      __( 'Get one event with all of its editable fields, terms, and (for a recurring parent) its occurrence IDs.', 'basecadet' ),
      [
        'type'                 => 'object',
        'properties'           => ['id' => ['type' => 'integer']],
        'required'             => ['id'],
        'additionalProperties' => false,
      ],
      [$this, 'get_event'],
      fn( $input ) => current_user_can( 'edit_post', (int) ( $input['id'] ?? 0 ) ),
      true
    );

    $this->add( 'create-event', __( 'Create Event', 'basecadet' ),
      __( 'Create an event. Dates and times are in the site timezone; timestamps are calculated for you. Recurring events generate their occurrences during the save; their IDs are in occurrence_ids. Terms must already exist.', 'basecadet' ),
      $this->event_input_schema( false ),
      [$this, 'create_event'],
      [$this, 'can_create'],
      false
    );

    $this->add( 'update-event', __( 'Update Event', 'basecadet' ),
      __( 'Update an event. Only the fields you pass change. A recurring occurrence (child) only accepts title, content, excerpt, status, featured_media, terms and child_deny_override; change its dates on the parent.', 'basecadet' ),
      $this->event_input_schema( true ),
      [$this, 'update_event'],
      [$this, 'can_update'],
      false
    );

    $this->add( 'list-terms', __( 'List Event Terms', 'basecadet' ),
      __( 'List the terms of a taxonomy attached to events (see get-settings for which ones).', 'basecadet' ),
      [
        'type'       => 'object',
        'properties' => [
          'taxonomy' => ['type' => 'string'],
          'search'   => ['type' => 'string'],
        ] + $paging,
        'required'             => ['taxonomy'],
        'additionalProperties' => false,
      ],
      [$this, 'list_terms'], $can_edit, true
    );

    $related_input = [
      'type'                 => 'object',
      'default'              => [],
      'properties'           => ['search' => ['type' => 'string']] + $paging,
      'additionalProperties' => false,
    ];

    if ( Hooks::hook_filter_use_event_locations() ) {
      $this->add( 'list-locations', __( 'List Event Locations', 'basecadet' ),
        __( 'List event locations, for the location_ids field.', 'basecadet' ),
        $related_input,
        fn( $input ) => $this->list_related( Settings::$locations_machine_name, $input ),
        $can_edit, true
      );
    }

    if ( Hooks::hook_filter_use_event_series() ) {
      $this->add( 'list-series', __( 'List Event Series', 'basecadet' ),
        __( 'List event series, for the series_ids field.', 'basecadet' ),
        $related_input,
        fn( $input ) => $this->list_related( Settings::$series_machine_name, $input ),
        $can_edit, true
      );
    }
  }


  private function add( string $name, string $label, string $description, array $input_schema, callable $execute, callable $permission, bool $readonly ) : void {
    wp_register_ability( 'bc-events/' . $name, [
      'label'               => $label,
      'description'         => $description,
      'category'            => self::CATEGORY,
      'input_schema'        => $input_schema,
      'execute_callback'    => $execute,
      'permission_callback' => $permission,
      'meta'                => [
        'show_in_rest' => true,
        'mcp'          => ['public' => Hooks::hook_filter_abilities_mcp_public()],
        'annotations'  => [
          'readonly'    => $readonly,
          'destructive' => false,
          'idempotent'  => $readonly,
        ],
      ],
    ] );
  }



  // ==================================
  //  Schemas
  // ==================================


  /**
   * Editable event meta fields, keyed by logical name (see EventsMetaKeys).
   * Fields a site has switched off with a filter are left out.
   *
   * virtual_event / virtual_url are deliberately absent: no editor exposes them yet.
   *
   * @return array<string, array> JSON Schema per field.
   */
  public function event_field_schemas() : array {
    $date  = ['type' => 'string', 'pattern' => self::DATE_PATTERN];
    $time  = ['type' => 'string', 'pattern' => self::TIME_PATTERN];
    $freqs = Hooks::hook_filter_frequency_options();

    $fields = [
      'start_date'            => $date + ['description' => 'Start date, YYYY-MM-DD.'],
      'start_time'            => $time + ['description' => 'Start time, 24h HH:MM.'],
      'end_date'              => $date + ['description' => 'End date, YYYY-MM-DD.'],
      'end_time'              => $time + ['description' => 'End time, 24h HH:MM. The end must be after the start.'],
      'hide_time_display'     => ['type' => 'boolean', 'description' => 'Hide the start and end times on the site.'],
      'hide_end_time_display' => ['type' => 'boolean', 'description' => 'Hide only the end time on the site.'],

      'is_recurring'          => ['type' => 'boolean', 'description' => 'Repeat this event. Needs use_frequency, custom_occurrences, or both.'],
      'use_frequency'         => ['type' => 'boolean', 'description' => 'Repeat on a rule (freq and friends).'],
      'freq'                  => ['type' => 'string', 'enum' => array_keys( $freqs ), 'description' => 'Repeat rule: ' . implode( ', ', $freqs ) . '.'],
      'freq_days'             => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => self::DAYS], 'description' => 'weekly: the days of the week to repeat on.'],
      'freq_mo_schedule'      => ['type' => 'string', 'enum' => ['first', 'second', 'third', 'fourth', 'last', 'every_other', 'date'], 'description' => 'monthly: which weekday of the month (with freq_mo_day), or "date" for a day number (with freq_mo_date).'],
      'freq_mo_day'           => ['type' => 'string', 'enum' => self::DAYS, 'description' => 'monthly: the weekday, when freq_mo_schedule is not "date".'],
      'freq_mo_date'          => ['type' => 'string', 'pattern' => '^(0[1-9]|[12]\d|3[01])$', 'description' => 'monthly: day of the month 01-31, when freq_mo_schedule is "date".'],
      'freq_consecutive_buffer' => ['type' => 'integer', 'minimum' => 0, 'description' => 'consecutive: minutes between one occurrence ending and the next starting.'],
      'freq_consecutive_count'  => ['type' => 'integer', 'minimum' => 2, 'description' => 'consecutive: how many occurrences.'],
      'freq_end_type'         => ['type' => 'string', 'enum' => ['on_date', 'after_x'], 'description' => 'daily/weekly/monthly: stop on freq_end_date, or after freq_end_after_x occurrences.'],
      'freq_end_date'         => $date + ['description' => 'Last date the rule can produce an occurrence, YYYY-MM-DD.'],
      'freq_end_after_x'      => ['type' => 'integer', 'minimum' => 1, 'description' => 'Number of occurrences before the rule stops.'],
      'custom_occurrences'    => [
        'type'        => 'array',
        'description' => 'Specific extra dates. Each uses the parent\'s times unless customize is true.',
        'items'       => [
          'type'       => 'object',
          'properties' => [
            'start_date' => $date,
            'customize'  => ['type' => 'boolean'],
            'start_time' => $time,
            'end_date'   => $date,
            'end_time'   => $time,
          ],
          'required'             => ['start_date'],
          'additionalProperties' => false,
        ],
      ],
      'omissions'             => ['type' => 'array', 'items' => $date, 'description' => 'Dates to skip, YYYY-MM-DD.'],
    ];

    if ( Hooks::hook_filter_use_event_recurring_description() ) {
      $fields['recur_desc'] = ['type' => 'string', 'description' => Hooks::hook_filter_recurring_description_helper_text() ?: 'Human-readable description of the schedule.'];
    }

    if ( Hooks::hook_filter_use_event_locations() ) {
      $fields['location_ids'] = ['type' => 'array', 'items' => ['type' => 'integer'], 'description' => 'Location post IDs (see list-locations).'];
    }

    if ( Hooks::hook_filter_use_event_series() ) {
      $fields['series_ids'] = ['type' => 'array', 'items' => ['type' => 'integer'], 'description' => 'Series post IDs (see list-series).'];
    }

    return $fields;
  }


  private function event_input_schema( bool $update ) : array {
    $post_type  = Settings::$events_machine_name;
    $properties = [
      'title'   => ['type' => 'string'],
      'content' => ['type' => 'string', 'description' => 'Post content (block markup or HTML).'],
      'status'  => ['type' => 'string', 'enum' => self::STATUSES, 'description' => 'Defaults to draft on create.'],
      'slug'    => ['type' => 'string'],
    ];

    if ( post_type_supports( $post_type, 'excerpt' ) ) {
      $properties['excerpt'] = ['type' => 'string'];
    }

    if ( post_type_supports( $post_type, 'thumbnail' ) ) {
      $properties['featured_media'] = ['type' => 'integer', 'description' => 'Attachment ID for the featured image; 0 removes it.'];
    }

    $properties['terms'] = [
      'type'                 => 'object',
      'description'          => 'Taxonomy slug => term IDs or slugs, replacing the current terms. Only taxonomies listed by get-settings; terms are never created.',
      'additionalProperties' => ['type' => 'array', 'items' => ['type' => ['integer', 'string']]],
    ];

    $properties += $this->event_field_schemas();

    if ( $update ) {
      $properties = ['id' => ['type' => 'integer']] + $properties;
      $properties['child_deny_override'] = ['type' => 'boolean', 'description' => 'Occurrences only: keep this occurrence\'s content when the parent is saved.'];
    }

    return [
      'type'                 => 'object',
      'properties'           => $properties,
      'required'             => $update ? ['id'] : ['title', ...self::DATE_FIELDS],
      'additionalProperties' => false,
    ];
  }



  // ==================================
  //  Permissions
  // ==================================


  public function can_create( $input ) : bool {
    $caps = get_post_type_object( Settings::$events_machine_name )->cap;
    return current_user_can( $caps->create_posts ) && $this->can_set_status( $input['status'] ?? 'draft' );
  }


  public function can_update( $input ) : bool {
    return current_user_can( 'edit_post', (int) ( $input['id'] ?? 0 ) )
      && ( ! isset( $input['status'] ) || $this->can_set_status( $input['status'] ) );
  }


  private function can_set_status( string $status ) : bool {
    return in_array( $status, ['draft', 'pending'], true )
      || current_user_can( get_post_type_object( Settings::$events_machine_name )->cap->publish_posts );
  }



  // ==================================
  //  Callbacks
  // ==================================


  public function get_settings() : array {
    $post_type = Settings::$events_machine_name;

    $taxonomies = array_values( array_map(
      fn( $tax ) => [
        'slug'         => $tax->name,
        'label'        => $tax->label,
        'hierarchical' => (bool) $tax->hierarchical,
      ],
      array_filter( get_object_taxonomies( $post_type, 'objects' ), fn( $tax ) => $tax->show_ui )
    ) );

    return [
      'frequency_options'                 => Hooks::hook_filter_frequency_options(),
      'use_locations'                     => Hooks::hook_filter_use_event_locations(),
      'use_series'                        => Hooks::hook_filter_use_event_series(),
      'use_recurring_description'         => Hooks::hook_filter_use_event_recurring_description(),
      'recurring_description_helper_text' => Hooks::hook_filter_recurring_description_helper_text(),
      'supports'                          => array_keys( get_all_post_type_supports( $post_type ) ),
      'taxonomies'                        => $taxonomies,
      'statuses'                          => self::STATUSES,
      'timezone'                          => wp_timezone_string(),
    ];
  }


  public function list_events( $input ) : array {
    $k     = EventsMetaKeys::get_keys();
    $input = (array) $input;

    $meta_query = [
      'start' => ['key' => $k['start_timestamp'], 'compare' => 'EXISTS', 'type' => 'NUMERIC'],
    ];

    if ( ! empty( $input['parent_id'] ) ) {
      $meta_query[] = ['key' => $k['parent_id'], 'value' => (int) $input['parent_id']];
    } elseif ( empty( $input['include_children'] ) ) {
      $meta_query[] = [
        'relation' => 'OR',
        ['key' => $k['is_child'], 'compare' => 'NOT EXISTS'],
        ['key' => $k['is_child'], 'value' => '1', 'compare' => '!='],
      ];
    }

    if ( ! empty( $input['starts_after'] ) ) {
      $meta_query[] = [
        'key'     => $k['start_timestamp'],
        'value'   => RestRoutes::date_time_to_timestamp( $input['starts_after'] ),
        'compare' => '>=',
        'type'    => 'NUMERIC',
      ];
    }

    $query = new \WP_Query( [
      'post_type'      => Settings::$events_machine_name,
      'post_status'    => ( $input['status'] ?? 'any' ) === 'any' ? self::STATUSES : $input['status'],
      's'              => $input['search'] ?? '',
      'posts_per_page' => $input['per_page'] ?? 20,
      'paged'          => $input['page'] ?? 1,
      'meta_query'     => $meta_query,
      'orderby'        => ['start' => 'ASC'],
    ] );

    return [
      'total'  => (int) $query->found_posts,
      'pages'  => (int) $query->max_num_pages,
      'events' => array_map( fn( $post ) => $this->event_summary( $post ), $query->posts ),
    ];
  }


  public function get_event( $input ) : array|\WP_Error {
    $post = $this->get_event_post( (int) $input['id'] );
    return is_wp_error( $post ) ? $post : $this->event_data( $post );
  }


  public function create_event( $input ) : array|\WP_Error {
    $fields = $this->pick_fields( $input );
    $valid  = $this->validate_fields( $fields );

    if ( is_wp_error( $valid ) ) {
      return $valid;
    }

    $postarr = $this->build_postarr( $input, $fields, $fields );

    if ( is_wp_error( $postarr ) ) {
      return $postarr;
    }

    $postarr['post_type']   = Settings::$events_machine_name;
    $postarr['post_status'] = $postarr['post_status'] ?? 'draft';

    $post_id = $this->without_loopback( fn() => wp_insert_post( $postarr, true ) );

    return is_wp_error( $post_id ) ? $post_id : $this->event_data( get_post( $post_id ) );
  }


  public function update_event( $input ) : array|\WP_Error {
    $post = $this->get_event_post( (int) $input['id'] );

    if ( is_wp_error( $post ) ) {
      return $post;
    }

    $is_child = get_post_meta( $post->ID, EventsMetaKeys::get_keys()['is_child'], true ) === '1';
    $fields   = $this->pick_fields( $input );

    if ( isset( $fields['child_deny_override'] ) && ! $is_child ) {
      return new \WP_Error( 'bc_events_not_a_child', 'child_deny_override only applies to a recurring occurrence.' );
    }

    if ( $is_child ) {
      $blocked = array_intersect( array_keys( $fields ), [...self::DATE_FIELDS, ...self::RECUR_FIELDS, 'hide_time_display', 'hide_end_time_display'] );

      if ( $blocked ) {
        return new \WP_Error( 'bc_events_child_field', sprintf(
          'Event %d is an occurrence of event %d; set %s on the parent instead.',
          $post->ID,
          (int) get_post_meta( $post->ID, EventsMetaKeys::get_keys()['parent_id'], true ),
          implode( ', ', $blocked )
        ) );
      }
    }

    $merged = array_merge( $this->read_fields( $post->ID ), $fields );
    $valid  = $is_child ? true : $this->validate_fields( $merged );

    if ( is_wp_error( $valid ) ) {
      return $valid;
    }

    $postarr = $this->build_postarr( $input, $fields, $merged );

    if ( is_wp_error( $postarr ) ) {
      return $postarr;
    }

    $postarr['ID'] = $post->ID;

    $result = $this->without_loopback( fn() => wp_update_post( $postarr, true ) );

    return is_wp_error( $result ) ? $result : $this->event_data( get_post( $post->ID ) );
  }


  public function list_terms( $input ) : array|\WP_Error {
    $taxonomy = $input['taxonomy'];

    if ( ! is_object_in_taxonomy( Settings::$events_machine_name, $taxonomy ) ) {
      return $this->not_an_event_taxonomy( $taxonomy );
    }

    $terms = get_terms( [
      'taxonomy'   => $taxonomy,
      'hide_empty' => false,
      'search'     => $input['search'] ?? '',
      'number'     => $input['per_page'] ?? 20,
      'offset'     => ( ( $input['page'] ?? 1 ) - 1 ) * ( $input['per_page'] ?? 20 ),
    ] );

    if ( is_wp_error( $terms ) ) {
      return $terms;
    }

    return [
      'terms' => array_map( fn( $term ) => [
        'id'     => $term->term_id,
        'name'   => $term->name,
        'slug'   => $term->slug,
        'parent' => $term->parent,
        'count'  => $term->count,
      ], $terms ),
    ];
  }


  public function list_related( string $post_type, $input ) : array {
    $input = (array) $input;
    $query = new \WP_Query( [
      'post_type'      => $post_type,
      'post_status'    => ['publish', 'draft', 'private'],
      's'              => $input['search'] ?? '',
      'posts_per_page' => $input['per_page'] ?? 20,
      'paged'          => $input['page'] ?? 1,
      'orderby'        => 'title',
      'order'          => 'ASC',
    ] );

    return [
      'total' => (int) $query->found_posts,
      'items' => array_map( fn( $post ) => [
        'id'     => $post->ID,
        'title'  => get_the_title( $post ),
        'status' => $post->post_status,
      ], $query->posts ),
    ];
  }



  // ==================================
  //  Helpers
  // ==================================


  /**
   * Run a save with the recurrence queue processed inline.
   *
   * The async loopback authenticates with the caller's cookies and a
   * user-bound nonce. Ability calls usually come in with an application
   * password and no cookies, so the loopback fails its nonce and the
   * occurrences wait for the queue's cron health check. Inline, they exist
   * before the ability returns.
   */
  private function without_loopback( callable $save ) : mixed {
    add_filter( 'bc_events/background/sync', '__return_true' );

    try {
      return $save();
    } finally {
      remove_filter( 'bc_events/background/sync', '__return_true' );
    }
  }


  private function get_event_post( int $id ) : \WP_Post|\WP_Error {
    $post = get_post( $id );

    if ( ! $post || $post->post_type !== Settings::$events_machine_name || $post->post_status === 'trash' ) {
      return new \WP_Error( 'bc_events_not_found', sprintf( 'No event with ID %d.', $id ) );
    }

    return $post;
  }


  /** The event meta fields present in the input, keyed by logical name. */
  private function pick_fields( array $input ) : array {
    $known = array_keys( $this->event_field_schemas() );
    $known[] = 'child_deny_override';
    return array_intersect_key( $input, array_flip( $known ) );
  }


  /** Current values of every exposed field, keyed by logical name. */
  private function read_fields( int $post_id ) : array {
    $k      = EventsMetaKeys::get_keys();
    $values = [];

    foreach ( $this->event_field_schemas() as $name => $schema ) {
      $value = get_post_meta( $post_id, $k[$name], true );

      $values[$name] = match ( $schema['type'] ) {
        'boolean' => (bool) $value,
        'integer' => $value === '' ? null : (int) $value,
        'array'   => is_array( $value ) ? array_values( $value ) : [],
        default   => (string) $value,
      };
    }

    return $values;
  }


  /**
   * The editor's validation rules (see editor/components/_sections/Validation),
   * applied to the full set of field values.
   */
  private function validate_fields( array $f ) : true|\WP_Error {
    foreach ( self::DATE_FIELDS as $name ) {
      if ( empty( $f[$name] ) ) {
        return new \WP_Error( 'bc_events_missing_date', 'start_date, start_time, end_date and end_time are all required.' );
      }
    }

    $start = RestRoutes::date_time_to_timestamp( $f['start_date'], $f['start_time'] );
    $end   = RestRoutes::date_time_to_timestamp( $f['end_date'], $f['end_time'] );

    if ( $start === null || $end === null ) {
      return new \WP_Error( 'bc_events_invalid_date', 'Invalid start or end date/time.' );
    }

    if ( $start >= $end ) {
      return new \WP_Error( 'bc_events_invalid_range', 'The start must be before the end.' );
    }

    if ( empty( $f['is_recurring'] ) ) {
      return true;
    }

    $missing = [];

    if ( empty( $f['use_frequency'] ) && empty( $f['custom_occurrences'] ) ) {
      return new \WP_Error( 'bc_events_missing_recurrence', 'A recurring event needs use_frequency with a rule, custom_occurrences, or both.' );
    }

    if ( ! empty( $f['use_frequency'] ) ) {
      $freq = $f['freq'] ?? '';

      if ( ! array_key_exists( $freq, Hooks::hook_filter_frequency_options() ) ) {
        $missing[] = 'freq';
      }

      if ( $freq === 'weekly' && empty( $f['freq_days'] ) ) {
        $missing[] = 'freq_days';
      }

      if ( $freq === 'monthly' ) {
        $schedule = $f['freq_mo_schedule'] ?? '';

        if ( $schedule === '' ) {
          $missing[] = 'freq_mo_schedule';
        } elseif ( $schedule === 'date' && empty( $f['freq_mo_date'] ) ) {
          $missing[] = 'freq_mo_date';
        } elseif ( $schedule !== 'date' && empty( $f['freq_mo_day'] ) ) {
          $missing[] = 'freq_mo_day';
        }
      }

      if ( $freq === 'consecutive' ) {
        if ( empty( $f['freq_consecutive_count'] ) ) {
          $missing[] = 'freq_consecutive_count';
        }
      } elseif ( $freq !== '' ) {
        $end_type = ( $f['freq_end_type'] ?? '' ) ?: 'on_date';

        if ( $end_type === 'on_date' && empty( $f['freq_end_date'] ) ) {
          $missing[] = 'freq_end_date';
        }

        if ( $end_type === 'after_x' && empty( $f['freq_end_after_x'] ) ) {
          $missing[] = 'freq_end_after_x';
        }
      }
    }

    return $missing
      ? new \WP_Error( 'bc_events_missing_fields', 'Missing or invalid for this recurrence: ' . implode( ', ', $missing ) . '.' )
      : true;
  }


  /**
   * Build the wp_insert_post/wp_update_post array. Meta and terms go in with the
   * post (meta_input/tax_input), so they're in place before wp_after_insert_post
   * runs the recurrence engine and copies them to the occurrences.
   *
   * @param array $input  Raw ability input.
   * @param array $fields Event fields being written.
   * @param array $all    Every field value after the write, for the timestamps.
   */
  private function build_postarr( array $input, array $fields, array $all ) : array|\WP_Error {
    $k       = EventsMetaKeys::get_keys();
    $postarr = [];

    foreach ( ['title' => 'post_title', 'content' => 'post_content', 'excerpt' => 'post_excerpt', 'status' => 'post_status', 'slug' => 'post_name'] as $from => $to ) {
      if ( isset( $input[$from] ) ) {
        $postarr[$to] = $input[$from];
      }
    }

    if ( isset( $input['featured_media'] ) ) {
      if ( $input['featured_media'] && get_post_type( $input['featured_media'] ) !== 'attachment' ) {
        return new \WP_Error( 'bc_events_invalid_media', sprintf( 'Attachment %d does not exist.', $input['featured_media'] ) );
      }
      $postarr['_thumbnail_id'] = $input['featured_media'] ?: -1;
    }

    if ( ! empty( $input['terms'] ) ) {
      $terms = $this->resolve_terms( $input['terms'] );

      if ( is_wp_error( $terms ) ) {
        return $terms;
      }

      $postarr['tax_input'] = $terms;
    }

    $meta = [];

    foreach ( $fields as $name => $value ) {
      if ( $name === 'custom_occurrences' ) {
        $value = array_map( fn( $row ) => array_merge(
          ['start_date' => '', 'customize' => false, 'start_time' => '', 'end_date' => '', 'end_time' => ''],
          $row
        ), $value );
      }

      $meta[$k[$name]] = $value;
    }

    if ( array_intersect_key( $fields, array_flip( self::DATE_FIELDS ) ) ) {
      $meta[$k['start_timestamp']] = RestRoutes::date_time_to_timestamp( $all['start_date'], $all['start_time'] );
      $meta[$k['end_timestamp']]   = RestRoutes::date_time_to_timestamp( $all['end_date'], $all['end_time'] );
    }

    if ( $meta ) {
      $postarr['meta_input'] = $meta;
    }

    return $postarr;
  }


  /**
   * Resolve term IDs/slugs/names to IDs, only for taxonomies attached to events.
   * Terms are never created here.
   *
   * @return array<string, int[]>|\WP_Error
   */
  private function resolve_terms( array $input_terms ) : array|\WP_Error {
    $resolved = [];

    foreach ( $input_terms as $taxonomy => $terms ) {
      if ( ! is_object_in_taxonomy( Settings::$events_machine_name, $taxonomy ) ) {
        return $this->not_an_event_taxonomy( $taxonomy );
      }

      if ( ! current_user_can( get_taxonomy( $taxonomy )->cap->assign_terms ) ) {
        return new \WP_Error( 'bc_events_cannot_assign_terms', sprintf( 'You cannot assign %s terms.', $taxonomy ) );
      }

      $resolved[$taxonomy] = [];

      foreach ( $terms as $term ) {
        $found = is_int( $term )
          ? get_term( $term, $taxonomy )
          : ( get_term_by( 'slug', $term, $taxonomy ) ?: get_term_by( 'name', $term, $taxonomy ) );

        if ( ! $found || is_wp_error( $found ) ) {
          return new \WP_Error( 'bc_events_term_not_found', sprintf( 'No %s term "%s". Use bc-events/list-terms; terms are not created here.', $taxonomy, $term ) );
        }

        $resolved[$taxonomy][] = $found->term_id;
      }
    }

    return $resolved;
  }


  private function not_an_event_taxonomy( string $taxonomy ) : \WP_Error {
    return new \WP_Error( 'bc_events_invalid_taxonomy', sprintf( '"%s" is not a taxonomy on events. See bc-events/get-settings.', $taxonomy ) );
  }


  private function event_summary( \WP_Post $post ) : array {
    $k = EventsMetaKeys::get_keys();

    return [
      'id'           => $post->ID,
      'title'        => get_the_title( $post ),
      'status'       => $post->post_status,
      'start_date'   => get_post_meta( $post->ID, $k['start_date'], true ),
      'start_time'   => get_post_meta( $post->ID, $k['start_time'], true ),
      'end_date'     => get_post_meta( $post->ID, $k['end_date'], true ),
      'end_time'     => get_post_meta( $post->ID, $k['end_time'], true ),
      'is_recurring' => (bool) get_post_meta( $post->ID, $k['is_recurring'], true ),
      'is_child'     => (bool) get_post_meta( $post->ID, $k['is_child'], true ),
      'parent_id'    => (int) get_post_meta( $post->ID, $k['parent_id'], true ) ?: null,
      'link'         => get_permalink( $post ),
    ];
  }


  private function event_data( \WP_Post $post ) : array {
    $k    = EventsMetaKeys::get_keys();
    $data = $this->event_summary( $post ) + [
      'slug'           => $post->post_name,
      'content'        => $post->post_content,
      'excerpt'        => $post->post_excerpt,
      'featured_media' => (int) get_post_thumbnail_id( $post ),
      'fields'         => $this->read_fields( $post->ID ),
      'terms'          => [],
    ];

    if ( $data['is_child'] ) {
      $data['fields']['child_deny_override'] = (bool) get_post_meta( $post->ID, $k['child_deny_override'], true );
    }

    foreach ( get_object_taxonomies( $post->post_type ) as $taxonomy ) {
      $terms = wp_get_object_terms( $post->ID, $taxonomy );
      $data['terms'][$taxonomy] = is_wp_error( $terms ) ? [] : array_map(
        fn( $term ) => ['id' => $term->term_id, 'name' => $term->name, 'slug' => $term->slug],
        $terms
      );
    }

    if ( $data['is_recurring'] && ! $data['is_child'] ) {
      $data['occurrence_ids'] = get_posts( [
        'post_type'      => $post->post_type,
        'post_status'    => 'any',
        'fields'         => 'ids',
        'posts_per_page' => -1,
        'meta_query'     => [
          ['key' => $k['parent_id'], 'value' => $post->ID],
          'start' => ['key' => $k['start_timestamp'], 'type' => 'NUMERIC'],
        ],
        'orderby'        => ['start' => 'ASC'],
      ] );
    }

    return $data;
  }

}
