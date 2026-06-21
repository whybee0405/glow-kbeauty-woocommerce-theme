<?php
/**
 * The faceted vehicle catalogue — WooCommerce `archive-product.php`.
 *
 * This is the shop / product-category / vehicle-taxonomy archive. It renders a
 * page hero, a Concierge mount, a desktop filter sidebar + a mirrored mobile
 * filter drawer (both driven by ONE reusable partial), a results toolbar and
 * the vehicle grid (or a friendly empty state).
 *
 * Filtering itself is applied server-side in functions.php via a single
 * `pre_get_posts` handler — this template only renders the GET form whose
 * fields that handler reads, and reflects the active values back to the user.
 *
 * Every WooCommerce template function is called defensively so the static
 * preview harness (which stubs only part of WC) still renders cleanly.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/*
 * Minimal fallbacks for WordPress form helpers, only defined when absent.
 * In a real WordPress runtime these already exist and these blocks are skipped;
 * they exist so the static preview harness (which stubs only part of WP) can
 * render this template without fatals.
 */
if ( ! function_exists( 'selected' ) ) {
	/**
	 * Echo (or return) selected="selected" when two values match.
	 *
	 * @param mixed $a       Current value.
	 * @param mixed $b       Value to compare against.
	 * @param bool  $display Whether to echo.
	 * @return string
	 */
	function selected( $a, $b = true, $display = true ) {
		$out = ( (string) $a === (string) $b ) ? ' selected="selected"' : '';
		if ( $display ) {
			echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		return $out;
	}
}
if ( ! function_exists( 'checked' ) ) {
	/**
	 * Echo (or return) checked="checked" when two values match.
	 *
	 * @param mixed $a       Current value.
	 * @param mixed $b       Value to compare against.
	 * @param bool  $display Whether to echo.
	 * @return string
	 */
	function checked( $a, $b = true, $display = true ) {
		$out = ( (string) $a === (string) $b ) ? ' checked="checked"' : '';
		if ( $display ) {
			echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		return $out;
	}
}
if ( ! function_exists( 'sanitize_html_class' ) ) {
	/**
	 * Reduce a string to a safe HTML class fragment.
	 *
	 * @param string $class Raw class string.
	 * @return string
	 */
	function sanitize_html_class( $class ) {
		return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $class );
	}
}

if ( ! function_exists( 'digicars_filter_val' ) ) {
	/**
	 * Read and sanitize a single GET filter value for safe form re-population.
	 *
	 * @param string $key     Query var name.
	 * @param string $default Fallback when absent.
	 * @return string Sanitized value.
	 */
	function digicars_filter_val( string $key, string $default = '' ): string {
		if ( ! isset( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $default;
		}
		return sanitize_text_field( wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}

if ( ! function_exists( 'digicars_shop_base_url' ) ) {
	/**
	 * The bare shop URL — the filter form's GET target and the reset link.
	 *
	 * @return string
	 */
	function digicars_shop_base_url(): string {
		$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
		if ( ! $url ) {
			$url = home_url( '/shop' );
		}
		return $url;
	}
}

if ( ! function_exists( 'digicars_render_filters' ) ) {
	/**
	 * Render the catalogue filter form.
	 *
	 * A single source of truth reused verbatim in the desktop sidebar and the
	 * mobile drawer. It is a GET <form> targeting the shop URL; every field's
	 * value is repopulated from $_GET (escaped) so the form reflects the active
	 * query. The matching server-side reader lives in functions.php.
	 *
	 * @param string $instance Either 'sidebar' or 'drawer' — used only to keep
	 *                         element ids unique between the two copies.
	 * @return void
	 */
	function digicars_render_filters( string $instance = 'sidebar' ): void {
		$base       = digicars_shop_base_url();
		$prefix     = 'f-' . sanitize_html_class( $instance ) . '-';
		$makes      = function_exists( 'digicars_makes' ) ? digicars_makes() : array();
		$body_types = function_exists( 'digicars_body_types' ) ? digicars_body_types() : array();

		// Active values.
		$condition = digicars_filter_val( 'condition' );
		$make      = digicars_filter_val( 'make' );
		$body      = digicars_filter_val( 'body' );
		$search_by = digicars_filter_val( 'search_by', 'price' );
		$search_by = ( 'monthly' === $search_by ) ? 'monthly' : 'price';
		$trans     = digicars_filter_val( 'transmission' );
		$fuel      = digicars_filter_val( 'fuel' );
		$province  = digicars_filter_val( 'province' );
		$dealer    = digicars_filter_val( 'dealer' );

		// Condition tabs.
		$conditions = array(
			''     => __( 'All', 'digicars' ),
			'new'  => __( 'New', 'digicars' ),
			'demo' => __( 'Demo', 'digicars' ),
			'used' => __( 'Used', 'digicars' ),
		);

		$transmissions = array(
			''          => __( 'Any', 'digicars' ),
			'manual'    => __( 'Manual', 'digicars' ),
			'automatic' => __( 'Automatic', 'digicars' ),
		);

		$fuels = array(
			''         => __( 'Any', 'digicars' ),
			'petrol'   => __( 'Petrol', 'digicars' ),
			'diesel'   => __( 'Diesel', 'digicars' ),
			'electric' => __( 'Electric', 'digicars' ),
			'hybrid'   => __( 'Hybrid', 'digicars' ),
		);

		// SA provinces.
		$provinces = array(
			''              => __( 'Any province', 'digicars' ),
			'gauteng'       => __( 'Gauteng', 'digicars' ),
			'western-cape'  => __( 'Western Cape', 'digicars' ),
			'kwazulu-natal' => __( 'KwaZulu-Natal', 'digicars' ),
			'eastern-cape'  => __( 'Eastern Cape', 'digicars' ),
			'free-state'    => __( 'Free State', 'digicars' ),
			'mpumalanga'    => __( 'Mpumalanga', 'digicars' ),
			'limpopo'       => __( 'Limpopo', 'digicars' ),
			'north-west'    => __( 'North West', 'digicars' ),
			'northern-cape' => __( 'Northern Cape', 'digicars' ),
		);
		?>
		<form class="filters__form" method="get" action="<?php echo esc_url( $base ); ?>" role="search" aria-label="<?php esc_attr_e( 'Filter vehicles', 'digicars' ); ?>">

			<div class="filter-group">
				<p class="filter-group__title"><?php esc_html_e( 'Condition', 'digicars' ); ?></p>
				<div class="filter-group__options cluster">
					<?php
					foreach ( $conditions as $value => $label ) :
						$is_active = ( $condition === $value );
						$args      = array();
						if ( '' !== $value ) {
							$args['condition'] = $value;
						}
						$url = $args ? add_query_arg( $args, $base ) : $base;
						?>
						<a
							class="chip<?php echo $is_active ? ' is-selected' : ''; ?>"
							href="<?php echo esc_url( $url ); ?>"
							aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
						><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</div>
				<?php // Carry the chosen condition through on submit of the full form. ?>
				<input type="hidden" name="condition" value="<?php echo esc_attr( $condition ); ?>">
			</div>

			<div class="filter-group">
				<p class="filter-group__title"><?php echo esc_html__( 'Make & model', 'digicars' ); ?></p>
				<div class="filter-group__options">
					<div class="field">
						<label class="label" for="<?php echo esc_attr( $prefix . 'make' ); ?>"><?php esc_html_e( 'Make', 'digicars' ); ?></label>
						<select class="select" id="<?php echo esc_attr( $prefix . 'make' ); ?>" name="make">
							<option value=""><?php esc_html_e( 'Any make', 'digicars' ); ?></option>
							<?php foreach ( $makes as $slug => $label ) : ?>
								<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $make, $slug ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="field">
						<label class="label" for="<?php echo esc_attr( $prefix . 'model' ); ?>"><?php esc_html_e( 'Model', 'digicars' ); ?></label>
						<input class="input" type="text" id="<?php echo esc_attr( $prefix . 'model' ); ?>" name="model" value="<?php echo esc_attr( digicars_filter_val( 'model' ) ); ?>" placeholder="<?php esc_attr_e( 'e.g. Tiggo, Ranger', 'digicars' ); ?>">
					</div>
				</div>
			</div>

			<div class="filter-group">
				<p class="filter-group__title"><?php esc_html_e( 'Body type', 'digicars' ); ?></p>
				<div class="field">
					<label class="label screen-reader-text" for="<?php echo esc_attr( $prefix . 'body' ); ?>"><?php esc_html_e( 'Body type', 'digicars' ); ?></label>
					<select class="select" id="<?php echo esc_attr( $prefix . 'body' ); ?>" name="body">
						<option value=""><?php esc_html_e( 'Any body', 'digicars' ); ?></option>
						<?php foreach ( $body_types as $slug => $info ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $body, $slug ); ?>><?php echo esc_html( $info['label'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="filter-group" data-search-by="<?php echo esc_attr( $search_by ); ?>">
				<p class="filter-group__title"><?php esc_html_e( 'Search by', 'digicars' ); ?></p>
				<div class="filter-group__options">
					<div class="cluster">
						<label class="check-row">
							<input type="radio" name="search_by" value="price" <?php checked( 'price', $search_by ); ?>>
							<span><?php esc_html_e( 'Price', 'digicars' ); ?></span>
						</label>
						<label class="check-row">
							<input type="radio" name="search_by" value="monthly" <?php checked( 'monthly', $search_by ); ?>>
							<span><?php esc_html_e( 'Monthly', 'digicars' ); ?></span>
						</label>
					</div>

					<?php // Cash-price range (ZAR). ?>
					<div class="filter-range" data-when="price"<?php echo ( 'monthly' === $search_by ) ? ' hidden' : ''; ?>>
						<div class="cluster cluster--between">
							<div class="field">
								<label class="label" for="<?php echo esc_attr( $prefix . 'price-min' ); ?>"><?php esc_html_e( 'Min (R)', 'digicars' ); ?></label>
								<input class="input" type="number" inputmode="numeric" min="0" step="1000" id="<?php echo esc_attr( $prefix . 'price-min' ); ?>" name="price_min" value="<?php echo esc_attr( digicars_filter_val( 'price_min' ) ); ?>" placeholder="0">
							</div>
							<div class="field">
								<label class="label" for="<?php echo esc_attr( $prefix . 'price-max' ); ?>"><?php esc_html_e( 'Max (R)', 'digicars' ); ?></label>
								<input class="input" type="number" inputmode="numeric" min="0" step="1000" id="<?php echo esc_attr( $prefix . 'price-max' ); ?>" name="price_max" value="<?php echo esc_attr( digicars_filter_val( 'price_max' ) ); ?>" placeholder="<?php esc_attr_e( 'No max', 'digicars' ); ?>">
							</div>
						</div>
						<p class="field-hint"><?php esc_html_e( 'Cash price, in South African Rand (ZAR).', 'digicars' ); ?></p>
					</div>

					<?php // Monthly instalment range (ZAR). ?>
					<div class="filter-range" data-when="monthly"<?php echo ( 'monthly' !== $search_by ) ? ' hidden' : ''; ?>>
						<div class="cluster cluster--between">
							<div class="field">
								<label class="label" for="<?php echo esc_attr( $prefix . 'pm-min' ); ?>"><?php esc_html_e( 'Min p/m (R)', 'digicars' ); ?></label>
								<input class="input" type="number" inputmode="numeric" min="0" step="100" id="<?php echo esc_attr( $prefix . 'pm-min' ); ?>" name="pm_min" value="<?php echo esc_attr( digicars_filter_val( 'pm_min' ) ); ?>" placeholder="0">
							</div>
							<div class="field">
								<label class="label" for="<?php echo esc_attr( $prefix . 'pm-max' ); ?>"><?php esc_html_e( 'Max p/m (R)', 'digicars' ); ?></label>
								<input class="input" type="number" inputmode="numeric" min="0" step="100" id="<?php echo esc_attr( $prefix . 'pm-max' ); ?>" name="pm_max" value="<?php echo esc_attr( digicars_filter_val( 'pm_max' ) ); ?>" placeholder="<?php esc_attr_e( 'No max', 'digicars' ); ?>">
							</div>
						</div>
						<p class="field-hint"><?php esc_html_e( 'Estimated instalment, in Rand per month (ZAR).', 'digicars' ); ?></p>
					</div>
				</div>
			</div>

			<div class="filter-group">
				<p class="filter-group__title"><?php esc_html_e( 'Year', 'digicars' ); ?></p>
				<div class="cluster cluster--between">
					<div class="field">
						<label class="label" for="<?php echo esc_attr( $prefix . 'year-min' ); ?>"><?php esc_html_e( 'From', 'digicars' ); ?></label>
						<input class="input" type="number" inputmode="numeric" min="1950" max="2100" id="<?php echo esc_attr( $prefix . 'year-min' ); ?>" name="year_min" value="<?php echo esc_attr( digicars_filter_val( 'year_min' ) ); ?>" placeholder="<?php esc_attr_e( 'Any', 'digicars' ); ?>">
					</div>
					<div class="field">
						<label class="label" for="<?php echo esc_attr( $prefix . 'year-max' ); ?>"><?php esc_html_e( 'To', 'digicars' ); ?></label>
						<input class="input" type="number" inputmode="numeric" min="1950" max="2100" id="<?php echo esc_attr( $prefix . 'year-max' ); ?>" name="year_max" value="<?php echo esc_attr( digicars_filter_val( 'year_max' ) ); ?>" placeholder="<?php esc_attr_e( 'Any', 'digicars' ); ?>">
					</div>
				</div>
			</div>

			<div class="filter-group">
				<p class="filter-group__title"><?php esc_html_e( 'Mileage', 'digicars' ); ?></p>
				<div class="field">
					<label class="label" for="<?php echo esc_attr( $prefix . 'km-max' ); ?>"><?php esc_html_e( 'Max km', 'digicars' ); ?></label>
					<input class="input" type="number" inputmode="numeric" min="0" step="1000" id="<?php echo esc_attr( $prefix . 'km-max' ); ?>" name="km_max" value="<?php echo esc_attr( digicars_filter_val( 'km_max' ) ); ?>" placeholder="<?php esc_attr_e( 'No limit', 'digicars' ); ?>">
				</div>
			</div>

			<div class="filter-group">
				<p class="filter-group__title"><?php esc_html_e( 'Drivetrain', 'digicars' ); ?></p>
				<div class="filter-group__options">
					<div class="field">
						<label class="label" for="<?php echo esc_attr( $prefix . 'transmission' ); ?>"><?php esc_html_e( 'Transmission', 'digicars' ); ?></label>
						<select class="select" id="<?php echo esc_attr( $prefix . 'transmission' ); ?>" name="transmission">
							<?php foreach ( $transmissions as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $trans, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="field">
						<label class="label" for="<?php echo esc_attr( $prefix . 'fuel' ); ?>"><?php esc_html_e( 'Fuel', 'digicars' ); ?></label>
						<select class="select" id="<?php echo esc_attr( $prefix . 'fuel' ); ?>" name="fuel">
							<?php foreach ( $fuels as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $fuel, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>

			<div class="filter-group">
				<p class="filter-group__title"><?php esc_html_e( 'Location', 'digicars' ); ?></p>
				<div class="filter-group__options">
					<div class="field">
						<label class="label" for="<?php echo esc_attr( $prefix . 'province' ); ?>"><?php esc_html_e( 'Province', 'digicars' ); ?></label>
						<select class="select" id="<?php echo esc_attr( $prefix . 'province' ); ?>" name="province">
							<?php foreach ( $provinces as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $province, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="field">
						<label class="label" for="<?php echo esc_attr( $prefix . 'dealer' ); ?>"><?php esc_html_e( 'Dealer', 'digicars' ); ?></label>
						<select class="select" id="<?php echo esc_attr( $prefix . 'dealer' ); ?>" name="dealer">
							<option value=""><?php esc_html_e( 'Any dealer', 'digicars' ); ?></option>
							<?php
							$dealer_terms = function_exists( 'get_terms' ) ? get_terms(
								array(
									'taxonomy'   => 'vehicle_dealer',
									'hide_empty' => false,
								)
							) : array();
							if ( is_array( $dealer_terms ) ) :
								foreach ( $dealer_terms as $term ) :
									if ( ! is_object( $term ) || empty( $term->slug ) ) {
										continue;
									}
									?>
									<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $dealer, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
									<?php
								endforeach;
							endif;
							?>
						</select>
					</div>
				</div>
			</div>

			<?php
			// Preserve the active sort order across filter submits.
			$orderby = digicars_filter_val( 'orderby' );
			if ( '' !== $orderby ) :
				?>
				<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>">
			<?php endif; ?>

			<div class="filter-group filters__actions">
				<button type="submit" class="btn btn--signal btn--block"><?php esc_html_e( 'Show results', 'digicars' ); ?></button>
				<a class="btn btn--outline btn--block" href="<?php echo esc_url( $base ); ?>"><?php esc_html_e( 'Reset filters', 'digicars' ); ?></a>
			</div>
		</form>
		<?php
	}
}

if ( function_exists( 'get_header' ) ) {
	get_header( 'shop' );
}

global $wp_query;
$digicars_found = ( isset( $wp_query ) && isset( $wp_query->found_posts ) ) ? (int) $wp_query->found_posts : 0;
$digicars_base  = digicars_shop_base_url();

// Hero title: taxonomy term name, else always "Cars in stock" (ignores WP shop page slug).
$digicars_term = function_exists( 'get_queried_object' ) ? get_queried_object() : null;
if ( $digicars_term instanceof WP_Term && ! empty( $digicars_term->name ) ) {
	$digicars_title = $digicars_term->name;
} else {
	$digicars_title = __( 'Cars in stock', 'digicars' );
}
?>

<div class="catalogue">
<div class="catalogue__intro">
	<div class="container">

		<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
			<?php woocommerce_breadcrumb(); ?>
		<?php endif; ?>

		<header class="catalogue__hero">
			<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Cars in stock', 'digicars' ); ?></p>
			<h1 class="t-1"><?php echo esc_html( $digicars_title ); ?></h1>
			<p class="lead muted">
				<?php esc_html_e( 'Browse verified new, demo and used vehicles from dealers across South Africa. Filter by budget or monthly instalment, then enquire or pre-qualify for finance online.', 'digicars' ); ?>
			</p>
		</header>

		<?php echo do_shortcode( '[helix_search]' ); ?>

	</div><!-- /.container -->
</div><!-- /.catalogue__intro -->

<div class="catalogue__body">
	<div class="container">

		<div class="catalogue__layout">

			<aside class="filters filters--sidebar" aria-label="<?php esc_attr_e( 'Vehicle filters', 'digicars' ); ?>">
				<?php digicars_render_filters( 'sidebar' ); ?>
			</aside>

			<div class="catalogue__results">

				<div class="filters__bar">
					<p class="result-count">
						<?php
						/* translators: %s: number of matching vehicles. */
						printf( esc_html__( '%s results', 'digicars' ), esc_html( number_format_i18n( $digicars_found ) ) );
						?>
					</p>

					<div class="filters__bar-controls cluster">
						<?php
						if ( function_exists( 'woocommerce_catalog_ordering' ) ) {
							woocommerce_catalog_ordering();
						}
						?>
						<button type="button" class="btn btn--outline btn--sm filter-toggle" data-filter-open aria-controls="filter-drawer" aria-expanded="false">
							<?php esc_html_e( 'Filters', 'digicars' ); ?>
						</button>
					</div>
				</div>

				<?php
				/*
				 * Primary path: the WooCommerce main loop. We render our own
				 * `.grid .grid--products` wrapper (matching the CSS contract)
				 * rather than woocommerce_product_loop_start() so the grid
				 * classes are guaranteed.
				 */
				$digicars_has_loop = false;
				if ( function_exists( 'woocommerce_product_loop' ) ) {
					$digicars_has_loop = woocommerce_product_loop();
				} elseif ( function_exists( 'have_posts' ) ) {
					$digicars_has_loop = have_posts();
				}

				/*
				 * Fallback (no WP main loop available, e.g. the static preview
				 * harness): pull products directly so the grid still renders.
				 * Skipped entirely in a real WP/WC runtime.
				 */
				$digicars_fallback = array();
				if ( ! $digicars_has_loop && ! function_exists( 'have_posts' ) && function_exists( 'wc_get_products' ) ) {
					$digicars_fallback = wc_get_products( array( 'status' => 'publish' ) );
				}

				if ( $digicars_has_loop ) :
					?>
					<ul class="grid grid--products products">
						<?php
						while ( function_exists( 'have_posts' ) && have_posts() ) :
							the_post();
							if ( function_exists( 'wc_get_template_part' ) ) {
								wc_get_template_part( 'content', 'product' );
							}
						endwhile;
						?>
					</ul>
					<?php
					if ( function_exists( 'woocommerce_pagination' ) ) :
						woocommerce_pagination();
					endif;
				elseif ( ! empty( $digicars_fallback ) ) :
					?>
					<ul class="grid grid--products products">
						<?php
						$digicars_card = get_theme_file_path( 'woocommerce/content-product.php' );
						foreach ( $digicars_fallback as $digicars_item ) :
							$product = ( $digicars_item instanceof WC_Product ) ? $digicars_item : null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
							if ( $product && is_file( $digicars_card ) ) {
								require $digicars_card;
							}
						endforeach;
						?>
					</ul>
					<?php
				else :
					?>
					<div class="catalogue__empty">
						<p class="eyebrow"><?php esc_html_e( 'No matches', 'digicars' ); ?></p>
						<h2 class="t-2"><?php esc_html_e( 'No matches. Try widening your budget or ask the Concierge.', 'digicars' ); ?></h2>
						<p class="muted"><?php esc_html_e( 'Adjust your filters, or let the Concierge shortlist vehicles that fit how you drive.', 'digicars' ); ?></p>
						<div class="cluster">
							<a class="btn btn--outline" href="<?php echo esc_url( $digicars_base ); ?>"><?php esc_html_e( 'Reset filters', 'digicars' ); ?></a>
							<button type="button" class="btn btn--signal" data-concierge-open><?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?></button>
						</div>
					</div>
					<?php
				endif;
				?>

			</div><!-- /.catalogue__results -->
		</div><!-- /.catalogue__layout -->
	</div><!-- /.container -->
</div><!-- /.catalogue__body -->
</div><!-- /.catalogue -->

<?php // Mobile filter drawer — mirrors the sidebar form. ?>
<div class="filter-scrim" data-filter-close hidden></div>
<aside class="filter-drawer" id="filter-drawer" aria-label="<?php esc_attr_e( 'Vehicle filters', 'digicars' ); ?>" aria-hidden="true">
	<div class="filter-drawer__head">
		<p class="filter-group__title"><?php esc_html_e( 'Filter vehicles', 'digicars' ); ?></p>
		<button type="button" class="filter-drawer__close" data-filter-close aria-label="<?php esc_attr_e( 'Close filters', 'digicars' ); ?>">
			<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<line x1="18" y1="6" x2="6" y2="18"></line>
				<line x1="6" y1="6" x2="18" y2="18"></line>
			</svg>
		</button>
	</div>
	<div class="filters">
		<?php digicars_render_filters( 'drawer' ); ?>
	</div>
</aside>

<?php
if ( function_exists( 'get_footer' ) ) {
	get_footer( 'shop' );
}
