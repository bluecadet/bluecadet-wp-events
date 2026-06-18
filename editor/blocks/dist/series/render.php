<?php
/**
 * Event Dates block — frontend render.
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

$post_id     = $block->context['postId'] ?? get_the_ID();
$address     = get_post_meta( $post_id, 'bc_events_location_address',     true );
$description = get_post_meta( $post_id, 'bc_events_location_description', true );
$website     = get_post_meta( $post_id, 'bc_events_location_website',     true );

?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'bc-event-dates' ] ); ?>>
	<?php if ( $address ) : ?>
		<div class="bc-event-dates__group bc-event-dates__group--start">
			<p><?=  $address ?></p>
		</div>
	<?php endif; ?>
	<?php if ( $description ) : ?>
		<div class="bc-event-dates__group bc-event-dates__group--description">
			<p><?=  $description ?></p>
		</div>
	<?php endif; ?>
	<?php if ( $website ) : ?>
		<div class="bc-event-dates__group bc-event-dates__group--website">
			<a href="<?= esc_url( $website ) ?>"><?=  $website ?></a>
		</div>
	<?php endif; ?>
</div>
