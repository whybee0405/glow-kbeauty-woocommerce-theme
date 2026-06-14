<?php
/**
 * Product card used in all shop loops.
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$glow_brand   = glow_meta( $product->get_id(), '_product_brand' );
$glow_step_no = glow_meta( $product->get_id(), '_product_routine_step' );
$glow_badges  = glow_product_badges( $product );
$glow_quick   = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
?>
<li <?php wc_product_class( 'product-card', $product ); ?>>
	<div class="card-media">
		<?php if ( $glow_badges ) : ?>
			<div class="card-badges">
				<?php foreach ( $glow_badges as $glow_badge ) : ?>
					<span class="badge <?php echo esc_attr( $glow_badge['class'] ); ?>"><?php echo esc_html( $glow_badge['label'] ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<button
			class="wishlist-btn"
			type="button"
			data-wishlist="<?php echo esc_attr( $product->get_id() ); ?>"
			aria-pressed="false"
			aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name. */ __( 'Save %s to wishlist', 'glow-kbeauty' ), $product->get_name() ) ); ?>"
		>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20.5C7 16.6 3.5 13.3 3.5 9.6 3.5 7 5.5 5 8 5c1.6 0 3.1.8 4 2.1C12.9 5.8 14.4 5 16 5c2.5 0 4.5 2 4.5 4.6 0 3.7-3.5 7-8.5 10.9Z"/></svg>
		</button>

		<a class="card-image-link" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php glow_product_image( $product ); ?>
		</a>

		<?php if ( $glow_quick ) : ?>
			<button class="quick-add" type="button" data-quick-add="<?php echo esc_attr( $product->get_id() ); ?>">
				<?php esc_html_e( 'Add to bag', 'glow-kbeauty' ); ?>
				<span class="mono"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			</button>
		<?php else : ?>
			<a class="quick-add" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View product', 'glow-kbeauty' ); ?></a>
		<?php endif; ?>
	</div>

	<div class="card-body">
		<p class="card-meta mono">
			<?php if ( $glow_brand ) : ?>
				<span class="card-brand"><?php echo esc_html( $glow_brand ); ?></span>
			<?php endif; ?>
			<?php if ( $glow_step_no ) : ?>
				<span class="card-step"><?php echo esc_html( sprintf( /* translators: %s: routine step number. */ __( 'STEP %s', 'glow-kbeauty' ), str_pad( (string) (int) $glow_step_no, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
			<?php endif; ?>
		</p>

		<h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<div class="card-foot">
			<?php if ( $product->get_review_count() > 0 ) : ?>
				<span class="card-rating">
					<?php glow_stars( (float) $product->get_average_rating() ); ?>
					<span class="mono review-count">(<?php echo esc_html( $product->get_review_count() ); ?>)</span>
				</span>
			<?php endif; ?>
			<span class="card-price mono"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
		</div>
	</div>
</li>
