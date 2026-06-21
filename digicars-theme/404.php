<?php
/**
 * 404 — rescue page. Search, the Concierge, body-type browse and a few cars.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

get_header();

$digicars_shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );
?>

<main id="main" class="site-main">
	<section class="section">
		<div class="container stack">
			<p class="eyebrow"><?php esc_html_e( 'Error 404', 'digicars' ); ?></p>
			<h1 class="t-hero"><?php esc_html_e( 'This page took a wrong turn.', 'digicars' ); ?></h1>
			<p class="t-lead muted"><?php esc_html_e( 'The page you wanted has moved or never existed. Let’s get you back to the cars — search, ask the Concierge, or browse by body type.', 'digicars' ); ?></p>

			<div class="stack-sm">
				<?php get_search_form(); ?>
			</div>

			<div class="concierge" data-concierge-inline>
				<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'The Concierge', 'digicars' ); ?></p>
				<p class="concierge__input-label"><?php esc_html_e( 'Describe how you drive and we’ll shortlist vehicles that fit.', 'digicars' ); ?></p>
				<noscript>
					<a class="btn btn--outline" href="<?php echo esc_url( $digicars_shop ); ?>"><?php esc_html_e( 'Browse all stock', 'digicars' ); ?></a>
				</noscript>
			</div>
		</div>
	</section>

	<section class="section section--tight surface-soft">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Browse by body type', 'digicars' ); ?></p>
			<ul class="bodytype-grid grid" style="--cols:5;">
				<?php
				if ( function_exists( 'digicars_body_types' ) ) :
					foreach ( digicars_body_types() as $digicars_slug => $digicars_body ) :
						$digicars_url = add_query_arg( 'body', $digicars_slug, $digicars_shop );
						?>
						<li class="bodytype-grid__item">
							<a class="bodytype-tile" href="<?php echo esc_url( $digicars_url ); ?>">
								<img class="bodytype-tile__icon" src="<?php echo esc_url( $digicars_body['icon'] ); ?>" alt="" aria-hidden="true" width="48" height="32" loading="lazy">
								<span class="bodytype-tile__label"><?php echo esc_html( $digicars_body['label'] ); ?></span>
							</a>
						</li>
						<?php
					endforeach;
				endif;
				?>
			</ul>
		</div>
	</section>

	<?php
	if ( function_exists( 'wc_get_products' ) ) :
		$digicars_cars = wc_get_products(
			array(
				'status' => 'publish',
				'limit'  => 4,
			)
		);
		if ( ! empty( $digicars_cars ) ) :
			?>
			<section class="section">
				<div class="container">
					<p class="eyebrow"><?php esc_html_e( 'Popular right now', 'digicars' ); ?></p>
					<ul class="grid grid--products products">
						<?php
						$digicars_card = get_theme_file_path( 'woocommerce/content-product.php' );
						foreach ( $digicars_cars as $digicars_loop_product ) :
							$GLOBALS['product'] = $digicars_loop_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
							$product            = $digicars_loop_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
							if ( is_file( $digicars_card ) ) {
								require $digicars_card;
							}
						endforeach;
						?>
					</ul>
				</div>
			</section>
			<?php
		endif;
	endif;
	?>
</main>

<?php
get_footer();
