<?php
/**
 * Single vehicle detail page (WooCommerce `single-product.php`).
 *
 * The digital-first vehicle PDP for Digicars. There is NO cart or
 * add-to-cart anywhere — the funnel is enquiry + finance. The page is built
 * from the global `$product` plus the `_vehicle_*` meta substrate, every read
 * of which tolerates a missing value.
 *
 * Structure:
 *   1. Breadcrumb + condition badge
 *   2. Hero split — gallery (left) + buy/assess panel (right)
 *   3. Key spec grid
 *   4. Affordability calculator mount (bound by affordability.js)
 *   5. Detail accordions (Overview / Full specs / Features / Finance)
 *   6. "Keep looking" — related vehicles
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

get_header( 'shop' );

/*
 * Resolve the product. Prefer the global set by the WC template loader; fall
 * back to the queried post; bail gracefully if there is still nothing.
 */
global $product;

if ( ! $product instanceof WC_Product && function_exists( 'wc_get_product' ) && function_exists( 'get_the_ID' ) ) {
	$product = wc_get_product( get_the_ID() );
}

if ( ! $product instanceof WC_Product ) {
	?>
	<div class="container section">
		<p class="muted"><?php esc_html_e( 'This vehicle could not be found.', 'digicars' ); ?></p>
	</div>
	<?php
	get_footer( 'shop' );
	return;
}

$vehicle_id = $product->get_id();

/* -------------------------------------------------------------------------
 * Identity — compose "Year Make Model Variant".
 * ---------------------------------------------------------------------- */
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
$title   = ! empty( $title_parts ) ? implode( ' ', $title_parts ) : $product->get_name();
$eyebrow = '' !== $make ? $make : $product->get_name();

/* -------------------------------------------------------------------------
 * Pricing — prefer explicit meta, derive monthly when absent.
 * ---------------------------------------------------------------------- */
$price_meta = digicars_meta( $vehicle_id, '_vehicle_price' );
$price      = is_numeric( $price_meta ) ? (float) $price_meta : (float) $product->get_price();

$monthly_meta = digicars_meta( $vehicle_id, '_vehicle_monthly_from' );
$monthly      = is_numeric( $monthly_meta ) && (int) $monthly_meta > 0
	? (int) $monthly_meta
	: ( $price > 0 ? digicars_monthly_from( $price ) : 0 );

/* -------------------------------------------------------------------------
 * Rating.
 * ---------------------------------------------------------------------- */
$rating       = (float) $product->get_average_rating();
$rating_count = method_exists( $product, 'get_rating_count' ) ? (int) $product->get_rating_count() : 0;

/* -------------------------------------------------------------------------
 * Badges (condition / EV / featured).
 * ---------------------------------------------------------------------- */
$badges          = digicars_vehicle_badges( $product );
$condition_badge = ! empty( $badges ) ? $badges[0] : null;

/* -------------------------------------------------------------------------
 * Lifestyle tags.
 * ---------------------------------------------------------------------- */
$lifestyle_tags = digicars_meta( $vehicle_id, '_vehicle_lifestyle_tags' );
$lifestyle_tags = is_array( $lifestyle_tags ) ? array_filter( array_map( 'trim', $lifestyle_tags ) ) : array();

/* -------------------------------------------------------------------------
 * Main image (with defensive theme fallback).
 * ---------------------------------------------------------------------- */
if ( $product->get_image_id() ) {
	$hero_image = $product->get_image( 'large' );
} else {
	$hero_image = sprintf(
		'<img src="%1$s" alt="%2$s" width="960" height="640" decoding="async" />',
		esc_url( get_theme_file_uri( 'images/vehicles/_default.jpg' ) ),
		esc_attr( $title )
	);
}

/* -------------------------------------------------------------------------
 * Key spec rows — label => value. Only non-empty values are rendered.
 * ---------------------------------------------------------------------- */
$mileage      = (int) digicars_meta( $vehicle_id, '_vehicle_mileage' );
$transmission = trim( (string) digicars_meta( $vehicle_id, '_vehicle_transmission' ) );
$drivetrain   = trim( (string) digicars_meta( $vehicle_id, '_vehicle_drivetrain' ) );
$fuel         = trim( (string) digicars_meta( $vehicle_id, '_vehicle_fuel' ) );
$engine       = trim( (string) digicars_meta( $vehicle_id, '_vehicle_engine' ) );
$power_kw     = (int) digicars_meta( $vehicle_id, '_vehicle_power_kw' );
$economy      = (float) digicars_meta( $vehicle_id, '_vehicle_fuel_economy' );
$range_km     = (int) digicars_meta( $vehicle_id, '_vehicle_range_km' );
$seats        = (int) digicars_meta( $vehicle_id, '_vehicle_seats' );
$doors        = (int) digicars_meta( $vehicle_id, '_vehicle_doors' );
$body_type    = trim( (string) digicars_meta( $vehicle_id, '_vehicle_body_type' ) );
$colour       = trim( (string) digicars_meta( $vehicle_id, '_vehicle_colour' ) );
$safety       = (int) digicars_meta( $vehicle_id, '_vehicle_safety_rating' );
$service_plan = trim( (string) digicars_meta( $vehicle_id, '_vehicle_service_plan' ) );
$warranty     = trim( (string) digicars_meta( $vehicle_id, '_vehicle_warranty' ) );
$stock_no     = trim( (string) digicars_meta( $vehicle_id, '_vehicle_stock_no' ) );
$dealer       = trim( (string) digicars_meta( $vehicle_id, '_vehicle_dealer' ) );
$province     = trim( (string) digicars_meta( $vehicle_id, '_vehicle_province' ) );

$spec_rows = array();
if ( $year > 0 ) {
	$spec_rows[ __( 'Year', 'digicars' ) ] = (string) $year;
}
if ( $mileage > 0 ) {
	/* translators: %s: mileage in kilometres, formatted. */
	$spec_rows[ __( 'Mileage', 'digicars' ) ] = sprintf( __( '%s km', 'digicars' ), number_format( $mileage, 0, '.', ' ' ) );
}
if ( '' !== $transmission ) {
	$spec_rows[ __( 'Transmission', 'digicars' ) ] = $transmission;
}
if ( '' !== $drivetrain ) {
	$spec_rows[ __( 'Drivetrain', 'digicars' ) ] = $drivetrain;
}
if ( '' !== $fuel ) {
	$spec_rows[ __( 'Fuel', 'digicars' ) ] = $fuel;
}
if ( '' !== $engine ) {
	$spec_rows[ __( 'Engine', 'digicars' ) ] = $engine;
}
if ( $power_kw > 0 ) {
	/* translators: %s: power output in kilowatts. */
	$spec_rows[ __( 'Power', 'digicars' ) ] = sprintf( __( '%s kW', 'digicars' ), number_format( $power_kw, 0, '.', ' ' ) );
}
if ( $economy > 0 ) {
	/* translators: %s: fuel economy in litres per 100km. */
	$spec_rows[ __( 'Fuel economy', 'digicars' ) ] = sprintf( __( '%s l/100km', 'digicars' ), number_format( $economy, 1, '.', ' ' ) );
}
if ( $range_km > 0 ) {
	/* translators: %s: electric range in kilometres. */
	$spec_rows[ __( 'Range', 'digicars' ) ] = sprintf( __( '%s km', 'digicars' ), number_format( $range_km, 0, '.', ' ' ) );
}
if ( $seats > 0 ) {
	$spec_rows[ __( 'Seats', 'digicars' ) ] = (string) $seats;
}
if ( $doors > 0 ) {
	$spec_rows[ __( 'Doors', 'digicars' ) ] = (string) $doors;
}
if ( '' !== $body_type ) {
	$spec_rows[ __( 'Body type', 'digicars' ) ] = $body_type;
}
if ( '' !== $colour ) {
	$spec_rows[ __( 'Colour', 'digicars' ) ] = $colour;
}
if ( $safety > 0 ) {
	/* translators: %d: safety star rating. */
	$spec_rows[ __( 'Safety rating', 'digicars' ) ] = sprintf( _n( '%d star', '%d stars', $safety, 'digicars' ), $safety );
}
if ( '' !== $service_plan ) {
	$spec_rows[ __( 'Service plan', 'digicars' ) ] = $service_plan;
}
if ( '' !== $warranty ) {
	$spec_rows[ __( 'Warranty', 'digicars' ) ] = $warranty;
}
if ( '' !== $stock_no ) {
	$spec_rows[ __( 'Stock no', 'digicars' ) ] = $stock_no;
}
if ( '' !== $dealer ) {
	$spec_rows[ __( 'Dealer', 'digicars' ) ] = $dealer;
}
if ( '' !== $province ) {
	$spec_rows[ __( 'Province', 'digicars' ) ] = $province;
}

/* -------------------------------------------------------------------------
 * Features list.
 * ---------------------------------------------------------------------- */
$features = digicars_meta( $vehicle_id, '_vehicle_features' );
$features = is_array( $features ) ? array_filter( array_map( 'trim', $features ) ) : array();

/* -------------------------------------------------------------------------
 * Concierge summary + finance disclosure copy.
 * ---------------------------------------------------------------------- */
$ai_summary   = digicars_build_ai_summary( $vehicle_id );
$finance_url  = home_url( '/finance' );
$finance_note = __( 'Finance is available through all major South African banks. Apply online and a Digicars consultant will structure a deal around your budget.', 'digicars' );
$disclosure   = __( 'Prices include VAT. Monthly estimate at 11.5% over 72 months, subject to approval.', 'digicars' );
?>

<main id="primary" class="site-main pdp" data-vehicle-id="<?php echo esc_attr( $vehicle_id ); ?>">

	<?php /* 1. Breadcrumb + condition badge ----------------------------------- */ ?>
	<div class="pdp-topbar surface-soft">
		<div class="container cluster cluster--between">
			<?php
			if ( function_exists( 'woocommerce_breadcrumb' ) ) {
				woocommerce_breadcrumb();
			} else {
				echo '<nav class="woocommerce-breadcrumb"><a href="' . esc_url( home_url( '/shop' ) ) . '">' . esc_html__( 'Vehicles', 'digicars' ) . '</a> <span class="breadcrumb-sep">&rsaquo;</span> ' . esc_html( $title ) . '</nav>';
			}
			?>
			<?php if ( $condition_badge ) : ?>
				<span class="badge badge--<?php echo esc_attr( $condition_badge['tone'] ); ?>">
					<?php echo esc_html( $condition_badge['label'] ); ?>
				</span>
			<?php endif; ?>
		</div>
	</div>

	<?php /* 2. Hero split — gallery + buy/assess panel ------------------------- */ ?>
	<section class="section section--tight">
		<div class="container">
			<div class="hero-split pdp-hero">

				<?php /* LEFT — gallery */ ?>
				<div class="pdp-gallery">
					<div class="pdp-gallery__main hero-split__stage">
						<?php echo $hero_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WC image markup / escaped fallback. ?>
					</div>
					<ul class="pdp-gallery__thumbs">
						<?php for ( $thumb = 0; $thumb < 4; $thumb++ ) : ?>
							<li class="pdp-gallery__thumb<?php echo 0 === $thumb ? ' is-active' : ''; ?>">
								<?php echo $hero_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
							</li>
						<?php endfor; ?>
					</ul>
				</div>

				<?php /* RIGHT — buy/assess panel */ ?>
				<div class="pdp-panel">
					<p class="eyebrow"><?php echo esc_html( strtoupper( $eyebrow ) ); ?></p>
					<h1 class="t-1 pdp-panel__title"><?php echo esc_html( $title ); ?></h1>

					<div class="pdp-panel__rating">
						<?php echo digicars_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes its own output. ?>
						<?php if ( $rating_count > 0 ) : ?>
							<span class="pdp-panel__rating-count muted">
								<?php
								/* translators: %d: number of reviews. */
								printf( esc_html( _n( '%d review', '%d reviews', $rating_count, 'digicars' ) ), (int) $rating_count );
								?>
							</span>
						<?php endif; ?>
					</div>

					<div class="pdp-panel__price">
						<span class="pdp-panel__price-main">R <?php echo esc_html( number_format( $price, 0, '.', ' ' ) ); ?></span>
						<?php if ( $monthly > 0 ) : ?>
							<span class="pdp-panel__price-sub t-mono">
								<?php
								/* translators: %s: monthly instalment, formatted in Rand. */
								printf( esc_html__( 'From R %s pm*', 'digicars' ), esc_html( number_format( $monthly, 0, '.', ' ' ) ) );
								?>
							</span>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $lifestyle_tags ) ) : ?>
						<ul class="pdp-panel__tags cluster">
							<?php foreach ( $lifestyle_tags as $tag ) : ?>
								<li class="chip" aria-disabled="true"><?php echo esc_html( ucwords( str_replace( '-', ' ', $tag ) ) ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<div class="pdp-panel__cta">
						<button
							class="btn btn--signal btn--lg btn--block"
							type="button"
							data-enquire
							data-vehicle-id="<?php echo esc_attr( $vehicle_id ); ?>"
							data-vehicle-name="<?php echo esc_attr( $title ); ?>"
						><?php esc_html_e( 'Enquire now', 'digicars' ); ?></button>

						<div class="pdp-panel__cta-row cluster">
							<a class="btn btn--outline" href="<?php echo esc_url( $finance_url ); ?>">
								<?php esc_html_e( 'Apply for finance', 'digicars' ); ?>
							</a>
							<button
								class="btn btn--outline"
								type="button"
								data-enquire
								data-topic="test-drive"
								data-vehicle-id="<?php echo esc_attr( $vehicle_id ); ?>"
								data-vehicle-name="<?php echo esc_attr( $title ); ?>"
							><?php esc_html_e( 'Book a test drive', 'digicars' ); ?></button>
						</div>

						<button
							class="btn btn--ghost pdp-panel__compare"
							type="button"
							data-compare-toggle
							data-vehicle-id="<?php echo esc_attr( $vehicle_id ); ?>"
							aria-pressed="false"
						><?php esc_html_e( 'Add to compare', 'digicars' ); ?></button>
					</div>

					<ul class="pdp-panel__assurance">
						<li><?php esc_html_e( 'Every unit independently verified', 'digicars' ); ?></li>
						<li><?php esc_html_e( 'Finance from all major SA banks', 'digicars' ); ?></li>
						<li><?php esc_html_e( 'Nationwide delivery to your door', 'digicars' ); ?></li>
					</ul>
				</div>

			</div>
		</div>
	</section>

	<?php /* 3. Key spec grid -------------------------------------------------- */ ?>
	<?php if ( ! empty( $spec_rows ) ) : ?>
		<section class="section section--tight section--flush-top">
			<div class="container">
				<div class="section-head">
					<div class="section-head__copy">
						<p class="eyebrow"><?php esc_html_e( 'At a glance', 'digicars' ); ?></p>
						<h2 class="t-2"><?php esc_html_e( 'Key specifications', 'digicars' ); ?></h2>
					</div>
				</div>
				<dl class="pdp-specs">
					<?php foreach ( $spec_rows as $label => $value ) : ?>
						<div class="pdp-specs__row">
							<dt class="pdp-specs__label"><?php echo esc_html( $label ); ?></dt>
							<dd class="pdp-specs__value t-mono"><?php echo esc_html( $value ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</section>
	<?php endif; ?>

	<?php /* 4. Affordability calculator mount --------------------------------- */ ?>
	<section class="section section--tight section--flush-top">
		<div class="container">
			<div
				class="pdp-afford surface-carbon"
				data-affordability
				data-price="<?php echo esc_attr( $price ); ?>"
			>
				<div class="pdp-afford__intro">
					<p class="eyebrow"><?php esc_html_e( 'Affordability', 'digicars' ); ?></p>
					<h2 class="t-2"><?php esc_html_e( 'Estimate your monthly', 'digicars' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'Adjust the deposit, term and balloon to see an indicative instalment. Final figures are confirmed on approval.', 'digicars' ); ?></p>
				</div>

				<form class="pdp-afford__form" novalidate>
					<label class="pdp-afford__field">
						<span class="pdp-afford__field-label"><?php esc_html_e( 'Deposit (R)', 'digicars' ); ?></span>
						<input type="number" inputmode="numeric" min="0" step="1000" value="0" data-aff-deposit />
					</label>

					<label class="pdp-afford__field">
						<span class="pdp-afford__field-label"><?php esc_html_e( 'Term (months)', 'digicars' ); ?></span>
						<select data-aff-term>
							<?php
							foreach ( array( 12, 24, 36, 48, 60, 72 ) as $term_option ) :
								$is_default = ( 72 === $term_option ) ? ' selected' : '';
								?>
								<option value="<?php echo esc_attr( $term_option ); ?>"<?php echo $is_default; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static literal. ?>>
									<?php echo esc_html( $term_option ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</label>

					<label class="pdp-afford__field">
						<span class="pdp-afford__field-label"><?php esc_html_e( 'Balloon (%)', 'digicars' ); ?></span>
						<input type="number" inputmode="numeric" min="0" max="40" step="5" value="0" data-aff-balloon />
					</label>
				</form>

				<div class="pdp-afford__output">
					<span class="pdp-afford__output-label t-mono"><?php esc_html_e( 'Estimated monthly', 'digicars' ); ?></span>
					<span class="pdp-afford__output-value" data-aff-output>
						<?php echo $monthly > 0 ? 'R ' . esc_html( number_format( $monthly, 0, '.', ' ' ) ) : esc_html__( 'On request', 'digicars' ); ?>
					</span>
					<span class="pdp-afford__output-note muted"><?php echo esc_html( $disclosure ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<?php /* 5. Detail accordions ---------------------------------------------- */ ?>
	<section class="section section--tight section--flush-top">
		<div class="container container--narrow">
			<div class="accordion pdp-accordion">

				<details class="accordion__item" open>
					<summary><?php esc_html_e( 'Overview', 'digicars' ); ?></summary>
					<div class="accordion__panel prose">
						<?php if ( '' !== trim( $ai_summary ) ) : ?>
							<p class="pdp-overview__summary"><?php echo esc_html( $ai_summary ); ?></p>
						<?php endif; ?>
						<?php
						if ( function_exists( 'get_the_content' ) && function_exists( 'the_content' ) && '' !== trim( (string) get_the_content() ) ) {
							the_content();
						} elseif ( method_exists( $product, 'get_short_description' ) && '' !== trim( (string) $product->get_short_description() ) ) {
							$short = $product->get_short_description();
							echo wp_kses_post( function_exists( 'wpautop' ) ? wpautop( $short ) : $short );
						} elseif ( '' === trim( $ai_summary ) ) {
							echo '<p class="muted">' . esc_html__( 'A detailed walk-around is available on request from the Concierge.', 'digicars' ) . '</p>';
						}
						?>
					</div>
				</details>

				<?php if ( ! empty( $spec_rows ) ) : ?>
					<details class="accordion__item">
						<summary><?php esc_html_e( 'Full specs', 'digicars' ); ?></summary>
						<div class="accordion__panel">
							<table class="spec-table">
								<tbody>
									<?php foreach ( $spec_rows as $label => $value ) : ?>
										<tr>
											<th scope="row"><?php echo esc_html( $label ); ?></th>
											<td><?php echo esc_html( $value ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</details>
				<?php endif; ?>

				<?php if ( ! empty( $features ) ) : ?>
					<details class="accordion__item">
						<summary><?php esc_html_e( 'Features', 'digicars' ); ?></summary>
						<div class="accordion__panel">
							<ul class="pdp-features cluster">
								<?php foreach ( $features as $feature ) : ?>
									<li class="chip" aria-disabled="true"><?php echo esc_html( $feature ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</details>
				<?php endif; ?>

				<details class="accordion__item">
					<summary><?php esc_html_e( 'Finance &amp; disclosure', 'digicars' ); ?></summary>
					<div class="accordion__panel prose">
						<p><?php echo esc_html( $finance_note ); ?></p>
						<p>
							<a class="link-arrow" href="<?php echo esc_url( $finance_url ); ?>">
								<?php esc_html_e( 'Apply for finance', 'digicars' ); ?>
							</a>
						</p>
						<p class="muted"><?php echo esc_html( $disclosure ); ?></p>
					</div>
				</details>

			</div>
		</div>
	</section>

	<?php
	/* -----------------------------------------------------------------------
	 * 6. "Keep looking" — up to 4 related vehicles by body type, latest
	 * otherwise. Mirrors the catalogue fallback so the grid renders both in
	 * a real WC runtime and in the static preview harness.
	 * -------------------------------------------------------------------- */
	$related = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$body_slug = '' !== $body_type ? sanitize_title( $body_type ) : '';

		$related_args = array(
			'status'  => 'publish',
			'limit'   => 4,
			'exclude' => array( $vehicle_id ),
			'orderby' => 'date',
			'order'   => 'DESC',
		);
		if ( '' !== $body_slug ) {
			$related_args['category'] = array( $body_slug );
		}

		$related = wc_get_products( $related_args );

		// Fall back to latest vehicles when the body-type query is empty.
		if ( empty( $related ) ) {
			$related = wc_get_products(
				array(
					'status'  => 'publish',
					'limit'   => 4,
					'exclude' => array( $vehicle_id ),
					'orderby' => 'date',
					'order'   => 'DESC',
				)
			);
		}
	}

	// Defensively drop the current vehicle and cap at 4 (the harness stub
	// ignores `exclude`, so filter here too).
	if ( ! empty( $related ) ) {
		$related = array_filter(
			$related,
			static function ( $item ) use ( $vehicle_id ) {
				return $item instanceof WC_Product && $item->get_id() !== $vehicle_id;
			}
		);
		$related = array_slice( array_values( $related ), 0, 4 );
	}
	?>

	<?php if ( ! empty( $related ) ) : ?>
		<section class="section surface-soft">
			<div class="container">
				<div class="section-head">
					<div class="section-head__copy">
						<p class="eyebrow"><?php esc_html_e( 'Keep looking', 'digicars' ); ?></p>
						<h2 class="t-2"><?php esc_html_e( 'Similar vehicles', 'digicars' ); ?></h2>
					</div>
					<a class="link-arrow" href="<?php echo esc_url( home_url( '/shop' ) ); ?>">
						<?php esc_html_e( 'Browse all vehicles', 'digicars' ); ?>
					</a>
				</div>

				<ul class="grid grid--products products" style="--cols:4;">
					<?php
					global $post;
					$prev_product = $product;
					$prev_post    = isset( $post ) ? $post : null;
					$related_card = get_theme_file_path( 'woocommerce/content-product.php' );

					foreach ( $related as $related_item ) {
						$product = $related_item; // phpcs:ignore WordPress.WP.GlobalVariablesOverride

						// Prime the WP loop globals when available (real runtime).
						if ( function_exists( 'get_post' ) && function_exists( 'setup_postdata' ) ) {
							$related_post = get_post( $related_item->get_id() );
							if ( $related_post ) {
								$post = $related_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
								setup_postdata( $post );
							}
						}

						if ( function_exists( 'wc_get_template_part' ) ) {
							wc_get_template_part( 'content', 'product' );
						} elseif ( is_file( $related_card ) ) {
							require $related_card;
						}
					}

					$product = $prev_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
					$post    = $prev_post;    // phpcs:ignore WordPress.WP.GlobalVariablesOverride
					if ( function_exists( 'wp_reset_postdata' ) ) {
						wp_reset_postdata();
					}
					?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

</main>

<?php
get_footer( 'shop' );
