<?php
/**
 * The vehicle card — WooCommerce `content-product.php`.
 *
 * Runs inside the products loop with the global `$product`. Used by the
 * catalogue archive and rendered into the Concierge AJAX results. There is
 * deliberately NO add-to-cart anywhere: the primary action opens the enquiry
 * modal, and a secondary toggle adds the vehicle to the compare tray.
 *
 * Markup matches the CSS contract in style.css (section 07) and uses the
 * helpers defined in functions.php. Every `_vehicle_*` read tolerates a
 * missing value.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$vehicle_id = $product->get_id();
$permalink  = get_permalink( $vehicle_id );

/*
 * Pricing. Prefer the explicit `_vehicle_price` meta, fall back to the
 * WooCommerce product price. Monthly prefers the precomputed
 * `_vehicle_monthly_from` meta, else derive it from the cash price.
 */
$price_meta = digicars_meta( $vehicle_id, '_vehicle_price' );
$price      = is_numeric( $price_meta ) ? (float) $price_meta : (float) $product->get_price();

$monthly_meta = digicars_meta( $vehicle_id, '_vehicle_monthly_from' );
$monthly      = is_numeric( $monthly_meta ) ? (int) $monthly_meta : digicars_monthly_from( $price );

/*
 * Identity. Compose a clean title from year + make + model + variant; fall
 * back to the product name if no identity meta is present.
 */
$make    = trim( (string) digicars_meta( $vehicle_id, '_vehicle_make' ) );
$model   = trim( (string) digicars_meta( $vehicle_id, '_vehicle_model' ) );
$variant = trim( (string) digicars_meta( $vehicle_id, '_vehicle_variant' ) );
$year    = (int) digicars_meta( $vehicle_id, '_vehicle_year' );

$title_parts = array();
if ( $year > 0 ) {
	$title_parts[] = (string) $year;
}
if ( '' !== $make ) {
	$title_parts[] = $make;
}
if ( '' !== $model ) {
	$title_parts[] = $model;
}
if ( '' !== $variant ) {
	$title_parts[] = $variant;
}

$title = ! empty( $title_parts ) ? implode( ' ', $title_parts ) : $product->get_name();

// Eyebrow: the make (uppercased) or, failing that, the leading title word.
$eyebrow = '' !== $make ? $make : $product->get_name();

// Condition badge: the first badge returned by the helper, shown top-left.
$badges          = digicars_vehicle_badges( $product );
$condition_badge = ! empty( $badges ) ? $badges[0] : null;

// Specs. Skip empties so new cars without mileage stay tidy.
$mileage      = (int) digicars_meta( $vehicle_id, '_vehicle_mileage' );
$transmission = trim( (string) digicars_meta( $vehicle_id, '_vehicle_transmission' ) );
$fuel         = trim( (string) digicars_meta( $vehicle_id, '_vehicle_fuel' ) );

// Image with a defensive theme fallback.
if ( $product->get_image_id() ) {
	$image = $product->get_image( 'digicars-card' );
} else {
	$image = sprintf(
		'<img src="%1$s" alt="%2$s" width="640" height="420" loading="lazy" decoding="async" />',
		esc_url( get_theme_file_uri( 'images/vehicles/_default.svg' ) ),
		esc_attr( $title )
	);
}
?>
<li <?php wc_product_class( 'vehicle-card', $product ); ?>>

	<div class="vehicle-card__media">
		<a class="vehicle-card__media-link" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WC image markup / escaped fallback. ?>
		</a>

		<?php if ( $condition_badge ) : ?>
			<span class="badge badge--<?php echo esc_attr( $condition_badge['tone'] ); ?>">
				<?php echo esc_html( $condition_badge['label'] ); ?>
			</span>
		<?php endif; ?>

		<button
			class="vehicle-card__compare"
			type="button"
			data-compare-toggle
			data-vehicle-id="<?php echo esc_attr( $vehicle_id ); ?>"
			aria-pressed="false"
			aria-label="<?php esc_attr_e( 'Add to compare', 'digicars' ); ?>"
		><?php esc_html_e( 'Compare', 'digicars' ); ?></button>
	</div>

	<div class="vehicle-card__body">
		<p class="vehicle-card__make eyebrow"><?php echo esc_html( strtoupper( $eyebrow ) ); ?></p>

		<div class="vehicle-card__price">
			<span class="vehicle-card__price-main">R <?php echo esc_html( number_format( $price, 0, '.', ' ' ) ); ?></span>
			<span class="vehicle-card__price-sub">
				<?php
				/* translators: %s: monthly instalment, formatted in Rand. */
				printf( esc_html__( 'From R %s pm*', 'digicars' ), esc_html( number_format( $monthly, 0, '.', ' ' ) ) );
				?>
			</span>
		</div>

		<h3 class="vehicle-card__title t-3">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
		</h3>

		<?php if ( $mileage > 0 || '' !== $transmission || '' !== $fuel ) : ?>
			<ul class="vehicle-card__specs">
				<?php if ( $mileage > 0 ) : ?>
					<li class="vehicle-card__spec">
						<?php
						/* translators: %s: mileage in kilometres, formatted. */
						printf( esc_html__( '%s km', 'digicars' ), esc_html( number_format( $mileage, 0, '.', ' ' ) ) );
						?>
					</li>
				<?php endif; ?>
				<?php if ( '' !== $transmission ) : ?>
					<li class="vehicle-card__spec"><?php echo esc_html( $transmission ); ?></li>
				<?php endif; ?>
				<?php if ( '' !== $fuel ) : ?>
					<li class="vehicle-card__spec"><?php echo esc_html( $fuel ); ?></li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>

		<div class="vehicle-card__action">
			<button
				class="btn btn--signal btn--sm"
				type="button"
				data-enquire
				data-vehicle-id="<?php echo esc_attr( $vehicle_id ); ?>"
				data-vehicle-name="<?php echo esc_attr( $title ); ?>"
			><?php esc_html_e( 'Enquire now', 'digicars' ); ?></button>
		</div>
	</div>
</li>
