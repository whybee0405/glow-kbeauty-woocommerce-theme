<?php
/**
 * Shop, category, skin-type and skin-concern archives.
 *
 * The journey: land → orient (breadcrumb, page hero, routine rail)
 * → narrow (sidebar filters) → decide (product grid).
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

get_header();

$glow_term  = is_tax() ? get_queried_object() : null;
$glow_title = $glow_term && isset( $glow_term->name ) ? $glow_term->name : __( 'All products', 'glow-kbeauty' );
$glow_intro = '';

if ( is_search() ) {
	/* translators: %s: search query. */
	$glow_title = sprintf( __( 'Results for “%s”', 'glow-kbeauty' ), get_search_query() );
	$glow_intro = __( 'We search product names, descriptions and key actives.', 'glow-kbeauty' );
} elseif ( $glow_term && ! empty( $glow_term->description ) ) {
	$glow_intro = $glow_term->description;
} elseif ( ! is_tax() ) {
	$glow_intro = __( 'Everything we stock, in routine order. Filter by step, skin type or concern — or search by ingredient if you already know what you\'re after.', 'glow-kbeauty' );
}

$glow_total = (int) $GLOBALS['wp_query']->found_posts;
?>

<main id="main">

	<div class="container">
		<header class="page-hero shop-hero">
			<?php
			if ( function_exists( 'woocommerce_breadcrumb' ) ) {
				woocommerce_breadcrumb();
			}
			?>
			<h1 class="t-1"><?php echo esc_html( $glow_title ); ?></h1>
			<?php if ( $glow_intro ) : ?>
				<p class="lead"><?php echo esc_html( $glow_intro ); ?></p>
			<?php endif; ?>
		</header>
	</div>

	<?php glow_routine_rail( true ); ?>

	<div class="container shop-layout">

		<aside class="shop-filters" data-filter-drawer aria-label="<?php esc_attr_e( 'Product filters', 'glow-kbeauty' ); ?>">
			<div class="filters-head">
				<h2 class="mono"><?php esc_html_e( 'Refine', 'glow-kbeauty' ); ?></h2>
				<button class="filters-close" type="button" aria-label="<?php esc_attr_e( 'Close filters', 'glow-kbeauty' ); ?>" data-filters-close>
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
				</button>
			</div>

			<div class="filter-group">
				<h3 class="filter-label mono"><?php esc_html_e( 'By step', 'glow-kbeauty' ); ?></h3>
				<ul class="filter-links">
					<?php foreach ( glow_routine_steps() as $glow_step ) : ?>
						<li>
							<a class="<?php echo ( $glow_term && $glow_term->slug === $glow_step['slug'] ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( glow_step_url( $glow_step['slug'] ) ); ?>">
								<span class="mono"><?php echo esc_html( $glow_step['no'] ); ?></span> <?php echo esc_html( $glow_step['name'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
					<?php
					// Non-routine categories (sheet masks, lips) appended after the steps.
					$glow_routine_slugs = wp_list_pluck( glow_routine_steps(), 'slug' );
					$glow_extra_cats    = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
						)
					);
					if ( ! is_wp_error( $glow_extra_cats ) ) :
						foreach ( $glow_extra_cats as $glow_cat ) :
							if ( in_array( $glow_cat->slug, $glow_routine_slugs, true ) || 'uncategorized' === $glow_cat->slug ) {
								continue;
							}
							$glow_cat_link = get_term_link( $glow_cat );
							if ( is_wp_error( $glow_cat_link ) ) {
								continue;
							}
							?>
							<li>
								<a class="<?php echo ( $glow_term && $glow_term->slug === $glow_cat->slug ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( $glow_cat_link ); ?>">
									<span class="mono">+</span> <?php echo esc_html( $glow_cat->name ); ?>
								</a>
							</li>
							<?php
						endforeach;
					endif;
					?>
				</ul>
			</div>

			<?php
			$glow_filter_taxes = array(
				'skin_type'    => __( 'Skin type', 'glow-kbeauty' ),
				'skin_concern' => __( 'Concern', 'glow-kbeauty' ),
			);

			foreach ( $glow_filter_taxes as $glow_tax => $glow_tax_label ) :
				$glow_tax_terms = get_terms(
					array(
						'taxonomy'   => $glow_tax,
						'hide_empty' => true,
					)
				);
				if ( is_wp_error( $glow_tax_terms ) || empty( $glow_tax_terms ) ) {
					continue;
				}
				?>
				<div class="filter-group">
					<h3 class="filter-label mono"><?php echo esc_html( $glow_tax_label ); ?></h3>
					<ul class="filter-links">
						<?php foreach ( $glow_tax_terms as $glow_tax_term ) : ?>
							<?php
							$glow_tax_link = get_term_link( $glow_tax_term );
							if ( is_wp_error( $glow_tax_link ) ) {
								continue;
							}
							?>
							<li>
								<a class="<?php echo ( $glow_term && $glow_term->term_id === $glow_tax_term->term_id ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( $glow_tax_link ); ?>">
									<?php echo esc_html( $glow_tax_term->name ); ?>
									<span class="mono count"><?php echo esc_html( $glow_tax_term->count ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>

			<?php if ( is_shop() || is_product_taxonomy() ) : ?>
				<div class="filter-group">
					<h3 class="filter-label mono"><?php esc_html_e( 'Price', 'glow-kbeauty' ); ?></h3>
					<?php the_widget( 'WC_Widget_Price_Filter' ); ?>
				</div>
			<?php endif; ?>
		</aside>

		<div class="filter-scrim" data-filter-scrim hidden></div>

		<div class="shop-main">
			<div class="shop-toolbar">
				<button class="btn btn-outline filter-toggle" type="button" data-filter-toggle aria-expanded="false">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
					<?php esc_html_e( 'Filter', 'glow-kbeauty' ); ?>
				</button>

				<p class="result-count mono">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: number of products. */
							_n( '%s product', '%s products', $glow_total, 'glow-kbeauty' ),
							number_format_i18n( $glow_total )
						)
					);
					?>
				</p>

				<?php
				if ( function_exists( 'woocommerce_catalog_ordering' ) && have_posts() ) {
					woocommerce_catalog_ordering();
				}
				?>
			</div>

			<?php if ( have_posts() ) : ?>

				<ul class="products shop-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
					?>
				</ul>

				<nav class="shop-pagination" aria-label="<?php esc_attr_e( 'Products pagination', 'glow-kbeauty' ); ?>">
					<?php
					the_posts_pagination(
						array(
							'mid_size'  => 1,
							'prev_text' => __( '← Previous', 'glow-kbeauty' ),
							'next_text' => __( 'Next →', 'glow-kbeauty' ),
						)
					);
					?>
				</nav>

			<?php else : ?>

				<div class="shop-empty">
					<h2 class="t-2"><?php esc_html_e( 'Nothing matches that combination — yet.', 'glow-kbeauty' ); ?></h2>
					<p><?php esc_html_e( 'Try widening the filters, or search by ingredient — “snail mucin”, “niacinamide” and “centella” all work.', 'glow-kbeauty' ); ?></p>
					<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form-row">
						<label class="screen-reader-text" for="glow-empty-search"><?php esc_html_e( 'Search products', 'glow-kbeauty' ); ?></label>
						<input type="search" id="glow-empty-search" name="s" placeholder="<?php esc_attr_e( 'Search by ingredient', 'glow-kbeauty' ); ?>" />
						<input type="hidden" name="post_type" value="product" />
						<button class="btn btn-solid" type="submit"><?php esc_html_e( 'Search', 'glow-kbeauty' ); ?></button>
					</form>
				</div>

			<?php endif; ?>
		</div>

	</div>

</main>

<?php get_footer(); ?>
