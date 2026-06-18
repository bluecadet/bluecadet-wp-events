<?php
/**
 * Event Dates block — frontend render.
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

$post_id         = $block->context['postId'] ?? get_the_ID();
$start_date      = get_post_meta( $post_id, 'bc_events_start_date',            true );
$start_time      = get_post_meta( $post_id, 'bc_events_start_time',            true );
$end_date        = get_post_meta( $post_id, 'bc_events_end_date',              true );
$end_time        = get_post_meta( $post_id, 'bc_events_end_time',              true );
$hide_time       = (bool) get_post_meta( $post_id, 'bc_events_hide_time_display',     true );
$hide_end_time   = (bool) get_post_meta( $post_id, 'bc_events_hide_end_time_display', true );
$is_virtual      = (bool) get_post_meta( $post_id, 'bc_events_virtual_event',         true );
$virtual_url     = get_post_meta( $post_id, 'bc_events_virtual_url',           true );

$location_ids = get_post_meta($post_id, 'bc_events_location_ids', true );
$locations = [];
if ( is_array( $location_ids ) ) {
	foreach ( $location_ids as $location_id ) {
		$locations[] = get_the_title($location_id);
	}
}

$series_ids = get_post_meta($post_id, 'bc_events_series_ids', true );
$seriess = [];
if ( is_array( $series_ids ) ) {
	foreach ( $series_ids as $series_id ) {
		$seriess[] = get_the_title($series_id);
	}
}

if ( ! $start_date && ! $end_date ) {
	return;
}

$format_date = static function ( string $date ): string {
	$ts = strtotime( $date );
	return $ts ? date_i18n( get_option( 'date_format' ), $ts ) : esc_html( $date );
};

$format_time = static function ( string $time ): string {
	$ts = strtotime( $time );
	return $ts ? date_i18n( get_option( 'time_format' ), $ts ) : esc_html( $time );
};

$datetime_attr = static function ( string $date, string $time ): string {
	return $time ? "{$date}T{$time}" : $date;
};
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'bc-event-dates' ] ); ?>>
	<?php if ( $start_date ) : ?>
		<div class="bc-event-dates__group bc-event-dates__group--start">
			<span class="bc-event-dates__label"><?php esc_html_e( 'Start', 'basecadet' ); ?></span>
			<time class="bc-event-dates__value" datetime="<?php echo esc_attr( $datetime_attr( $start_date, $start_time ) ); ?>">
				<?php echo esc_html( $format_date( $start_date ) ); ?>
				<?php if ( $start_time && ! $hide_time ) : ?>
					<span class="bc-event-dates__time"><?php echo esc_html( $format_time( $start_time ) ); ?></span>
				<?php endif; ?>
			</time>
		</div>
	<?php endif; ?>
	<?php if ( $end_date ) : ?>
		<div class="bc-event-dates__group bc-event-dates__group--end">
			<span class="bc-event-dates__label"><?php esc_html_e( 'End', 'basecadet' ); ?></span>
			<time class="bc-event-dates__value" datetime="<?php echo esc_attr( $datetime_attr( $end_date, $end_time ) ); ?>">
				<?php echo esc_html( $format_date( $end_date ) ); ?>
				<?php if ( $end_time && ! $hide_time && ! $hide_end_time ) : ?>
					<span class="bc-event-dates__time"><?php echo esc_html( $format_time( $end_time ) ); ?></span>
				<?php endif; ?>
			</time>
		</div>
	<?php endif; ?>
	<?php if ( $is_virtual ) : ?>
		<div class="bc-event-dates__group bc-event-dates__group--virtual">
			<span class="bc-event-dates__label"><?php esc_html_e( 'Virtual Event', 'basecadet' ); ?></span>
			<?php if ( $virtual_url ) : ?>
				<a class="bc-event-dates__virtual-url" href="<?php echo esc_url( $virtual_url ); ?>">
					<?php echo esc_html( $virtual_url ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $locations && count( $locations ) > 0 ) : ?>
		<div class="bc-event-dates__group bc-event-dates__group--location">
			<span class="bc-event-dates__label"><?php esc_html_e( 'Location', 'basecadet' ); ?></span>
			<span class="bc-event-dates__location-names">
				<?php echo esc_html( implode( ', ', $locations ) ); ?>
			</span>
		</div>
	<?php endif; ?>

	<?php if ( $seriess && count( $seriess ) > 0 ) : ?>
		<div class="bc-event-dates__group bc-event-dates__group--series">
			<span class="bc-event-dates__label"><?php esc_html_e( 'Series', 'basecadet' ); ?></span>
			<span class="bc-event-dates__series-names">
				<?php echo esc_html( implode( ', ', $seriess ) ); ?>
			</span>
		</div>
	<?php endif; ?>
</div>
