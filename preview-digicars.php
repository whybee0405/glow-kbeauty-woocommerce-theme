<?php
/**
 * Static preview harness for the DIGICARS theme.
 *
 * There is no WordPress/WooCommerce runtime in this repo, so we stub just
 * enough of WP/WC to render the theme's templates to a static HTML file for a
 * visual check. NOT part of the theme — never shipped.
 *
 * Usage:
 *   php preview-digicars.php [template]      e.g. php preview-digicars.php front-page.php
 *   (or pass ?template=archive-product.php as a query var when run via a server)
 *
 * Output: preview-digicars.html (gitignored).
 *
 * Mirrors the WP-stubbing pattern in preview.php (the kbeauty harness), adapted
 * for Digicars: header.php, footer.php and functions.php helpers.
 */

error_reporting( E_ALL & ~E_DEPRECATED );

define( 'ABSPATH', __DIR__ . '/' );
define( 'DAY_IN_SECONDS', 86400 );

$GLOBALS['digicars_theme_dir'] = __DIR__ . '/digicars-theme';

/* -------------------------------------------------------------------------
 * Asset URL handling.
 *
 * Templates are rendered into preview-digicars.html at the repo root, so all
 * theme asset URLs must resolve relative to digicars-theme/ for the file to be
 * viewable directly in a browser.
 * ---------------------------------------------------------------------- */
$GLOBALS['digicars_theme_uri'] = 'digicars-theme';

/* -------------------------------------------------------------------------
 * Hooks / setup no-ops.
 * ---------------------------------------------------------------------- */
function add_action( ...$a ) {}
function add_filter( ...$a ) {}
function remove_action( ...$a ) {}
function remove_filter( ...$a ) {}
function register_nav_menus( $a ) {}
function register_taxonomy( ...$a ) {}
function register_post_meta( ...$a ) {}
function add_theme_support( ...$a ) {}
function add_image_size( ...$a ) {}
function load_theme_textdomain( ...$a ) {}
function __return_false() { return false; }
function __return_true() { return true; }
function __return_empty_array() { return array(); }

/* Enqueue / script no-ops (functions.php registers assets via wp_head/footer). */
function wp_enqueue_style( ...$a ) {}
function wp_enqueue_script( ...$a ) {}
function wp_localize_script( ...$a ) {}
function wp_register_style( ...$a ) {}
function wp_register_script( ...$a ) {}
function wp_style_is( ...$a ) { return false; }
function wp_script_is( ...$a ) { return false; }

/* -------------------------------------------------------------------------
 * Escaping / i18n.
 * ---------------------------------------------------------------------- */
function __( $s, $d = null ) { return $s; }
function _e( $s, $d = null ) { echo $s; }
function _x( $s, $c = null, $d = null ) { return $s; }
function esc_html__( $s, $d = null ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr__( $s, $d = null ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_html_e( $s, $d = null ) { echo esc_html__( $s ); }
function esc_attr_e( $s, $d = null ) { echo esc_attr__( $s ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url( $s ) { return (string) $s; }
function esc_url_raw( $s ) { return (string) $s; }
function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function wp_kses( $s, $a = null ) { return $s; }
function wp_kses_post( $s ) { return $s; }
function number_format_i18n( $n, $d = 0 ) { return number_format( (float) $n, $d ); }
function _n( $single, $plural, $n, $d = null ) { return 1 === (int) $n ? $single : $plural; }
function _nx( $single, $plural, $n, $c = null, $d = null ) { return 1 === (int) $n ? $single : $plural; }
function sanitize_text_field( $s ) { return trim( (string) $s ); }
function sanitize_key( $s ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', (string) $s ) ); }
function sanitize_title( $s ) { return strtolower( preg_replace( '/[^a-z0-9\-]/i', '-', (string) $s ) ); }

/* -------------------------------------------------------------------------
 * URLs / theme paths.
 * ---------------------------------------------------------------------- */
function home_url( $p = '' ) { return '#' . ltrim( (string) $p, '#' ); }
function site_url( $p = '' ) { return '#' . ltrim( (string) $p, '#' ); }
function admin_url( $p = '' ) { return '#' . $p; }
function get_template_directory() { return $GLOBALS['digicars_theme_dir']; }
function get_stylesheet_directory() { return $GLOBALS['digicars_theme_dir']; }
function get_template_directory_uri() { return $GLOBALS['digicars_theme_uri']; }
function get_stylesheet_directory_uri() { return $GLOBALS['digicars_theme_uri']; }
function get_stylesheet_uri() { return $GLOBALS['digicars_theme_uri'] . '/style.css'; }
function get_theme_file_path( $file = '' ) {
	return $GLOBALS['digicars_theme_dir'] . ( $file ? '/' . ltrim( $file, '/' ) : '' );
}
function get_theme_file_uri( $file = '' ) {
	return $GLOBALS['digicars_theme_uri'] . ( $file ? '/' . ltrim( $file, '/' ) : '' );
}
function add_query_arg( $args, $url = '' ) {
	if ( ! is_array( $args ) ) {
		$args = array( $args => $url );
		$url  = func_num_args() > 2 ? func_get_arg( 2 ) : '';
	}
	$sep = ( false === strpos( (string) $url, '?' ) ) ? '?' : '&';
	return $url . $sep . http_build_query( (array) $args );
}

/* -------------------------------------------------------------------------
 * Misc WP no-ops / nonces / options.
 * ---------------------------------------------------------------------- */
function wp_create_nonce( $a = '' ) { return 'previewnonce'; }
function get_option( $k, $default = false ) { return $default; }
function update_option( ...$a ) { return true; }
function delete_option( ...$a ) { return true; }
function get_transient( $k ) { return false; }
function set_transient( ...$a ) { return true; }
function delete_transient( ...$a ) { return true; }
function is_admin() { return false; }
function is_wp_error( $thing ) { return false; }
function current_user_can( ...$a ) { return false; }
function wp_unslash( $v ) { return $v; }
function absint( $v ) { return abs( (int) $v ); }

/* -------------------------------------------------------------------------
 * Document head / body helpers used by header.php + footer.php.
 * ---------------------------------------------------------------------- */
function language_attributes() { echo 'lang="en"'; }
function bloginfo( $k ) { echo 'charset' === $k ? 'UTF-8' : 'Digicars'; }
function get_bloginfo( $k ) { return 'Digicars'; }
function body_class( $extra = '' ) {
	$c = trim( 'home ' . ( is_array( $extra ) ? implode( ' ', $extra ) : (string) $extra ) );
	echo 'class="' . esc_attr( $c ) . '"';
}
function wp_body_open() {}

function wp_head() {
	$uri = $GLOBALS['digicars_theme_uri'];
	echo '<script>document.documentElement.classList.add("js");</script>' . "\n";
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo+Expanded:wght@700;800&family=Hanken+Grotesk:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap">' . "\n";
	echo '<link rel="stylesheet" href="' . esc_attr( $uri ) . '/style.css">' . "\n";
	echo '<link rel="stylesheet" href="' . esc_attr( $uri ) . '/css/woocommerce.css">' . "\n";
}

function wp_footer() {
	$uri = $GLOBALS['digicars_theme_uri'];
	echo '<script>var digicarsData={ajaxUrl:"#",nonce:"previewnonce"};</script>' . "\n";
	echo '<script src="' . esc_attr( $uri ) . '/js/main.js"></script>' . "\n";
	echo '<script src="' . esc_attr( $uri ) . '/js/concierge.js"></script>' . "\n";
	echo '<script src="' . esc_attr( $uri ) . '/js/affordability.js"></script>' . "\n";
}

/* -------------------------------------------------------------------------
 * Navigation / search form.
 * ---------------------------------------------------------------------- */
function wp_nav_menu( $args ) {
	if ( isset( $args['fallback_cb'] ) && is_callable( $args['fallback_cb'] ) ) {
		call_user_func( $args['fallback_cb'] );
	}
}
function has_custom_logo() { return false; }
function the_custom_logo() {}

function get_search_form( $echo = true ) {
	$shop = home_url( '/shop' );
	$form = '<form role="search" method="get" class="search-form" action="' . esc_url( $shop ) . '">'
		. '<label class="screen-reader-text" for="digicars-s">' . esc_html__( 'Search vehicles' ) . '</label>'
		. '<input type="search" id="digicars-s" name="s" placeholder="' . esc_attr__( 'Search make, model or body type' ) . '">'
		. '<button type="submit" class="btn btn--signal">' . esc_html__( 'Search' ) . '</button>'
		. '</form>';
	if ( $echo ) {
		echo $form; // phpcs:ignore
		return;
	}
	return $form;
}

/* -------------------------------------------------------------------------
 * Template tags / conditionals occasionally referenced by templates.
 * ---------------------------------------------------------------------- */
function is_tax( ...$a ) { return false; }
function is_search() { return false; }
function is_shop() { return false; }
function is_product() { return false; }
function is_product_category() { return false; }
function is_cart() { return false; }
function is_checkout() { return false; }
function get_term_by( ...$a ) { return false; }
function get_queried_object() { return null; }
function setup_postdata( ...$a ) {}
function wp_reset_postdata() {}
function get_post( $id = null ) { return null; }
function the_title() {}
function get_the_title( $id = 0 ) { return ''; }

/* WooCommerce page/url helpers used in header/footer (WooCommerce inactive). */
function wc_get_page_permalink( $page ) { return ''; }

/* -------------------------------------------------------------------------
 * Vehicle meta store + WC_Product-like stub.
 *
 * The theme reads vehicle attributes via get_post_meta($id,$key,true), wrapped
 * by digicars_meta($id,$key). Both resolve against $GLOBALS['digicars_meta_store']
 * keyed by product id, populated from the dummy vehicles below.
 * ---------------------------------------------------------------------- */
$GLOBALS['digicars_meta_store']    = array();
$GLOBALS['digicars_product_store'] = array();

function get_post_meta( $id, $key = '', $single = false ) {
	$store = $GLOBALS['digicars_meta_store'];
	if ( ! isset( $store[ $id ] ) ) {
		return $single ? '' : array();
	}
	if ( '' === $key ) {
		return $store[ $id ];
	}
	if ( ! array_key_exists( $key, $store[ $id ] ) ) {
		return $single ? '' : array();
	}
	$value = $store[ $id ][ $key ];
	return $single ? $value : array( $value );
}

if ( ! class_exists( 'WC_Product' ) ) {
	/**
	 * Minimal WC_Product stand-in exposing only the methods the Digicars
	 * templates and functions.php helpers call.
	 */
	class WC_Product {
		protected $id;
		protected $data;

		public function __construct( $id, array $data = array() ) {
			$this->id   = (int) $id;
			$this->data = $data;
		}

		public function get_id() { return $this->id; }
		public function get_name() { return $this->data['name'] ?? ''; }
		public function get_title() { return $this->get_name(); }
		public function get_average_rating() { return (string) ( $this->data['rating'] ?? '0' ); }
		public function get_rating_count() { return (int) ( $this->data['rating_count'] ?? 0 ); }
		public function get_price() { return (string) ( $this->data['price'] ?? '' ); }
		public function get_regular_price() { return $this->get_price(); }
		public function get_price_html() {
			$p = (float) $this->get_price();
			return $p > 0 ? 'R' . number_format_i18n( $p ) : '';
		}
		public function get_permalink() { return $this->data['permalink'] ?? home_url( '/vehicle/' . $this->id ); }
		public function is_featured() { return ! empty( $this->data['featured'] ); }
		public function is_on_sale() { return false; }
		public function is_purchasable() { return false; }
		public function get_image( $size = 'digicars-card', $attr = array() ) {
			$src = $this->data['image'] ?? ( $GLOBALS['digicars_theme_uri'] . '/images/vehicles/_default.jpg' );
			$alt = esc_attr( $this->get_name() );
			return '<img class="vehicle-card__img" src="' . esc_attr( $src ) . '" alt="' . $alt . '" loading="lazy" width="640" height="420">';
		}
		public function get_image_id() { return 0; }
	}
}

function wc_get_product( $id ) {
	$id = $id instanceof WC_Product ? $id->get_id() : (int) $id;
	return $GLOBALS['digicars_product_store'][ $id ] ?? null;
}
function wc_get_products( $args = array() ) {
	$ids = array_keys( $GLOBALS['digicars_product_store'] );
	if ( isset( $args['return'] ) && 'ids' === $args['return'] ) {
		return $ids;
	}
	return array_values( $GLOBALS['digicars_product_store'] );
}
function wc_price( $price, $args = array() ) { return 'R' . number_format_i18n( (float) $price ); }
function wc_get_template_part( ...$a ) {}

/* -------------------------------------------------------------------------
 * Dummy vehicle fixtures — ~4 vehicles spanning conditions + body types.
 * ZAR pricing, monthly-from, identity + powertrain + history fields.
 * ---------------------------------------------------------------------- */
function digicars_preview_seed() {
	$img = $GLOBALS['digicars_theme_uri'] . '/images/vehicles/_default.jpg';

	$vehicles = array(
		101 => array(
			'name'     => '2026 Chery Tiggo 7 Pro 1.5T Elite',
			'price'    => 469900,
			'rating'   => 4.6,
			'featured' => true,
			'image'    => $img,
			'meta'     => array(
				'_vehicle_make'         => 'Chery',
				'_vehicle_model'        => 'Tiggo 7 Pro',
				'_vehicle_variant'      => '1.5T Elite',
				'_vehicle_year'         => 2026,
				'_vehicle_body_type'    => 'SUV',
				'_vehicle_condition'    => 'New',
				'_vehicle_price'        => 469900,
				'_vehicle_monthly_from' => 7990,
				'_vehicle_fuel'         => 'Petrol',
				'_vehicle_transmission' => 'Automatic',
				'_vehicle_mileage'      => 0,
				'_vehicle_seats'        => 5,
				'_vehicle_colour'       => 'Phantom Grey',
				'_vehicle_dealer'       => 'Digicars Sandton',
				'_vehicle_lifestyle_tags' => array( 'family', 'first-car' ),
				'_vehicle_features'     => array( 'Panoramic sunroof', 'Adaptive cruise' ),
			),
		),
		102 => array(
			'name'     => '2025 Volkswagen Polo 1.0 TSI Life (Demo)',
			'price'    => 379500,
			'rating'   => 4.8,
			'featured' => false,
			'image'    => $img,
			'meta'     => array(
				'_vehicle_make'         => 'Volkswagen',
				'_vehicle_model'        => 'Polo',
				'_vehicle_variant'      => '1.0 TSI Life',
				'_vehicle_year'         => 2025,
				'_vehicle_body_type'    => 'Hatchback',
				'_vehicle_condition'    => 'Demo',
				'_vehicle_price'        => 379500,
				'_vehicle_monthly_from' => 6490,
				'_vehicle_fuel'         => 'Petrol',
				'_vehicle_transmission' => 'Manual',
				'_vehicle_mileage'      => 4200,
				'_vehicle_seats'        => 5,
				'_vehicle_colour'       => 'Pure White',
				'_vehicle_dealer'       => 'Digicars Midrand',
				'_vehicle_lifestyle_tags' => array( 'commuter', 'first-car' ),
				'_vehicle_features'     => array( 'App-Connect', 'Rear camera' ),
			),
		),
		103 => array(
			'name'     => '2022 Ford Ranger 2.0 BiT Wildtrak 4x4 (Used)',
			'price'    => 689000,
			'rating'   => 4.5,
			'featured' => true,
			'image'    => $img,
			'meta'     => array(
				'_vehicle_make'         => 'Ford',
				'_vehicle_model'        => 'Ranger',
				'_vehicle_variant'      => '2.0 BiT Wildtrak 4x4',
				'_vehicle_year'         => 2022,
				'_vehicle_body_type'    => 'Double Cab',
				'_vehicle_condition'    => 'Used',
				'_vehicle_price'        => 689000,
				'_vehicle_monthly_from' => 11750,
				'_vehicle_fuel'         => 'Diesel',
				'_vehicle_transmission' => 'Automatic',
				'_vehicle_mileage'      => 68400,
				'_vehicle_previous_owners' => 1,
				'_vehicle_seats'        => 5,
				'_vehicle_colour'       => 'Sea Grey',
				'_vehicle_dealer'       => 'Digicars Pretoria',
				'_vehicle_lifestyle_tags' => array( 'fleet', 'adventure' ),
				'_vehicle_features'     => array( 'Tow bar', 'Load-bin liner' ),
			),
		),
		104 => array(
			'name'     => '2026 Omoda E5 EV Elegance (New EV)',
			'price'    => 639900,
			'rating'   => 4.7,
			'featured' => false,
			'image'    => $img,
			'meta'     => array(
				'_vehicle_make'         => 'Omoda',
				'_vehicle_model'        => 'E5',
				'_vehicle_variant'      => 'EV Elegance',
				'_vehicle_year'         => 2026,
				'_vehicle_body_type'    => 'SUV',
				'_vehicle_condition'    => 'New',
				'_vehicle_price'        => 639900,
				'_vehicle_monthly_from' => 10890,
				'_vehicle_fuel'         => 'Electric',
				'_vehicle_transmission' => 'Automatic',
				'_vehicle_mileage'      => 0,
				'_vehicle_range_km'     => 430,
				'_vehicle_battery_kwh'  => 61.1,
				'_vehicle_seats'        => 5,
				'_vehicle_colour'       => 'Vortex Grey',
				'_vehicle_dealer'       => 'Digicars Sandton',
				'_vehicle_lifestyle_tags' => array( 'eco', 'family' ),
				'_vehicle_features'     => array( '50 kW DC fast charge', 'Heat pump' ),
			),
		),
	);

	foreach ( $vehicles as $id => $v ) {
		$GLOBALS['digicars_meta_store'][ $id ]    = $v['meta'];
		$GLOBALS['digicars_product_store'][ $id ] = new WC_Product(
			$id,
			array(
				'name'     => $v['name'],
				'price'    => $v['price'],
				'rating'   => $v['rating'],
				'featured' => $v['featured'],
				'image'    => $v['image'],
			)
		);
	}
}
digicars_preview_seed();

/* -------------------------------------------------------------------------
 * Load the REAL theme helpers (functions.php). It registers hooks via the
 * no-op add_action/add_filter stubs above and defines the digicars_* helpers
 * with their real bodies, so templates exercise genuine theme code.
 * ---------------------------------------------------------------------- */
require $GLOBALS['digicars_theme_dir'] . '/functions.php';

/* -------------------------------------------------------------------------
 * Choose the template to render.
 *   1. $argv[1] when run on the CLI.
 *   2. ?template= query var when served.
 *   3. Default front-page.php.
 * ---------------------------------------------------------------------- */
$template = 'front-page.php';
if ( isset( $argv[1] ) && '' !== trim( (string) $argv[1] ) ) {
	$template = trim( (string) $argv[1] );
} elseif ( isset( $_GET['template'] ) && '' !== trim( (string) $_GET['template'] ) ) {
	$template = trim( (string) $_GET['template'] );
}
// Guard against path traversal — only a bare filename within the theme.
$template = basename( $template );

/* -------------------------------------------------------------------------
 * Render: header + (template | graceful placeholder) + footer.
 * ---------------------------------------------------------------------- */
$template_path = $GLOBALS['digicars_theme_dir'] . '/' . $template;
$has_template  = is_file( $template_path ) && filesize( $template_path ) > 0;

ob_start();
require $GLOBALS['digicars_theme_dir'] . '/header.php';

if ( $has_template ) {
	require $template_path;
} else {
	// Graceful degradation — the chosen template is not built yet (or empty).
	printf(
		'<div class="container" style="padding:var(--s-12,4rem) 0;">'
		. '<p class="muted">Preview harness: <code>%s</code> is not built yet (empty or missing). '
		. 'Rendering header + footer only.</p></div>',
		esc_html( $template )
	);
}

require $GLOBALS['digicars_theme_dir'] . '/footer.php';
$html = ob_get_clean();

$out = __DIR__ . '/preview-digicars.html';
file_put_contents( $out, $html );

if ( 'cli' === PHP_SAPI ) {
	fwrite(
		STDOUT,
		sprintf(
			"Rendered template '%s' (%s) -> %s (%d bytes)\n",
			$template,
			$has_template ? 'built' : 'placeholder',
			$out,
			strlen( $html )
		)
	);
} else {
	echo $html;
}
