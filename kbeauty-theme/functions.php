<?php
/**
 * Glow K-Beauty — theme setup, the routine, taxonomies, WooCommerce
 * adjustments, AJAX and template helpers.
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

define( 'GLOW_VERSION', '2.0.0' );


/* --------------------------------------------------------------------------
 * Setup
 * ------------------------------------------------------------------------ */

function glow_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'glow-kbeauty' ),
			'footer'  => __( 'Footer menu', 'glow-kbeauty' ),
		)
	);

	add_image_size( 'glow-card', 600, 600, true );

	load_theme_textdomain( 'glow-kbeauty', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'glow_setup' );

function glow_wc_active() {
	return class_exists( 'WooCommerce' );
}

function glow_inline_logo() {
	?>
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 118 42" width="118" height="42" overflow="visible" aria-hidden="true" focusable="false">
		<path d="M 12 2 C 0 9 0 25 12 34 C 24 25 24 9 12 2 Z" fill="#6B8F5E"/>
		<text x="30" y="27" font-family="'Young Serif', Georgia, serif" font-size="22" fill="currentColor" letter-spacing="5">GLOW</text>
	</svg>
	<?php
}

add_action( 'wp_head', function () {
	if ( ! has_site_icon() ) {
		?>
		<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/images/favicon.svg' ); ?>" type="image/svg+xml"/>
		<?php
	}
} );

// Importmap must appear in <head> before any type="module" script.
// The addon modules (RoomEnvironment, OrbitControls) internally import from
// the bare specifier 'three' — without this map the browser can\'t resolve them.
add_action( 'wp_head', function () {
	if ( is_front_page() || is_singular( 'product' ) ) {
		echo '<script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js","three/addons/":"https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"}}</script>' . "\n";
	}
}, 1 );

/* --------------------------------------------------------------------------
 * Assets
 * ------------------------------------------------------------------------ */

// Disable WooCommerce\'s bundled CSS — the theme ships its own complete WC styles.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

function glow_module_script_type( $tag, $handle, $src ) {
	$module_handles = array( 'glow-hero-3d', 'glow-pdp-3d' );
	if ( in_array( $handle, $module_handles, true ) ) {
		return '<script type="module" src="' . esc_url( $src ) . '"></script>' . "\n";
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'glow_module_script_type', 10, 3 );

function glow_enqueue() {
	// Fonts + styles
	wp_enqueue_style(
		'glow-fonts',
		'https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:ital,wght@0,400..700;1,400..700&family=Spline+Sans+Mono:wght@400..600&family=Young+Serif&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'glow-style', get_stylesheet_uri(), array( 'glow-fonts' ), GLOW_VERSION );
	if ( glow_wc_active() ) {
		wp_enqueue_style( 'glow-woocommerce', get_template_directory_uri() . '/css/woocommerce.css', array( 'glow-style' ), GLOW_VERSION );
	}

	// GSAP + ScrollTrigger (CDN, footer, all pages)
	wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), null, true );
	wp_enqueue_script( 'gsap-st', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array( 'gsap' ), null, true );

	// Scroll animations — listed as dep of glow-main so it always runs first
	wp_enqueue_script( 'glow-scroll-anim', get_template_directory_uri() . '/js/scroll-animations.js', array( 'gsap', 'gsap-st' ), GLOW_VERSION, true );

	// Main script
	wp_enqueue_script( 'glow-main', get_template_directory_uri() . '/js/main.js', array( 'glow-scroll-anim' ), GLOW_VERSION, true );

	wp_localize_script(
		'glow-main',
		'glowData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'glow_nonce' ),
		)
	);

	// Cursor (all pages, desktop pointer:fine only)
	wp_enqueue_script( 'glow-cursor', get_template_directory_uri() . '/js/cursor.js', array(), GLOW_VERSION, true );

	// Homepage-only scripts
	if ( is_front_page() ) {
		wp_enqueue_script( 'glow-particles', get_template_directory_uri() . '/js/particles.js', array(), GLOW_VERSION, true );
	}

	// PDP-only scripts
	if ( is_singular( 'product' ) ) {
		wp_enqueue_script( 'glow-pdp-3d', get_template_directory_uri() . '/js/pdp-3d.js', array(), GLOW_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'glow_enqueue' );

/**
 * Flag JS availability before first paint so reveal styles only
 * apply when the observer will actually run.
 */
function glow_js_flag() {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'glow_js_flag', 0 );

function glow_preconnect_fonts( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'glow_preconnect_fonts', 10, 2 );

/* --------------------------------------------------------------------------
 * The routine — single source of truth
 * ------------------------------------------------------------------------ */

/**
 * The 7-step routine. Order is real information here: it is how
 * K-beauty products are applied, and how this store is navigated.
 *
 * @return array[] Each step: no, name, korean, slug.
 */
function glow_routine_steps() {
	return array(
		array( 'no' => '01', 'name' => __( 'Cleanse',    'glow-kbeauty' ), 'slug' => 'cleansers' ),
		array( 'no' => '02', 'name' => __( 'Exfoliate',  'glow-kbeauty' ), 'slug' => 'exfoliators' ),
		array( 'no' => '03', 'name' => __( 'Tone',       'glow-kbeauty' ), 'slug' => 'toners-essences' ),
		array( 'no' => '04', 'name' => __( 'Treat',      'glow-kbeauty' ), 'slug' => 'serums-ampoules' ),
		array( 'no' => '05', 'name' => __( 'Moisturise', 'glow-kbeauty' ), 'slug' => 'moisturisers' ),
		array( 'no' => '06', 'name' => __( 'Eye',        'glow-kbeauty' ), 'slug' => 'eye-care' ),
		array( 'no' => '07', 'name' => __( 'Protect',    'glow-kbeauty' ), 'slug' => 'sun-care' ),
	);
}

/**
 * Resolve a routine category slug to its archive URL, falling back to
 * the shop page filtered by category before terms exist.
 */
function glow_step_url( $slug ) {
	if ( glow_wc_active() ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				return $link;
			}
		}
		$shop = wc_get_page_permalink( 'shop' );
		if ( $shop ) {
			return add_query_arg( 'product_cat', $slug, $shop );
		}
	}
	return home_url( '/' );
}

/**
 * Resolve any taxonomy term slug to its archive URL, falling back to
 * the shop page (filtered where possible) before the term exists.
 */
function glow_tax_url( $slug, $taxonomy ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( $term && ! is_wp_error( $term ) ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	return glow_wc_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/**
 * Render the Routine Rail — the theme\'s signature wayfinding element.
 * Appears on the homepage (driving the hero stage), shop archives and
 * the 404 page.
 *
 * @param bool $linked Link each step to its product category archive.
 */
function glow_routine_rail( $linked = true ) {
	$steps   = glow_routine_steps();
	$current = '';

	if ( glow_wc_active() && is_tax( 'product_cat' ) ) {
		$term = get_queried_object();
		if ( $term && isset( $term->slug ) ) {
			$current = $term->slug;
		}
	}
	?>
	<nav class="routine-rail" id="routine" aria-label="<?php esc_attr_e( 'The 7-step routine', 'glow-kbeauty' ); ?>">
		<ol>
			<?php foreach ( $steps as $i => $step ) : ?>
				<li>
					<?php
					$classes = 'rail-step' . ( $current === $step['slug'] ? ' is-active' : '' );
					$inner   = sprintf(
						'<span class="rail-no">%1$s %2$s</span><span class="rail-name">%3$s</span>',
						esc_html__( 'STEP', 'glow-kbeauty' ),
						esc_html( $step['no'] ),
						esc_html( $step['name'] )
					);

					if ( $linked ) {
						printf(
							'<a class="%1$s" href="%2$s" data-step="%3$d">%4$s</a>',
							esc_attr( $classes ),
							esc_url( glow_step_url( $step['slug'] ) ),
							(int) ( $i + 1 ),
							$inner // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above.
						);
					} else {
						printf(
							'<span class="%1$s" data-step="%2$d">%3$s</span>',
							esc_attr( $classes ),
							(int) ( $i + 1 ),
							$inner // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above.
						);
					}
					?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/* --------------------------------------------------------------------------
 * Taxonomies: skin concern & skin type
 * ------------------------------------------------------------------------ */

function glow_register_taxonomies() {
	register_taxonomy(
		'skin_concern',
		'product',
		array(
			'labels'            => array(
				'name'          => __( 'Skin Concerns', 'glow-kbeauty' ),
				'singular_name' => __( 'Skin Concern', 'glow-kbeauty' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'concern' ),
		)
	);

	register_taxonomy(
		'skin_type',
		'product',
		array(
			'labels'            => array(
				'name'          => __( 'Skin Types', 'glow-kbeauty' ),
				'singular_name' => __( 'Skin Type', 'glow-kbeauty' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'skin-type' ),
		)
	);
}
add_action( 'init', 'glow_register_taxonomies' );

/**
 * Skin concern and skin type archives should behave like product
 * archives: product grid, 12 per page, newest first by default.
 */
function glow_tax_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_tax( 'skin_concern' ) || $query->is_tax( 'skin_type' ) ) {
		$query->set( 'post_type', 'product' );
		$query->set( 'posts_per_page', 12 );

		$orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		switch ( $orderby ) {
			case 'price':
				$query->set( 'meta_key', '_price' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'ASC' );
				break;
			case 'price-desc':
				$query->set( 'meta_key', '_price' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'DESC' );
				break;
			case 'popularity':
				$query->set( 'meta_key', 'total_sales' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'DESC' );
				break;
			case 'rating':
				$query->set( 'meta_key', '_wc_average_rating' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'DESC' );
				break;
		}
	}
}
add_action( 'pre_get_posts', 'glow_tax_archive_query' );

/**
 * Route skin concern / skin type archives through the product archive
 * template so filtering by either feels native to the shop.
 */
function glow_tax_archive_template( $template ) {
	if ( glow_wc_active() && ( is_tax( 'skin_concern' ) || is_tax( 'skin_type' ) ) ) {
		$product_archive = locate_template( 'archive-product.php' );
		if ( $product_archive ) {
			return $product_archive;
		}
	}
	return $template;
}
add_filter( 'template_include', 'glow_tax_archive_template' );

/* --------------------------------------------------------------------------
 * Product meta
 * ------------------------------------------------------------------------ */

/**
 * Product meta keys used by the theme and the bundled importer:
 * _product_brand, _skin_types, _key_ingredients, _product_routine_step,
 * _is_vegan, _is_cruelty_free.
 */
function glow_meta( $product_id, $key ) {
	return get_post_meta( $product_id, $key, true );
}

/**
 * Product search also matches key actives, so "snail mucin" or
 * "niacinamide" finds products even when the name doesn\'t say so.
 * The Ingredient Index, 404 and empty states all rely on this.
 */
function glow_product_search_includes_actives( $search, $query ) {
	global $wpdb;

	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() || 'product' !== $query->get( 'post_type' ) ) {
		return $search;
	}

	$term = trim( (string) $query->get( 's' ) );
	if ( '' === $term ) {
		return $search;
	}

	$like = '%' . $wpdb->esc_like( $term ) . '%';

	return $wpdb->prepare(
		" AND ( ({$wpdb->posts}.post_title LIKE %s)
			OR ({$wpdb->posts}.post_excerpt LIKE %s)
			OR ({$wpdb->posts}.post_content LIKE %s)
			OR EXISTS (
				SELECT 1 FROM {$wpdb->postmeta} glow_pm
				WHERE glow_pm.post_id = {$wpdb->posts}.ID
				AND glow_pm.meta_key = '_key_ingredients'
				AND glow_pm.meta_value LIKE %s
			) ) ",
		$like,
		$like,
		$like,
		$like
	);
}
add_filter( 'posts_search', 'glow_product_search_includes_actives', 10, 2 );

/* --------------------------------------------------------------------------
 * WooCommerce adjustments
 * ------------------------------------------------------------------------ */

function glow_wc_adjustments() {
	if ( ! glow_wc_active() ) {
		return;
	}

	// The theme provides its own page chrome.
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	add_filter( 'woocommerce_show_page_title', '__return_false' );
	add_filter( 'woocommerce_sale_flash', '__return_empty_string' );

	add_filter(
		'loop_shop_per_page',
		function () {
			return 12;
		},
		20
	);

	add_filter(
		'woocommerce_breadcrumb_defaults',
		function ( $defaults ) {
			$defaults['delimiter']   = ' <span aria-hidden="true">→</span> ';
			$defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'glow-kbeauty' ) . '">';
			return $defaults;
		}
	);
}
add_action( 'init', 'glow_wc_adjustments' );

/**
 * Live cart count in the header, kept fresh by WooCommerce fragments.
 */
function glow_cart_count_fragment( $fragments ) {
	$fragments['span.cart-count'] = '<span class="cart-count" data-cart-count>' . esc_html( WC()->cart->get_cart_contents_count() ) . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'glow_cart_count_fragment' );

/* --------------------------------------------------------------------------
 * AJAX: quick add, newsletter, contact
 * ------------------------------------------------------------------------ */

function glow_quick_add() {
	check_ajax_referer( 'glow_nonce', 'nonce' );

	if ( ! glow_wc_active() ) {
		wp_send_json_error( array( 'message' => __( 'The shop is taking a breather. Try again in a moment.', 'glow-kbeauty' ) ) );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		wp_send_json_error( array( 'message' => __( 'That one can\'t be added right now.', 'glow-kbeauty' ) ) );
	}

	$added = WC()->cart->add_to_cart( $product_id, 1 );

	if ( ! $added ) {
		wp_send_json_error( array( 'message' => __( 'Couldn\'t add that to your bag. Try the product page.', 'glow-kbeauty' ) ) );
	}

	wp_send_json_success(
		array(
			'count'   => WC()->cart->get_cart_contents_count(),
			'message' => sprintf(
				/* translators: %s: product name. */
				__( 'Added to bag — %s', 'glow-kbeauty' ),
				$product->get_name()
			),
		)
	);
}
add_action( 'wp_ajax_glow_quick_add', 'glow_quick_add' );
add_action( 'wp_ajax_nopriv_glow_quick_add', 'glow_quick_add' );

function glow_newsletter_signup() {
	check_ajax_referer( 'glow_nonce', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'That email doesn\'t look right — check it and try again.', 'glow-kbeauty' ) ) );
	}

	$subscribers = get_option( 'glow_newsletter_subscribers', array() );

	if ( ! in_array( $email, $subscribers, true ) ) {
		$subscribers[] = $email;
		update_option( 'glow_newsletter_subscribers', $subscribers, false );
	}

	wp_send_json_success( array( 'message' => __( 'You\'re on the list. One email a month, starting with the next one.', 'glow-kbeauty' ) ) );
}
add_action( 'wp_ajax_glow_newsletter', 'glow_newsletter_signup' );
add_action( 'wp_ajax_nopriv_glow_newsletter', 'glow_newsletter_signup' );

function glow_contact_submit() {
	check_ajax_referer( 'glow_nonce', 'nonce' );

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$topic   = isset( $_POST['topic'] ) ? sanitize_text_field( wp_unslash( $_POST['topic'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		wp_send_json_error( array( 'message' => __( 'We need your name, a working email and a message to reply to.', 'glow-kbeauty' ) ) );
	}

	$subject = sprintf(
		/* translators: 1: topic, 2: sender name. */
		__( '[Glow] %1$s — from %2$s', 'glow-kbeauty' ),
		$topic ? $topic : __( 'Website message', 'glow-kbeauty' ),
		$name
	);

	$body  = $message . "\n\n—\n" . $name . ' <' . $email . '>';
	$sent  = wp_mail( get_option( 'admin_email' ), $subject, $body, array( 'Reply-To: ' . $name . ' <' . $email . '>' ) );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'The message didn\'t send. Email us directly at the address above.', 'glow-kbeauty' ) ) );
	}

	wp_send_json_success( array( 'message' => __( 'Got it. A person — not an autoresponder — will reply within one working day.', 'glow-kbeauty' ) ) );
}
add_action( 'wp_ajax_glow_contact', 'glow_contact_submit' );
add_action( 'wp_ajax_nopriv_glow_contact', 'glow_contact_submit' );

/* --------------------------------------------------------------------------
 * Template helpers
 * ------------------------------------------------------------------------ */

/**
 * Badges, in priority order: Sale, Best seller, Vegan, New
 * (published within 30 days and not featured).
 *
 * @param WC_Product $product Product object.
 * @return array[] Each badge: label, class.
 */
function glow_product_badges( $product ) {
	$badges = array();

	if ( $product->is_on_sale() ) {
		$badges[] = array(
			'label' => __( 'Sale', 'glow-kbeauty' ),
			'class' => 'badge-sale',
		);
	}

	if ( $product->is_featured() ) {
		$badges[] = array(
			'label' => __( 'Best seller', 'glow-kbeauty' ),
			'class' => 'badge-best',
		);
	}

	if ( 'yes' === glow_meta( $product->get_id(), '_is_vegan' ) ) {
		$badges[] = array(
			'label' => __( 'Vegan', 'glow-kbeauty' ),
			'class' => 'badge-vegan',
		);
	}

	$created = $product->get_date_created();
	if ( ! $product->is_featured() && $created && ( time() - $created->getTimestamp() ) < 30 * DAY_IN_SECONDS ) {
		$badges[] = array(
			'label' => __( 'New', 'glow-kbeauty' ),
			'class' => 'badge-new',
		);
	}

	return $badges;
}

/**
 * Star rating: five-star track with a yuja fill clipped to the rating.
 *
 * @param float $rating Rating out of 5.
 */
function glow_stars( $rating ) {
	$rating  = max( 0, min( 5, (float) $rating ) );
	$percent = ( $rating / 5 ) * 100;
	printf(
		'<span class="stars" role="img" aria-label="%1$s"><span aria-hidden="true">★★★★★</span><span class="stars-fill" aria-hidden="true" style="width:%2$s%%">★★★★★</span></span>',
		esc_attr(
			sprintf(
				/* translators: %s: rating out of 5. */
				__( 'Rated %s out of 5', 'glow-kbeauty' ),
				number_format_i18n( $rating, 1 )
			)
		),
		esc_attr( round( $percent, 1 ) )
	);
}

/**
 * Product image with a graceful fallback chain: featured image →
 * bundled SVG referenced by `_glow_svg` meta → bundled default SVG.
 * Keeps the store looking finished before any media is uploaded.
 *
 * @param WC_Product $product Product object.
 * @param string     $size    Registered image size.
 */
function glow_product_image( $product, $size = 'glow-card' ) {
	if ( $product->get_image_id() ) {
		echo wp_get_attachment_image( $product->get_image_id(), $size, false, array( 'loading' => 'lazy' ) );
		return;
	}

	$svg  = glow_meta( $product->get_id(), '_glow_svg' );
	$file = $svg ? 'images/products/' . basename( $svg ) : 'images/products/_default.svg';

	if ( ! file_exists( get_template_directory() . '/' . $file ) ) {
		$file = 'images/products/_default.svg';
	}

	printf(
		'<img src="%1$s" alt="%2$s" width="600" height="600" loading="lazy" />',
		esc_url( get_template_directory_uri() . '/' . $file ),
		esc_attr( $product->get_name() )
	);
}

/**
 * Fallback primary menu before one is assigned: the five core journeys.
 */
function glow_primary_menu_fallback() {
	$shop = glow_wc_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	$items = array(
		array( __( 'Shop all', 'glow-kbeauty' ), $shop ),
		array( __( 'The routine', 'glow-kbeauty' ), home_url( '/#routine' ) ),
		array( __( 'Ingredients', 'glow-kbeauty' ), home_url( '/#ingredients' ) ),
		array( __( 'About', 'glow-kbeauty' ), home_url( '/about/' ) ),
		array( __( 'Help', 'glow-kbeauty' ), home_url( '/help/' ) ),
	);

	echo '<ul>';
	foreach ( $items as $item ) {
		printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $item[1] ), esc_html( $item[0] ) );
	}
	echo '</ul>';
}

/**
 * Footer link columns share one data source so footer.php stays lean.
 */
function glow_footer_columns() {
	$shop = glow_wc_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	return array(
		__( 'Shop', 'glow-kbeauty' )    => array(
			array( __( 'All products', 'glow-kbeauty' ), $shop ),
			array( __( 'Cleansers', 'glow-kbeauty' ), glow_step_url( 'cleansers' ) ),
			array( __( 'Serums & ampoules', 'glow-kbeauty' ), glow_step_url( 'serums-ampoules' ) ),
			array( __( 'Moisturisers', 'glow-kbeauty' ), glow_step_url( 'moisturisers' ) ),
			array( __( 'Sun care', 'glow-kbeauty' ), glow_step_url( 'sun-care' ) ),
		),
		__( 'Learn', 'glow-kbeauty' )   => array(
			array( __( 'The 7-step routine', 'glow-kbeauty' ), home_url( '/#routine' ) ),
			array( __( 'Ingredient index', 'glow-kbeauty' ), home_url( '/#ingredients' ) ),
			array( __( 'About us', 'glow-kbeauty' ), home_url( '/about/' ) ),
		),
		__( 'Support', 'glow-kbeauty' ) => array(
			array( __( 'Help & FAQ', 'glow-kbeauty' ), home_url( '/help/' ) ),
			array( __( 'Contact', 'glow-kbeauty' ), home_url( '/contact/' ) ),
			array( __( 'My account', 'glow-kbeauty' ), glow_wc_active() ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) ),
			array( __( 'Cart', 'glow-kbeauty' ), glow_wc_active() ? wc_get_cart_url() : home_url( '/' ) ),
		),
	);
}

/* --------------------------------------------------------------------------
 * Customizer
 * ------------------------------------------------------------------------ */

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/admin-setup.php';
require get_template_directory() . '/inc/admin-import.php';

/* --------------------------------------------------------------------------
 * Elementor integration
 * ------------------------------------------------------------------------ */

/**
 * Returns true when Elementor has taken over the current page/post.
 * Used by page templates to swap from hardcoded PHP to the_content().
 */
function glow_is_elementor_page() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	return 'builder' === get_post_meta( get_the_ID(), '_elementor_edit_mode', true );
}

function glow_elementor_category( $manager ) {
	$manager->add_category(
		'glow-kbeauty',
		array(
			'title' => __( 'Glow K-Beauty', 'glow-kbeauty' ),
			'icon'  => 'eicon-star',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'glow_elementor_category' );

function glow_register_elementor_widgets( $manager ) {
	require_once get_template_directory() . '/inc/elementor-widgets.php';
	$manager->register( new Glow_Hero_Stage_Widget() );
	$manager->register( new Glow_Routine_Rail_Widget() );
	$manager->register( new Glow_Concern_Tiles_Widget() );
	$manager->register( new Glow_Best_Sellers_Widget() );
	$manager->register( new Glow_Sourcing_Split_Widget() );
	$manager->register( new Glow_Ingredient_Index_Widget() );
	$manager->register( new Glow_Review_Cards_Widget() );
	$manager->register( new Glow_Newsletter_Widget() );
}
add_action( 'elementor/widgets/register', 'glow_register_elementor_widgets' );

/**
 * Remove Elementor\'s default padding on widget containers for our full-bleed
 * widgets, so the section CSS works as designed.
 */
function glow_elementor_widget_css() {
	$full_bleed = array(
		'glow_hero_stage', 'glow_routine_rail', 'glow_concern_tiles',
		'glow_best_sellers', 'glow_sourcing_split', 'glow_ingredient_index',
		'glow_review_cards', 'glow_newsletter',
	);

	$selectors = array_map( function ( $name ) {
		return '.elementor-widget-' . $name . ' > .elementor-widget-container';
	}, $full_bleed );

	echo '<style>' . implode( ', ', $selectors ) . ' { padding: 0; }</style>' . "\n";
}
add_action( 'wp_head', 'glow_elementor_widget_css', 5 );

/* --------------------------------------------------------------------------
 * SEO & structured data
 * ------------------------------------------------------------------------ */

require get_template_directory() . '/inc/seo.php';
