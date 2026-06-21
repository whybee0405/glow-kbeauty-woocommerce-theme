<?php
/**
 * Static preview harness — stubs just enough of WordPress to render the
 * theme's homepage for a visual check. Not part of the theme.
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'DAY_IN_SECONDS', 86400 );

$GLOBALS['glow_theme_dir'] = __DIR__ . '/kbeauty-theme';

function add_action( ...$a ) {}
function add_filter( ...$a ) {}
function register_nav_menus( $a ) {}
function add_theme_support( ...$a ) {}
function add_image_size( ...$a ) {}
function load_theme_textdomain( ...$a ) {}
function wp_create_nonce( $a ) { return 'stub'; }
function admin_url( $p = '' ) { return '#' . $p; }

function __( $s, $d = null ) { return $s; }
function esc_html__( $s, $d = null ) { return htmlspecialchars( $s, ENT_QUOTES ); }
function esc_attr__( $s, $d = null ) { return htmlspecialchars( $s, ENT_QUOTES ); }
function esc_html_e( $s, $d = null ) { echo esc_html__( $s ); }
function esc_attr_e( $s, $d = null ) { echo esc_attr__( $s ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url( $s ) { return $s; }
function wp_kses( $s, $a ) { return $s; }
function wp_kses_post( $s ) { return $s; }
function number_format_i18n( $n, $d = 0 ) { return number_format( (float) $n, $d ); }
function _n( $single, $plural, $n, $d = null ) { return 1 === (int) $n ? $single : $plural; }

function home_url( $p = '' ) { return '#' . $p; }
function get_template_directory() { return $GLOBALS['glow_theme_dir']; }
function get_template_directory_uri() { return 'kbeauty-theme'; }
function get_stylesheet_uri() { return 'kbeauty-theme/style.css'; }

function get_header() { require $GLOBALS['glow_theme_dir'] . '/header.php'; }
function get_footer() { require $GLOBALS['glow_theme_dir'] . '/footer.php'; }

function language_attributes() { echo 'lang="en"'; }
function bloginfo( $k ) { echo 'charset' === $k ? 'UTF-8' : 'Glow'; }
function get_bloginfo( $k ) { return 'Glow'; }
function body_class() { echo 'class="home"'; }
function wp_body_open() {}

function wp_head() {
	echo '<script>document.documentElement.classList.add("js");</script>' . "\n";
	echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:ital,wght@0,400..700;1,400..700&family=Spline+Sans+Mono:wght@400..600&family=Young+Serif&display=swap">' . "\n";
	echo '<link rel="stylesheet" href="kbeauty-theme/style.css">' . "\n";
}

function wp_footer() {
	echo '<script>var glowData={ajaxUrl:"#",nonce:"stub"};</script>' . "\n";
	echo '<script src="kbeauty-theme/js/main.js"></script>' . "\n";
}

function wp_nav_menu( $args ) {
	if ( isset( $args['fallback_cb'] ) && is_callable( $args['fallback_cb'] ) ) {
		call_user_func( $args['fallback_cb'] );
	}
}

function has_custom_logo() { return false; }
function is_tax( ...$a ) { return false; }
function is_search() { return false; }
function get_term_by( ...$a ) { return false; }
function get_queried_object() { return null; }
function get_post_meta( ...$a ) { return ''; }
function add_query_arg( $args, $url = '' ) { return $url . '?' . http_build_query( (array) $args ); }
function rawurlencode_deep( $v ) { return $v; }
function gmdate_i18n() {}

require $GLOBALS['glow_theme_dir'] . '/functions.php';
require $GLOBALS['glow_theme_dir'] . '/front-page.php';
