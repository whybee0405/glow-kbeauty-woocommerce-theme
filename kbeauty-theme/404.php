<?php
/**
 * 404 — a rescue page: search, the routine rail, customer favourites.
 *
 * @package Glow_KBeauty
 */

get_header();
?>

<main id="main">

	<div class="container">
		<header class="error-hero">
			<span class="mono-code">404</span>
			<h1 class="t-hero"><?php esc_html_e( 'This page evaporated like a bad toner.', 'glow-kbeauty' ); ?></h1>
			<p class="lead"><?php esc_html_e( 'The link is broken or the page has moved. The search below indexes ingredients too, so “snail mucin” works as well as a product name.', 'glow-kbeauty' ); ?></p>

			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form-row error-search">
				<label class="screen-reader-text" for="glow-404-search"><?php esc_html_e( 'Search products', 'glow-kbeauty' ); ?></label>
				<input type="search" id="glow-404-search" name="s" placeholder="<?php esc_attr_e( 'Search products or ingredients', 'glow-kbeauty' ); ?>" />
				<input type="hidden" name="post_type" value="product" />
				<button class="btn btn-solid" type="submit"><?php esc_html_e( 'Search', 'glow-kbeauty' ); ?></button>
			</form>
		</header>
	</div>

	<?php glow_routine_rail( true ); ?>

	<?php if ( glow_wc_active() ) : ?>
		<?php
		$glow_favourites = wc_get_products(
			array(
				'status'   => 'publish',
				'featured' => true,
				'limit'    => 4,
			)
		);

		if ( count( $glow_favourites ) < 4 ) {
			$glow_favourites = wc_get_products(
				array(
					'status' => 'publish',
					'limit'  => 4,
				)
			);
		}
		?>
		<?php if ( $glow_favourites ) : ?>
			<section class="section">
				<div class="container">
					<div class="section-head">
						<div>
							<p class="eyebrow"><?php esc_html_e( 'While you\'re here', 'glow-kbeauty' ); ?></p>
							<h2 class="t-1"><?php esc_html_e( 'Customer favourites', 'glow-kbeauty' ); ?></h2>
						</div>
					</div>
					<ul class="products grid-4">
						<?php
						foreach ( $glow_favourites as $glow_fav ) {
							$post_object = get_post( $glow_fav->get_id() );
							setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
							wc_get_template_part( 'content', 'product' );
						}
						wp_reset_postdata();
						?>
					</ul>
				</div>
			</section>
		<?php endif; ?>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
