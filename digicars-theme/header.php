<?php
/**
 * Header template.
 *
 * Outputs the document head, the notice bar, the sticky site header (brand,
 * primary navigation, utilities) and the mobile overlay menu, then opens the
 * main content region which footer.php closes.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

if ( ! function_exists( 'digicars_default_nav' ) ) {
	/**
	 * Fallback primary navigation when no menu is assigned to the
	 * `primary` location. Mirrors the marketing information architecture.
	 */
	function digicars_default_nav() {
		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
		if ( ! $shop_url ) {
			$shop_url = home_url( '/shop' );
		}

		$links = array(
			array( 'url' => $shop_url,                    'label' => __( 'Cars in stock', 'digicars' ) ),
			array( 'url' => home_url( '/finance' ),       'label' => __( 'Finance', 'digicars' ) ),
			array( 'url' => home_url( '/sell' ),          'label' => __( 'Trade-in', 'digicars' ) ),
			array( 'url' => home_url( '/book-a-service' ),'label' => __( 'Book a service', 'digicars' ) ),
			array( 'url' => home_url( '/car-torque' ),    'label' => __( 'Blog', 'digicars' ) ),
			array( 'url' => home_url( '/about' ),         'label' => __( 'About', 'digicars' ) ),
		);

		echo '<ul class="nav">';
		foreach ( $links as $link ) {
			printf(
				'<li><a class="nav__link" href="%1$s">%2$s</a></li>',
				esc_url( $link['url'] ),
				esc_html( $link['label'] )
			);
		}
		echo '</ul>';
	}
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'digicars' ); ?></a>

<div class="notice-bar" role="region" aria-label="<?php esc_attr_e( 'Announcements', 'digicars' ); ?>">
	<div class="notice-bar__track" aria-hidden="true">
		<span><?php esc_html_e( 'Finance from major SA banks', 'digicars' ); ?></span>
		<span class="notice-bar__sep">&#x2022;</span>
		<span><?php esc_html_e( 'Every vehicle verified', 'digicars' ); ?></span>
		<span class="notice-bar__sep">&#x2022;</span>
		<span><?php esc_html_e( '23 000+ happy South African drivers', 'digicars' ); ?></span>
		<span class="notice-bar__sep">&#x2022;</span>
		<span><?php esc_html_e( 'Digital-first automotive showroom', 'digicars' ); ?></span>
		<span class="notice-bar__sep">&#x2022;</span>
		<span><?php esc_html_e( 'Finance from major SA banks', 'digicars' ); ?></span>
		<span class="notice-bar__sep">&#x2022;</span>
		<span><?php esc_html_e( 'Every vehicle verified', 'digicars' ); ?></span>
		<span class="notice-bar__sep">&#x2022;</span>
		<span><?php esc_html_e( '23 000+ happy South African drivers', 'digicars' ); ?></span>
		<span class="notice-bar__sep">&#x2022;</span>
		<span><?php esc_html_e( 'Digital-first automotive showroom', 'digicars' ); ?></span>
		<span class="notice-bar__sep">&#x2022;</span>
	</div>
	<p class="notice-bar__static sr-only"><?php esc_html_e( 'Finance from major SA banks · Every vehicle verified · Digital-first automotive showroom', 'digicars' ); ?></p>
</div>

<header class="site-header" data-site-header>
	<div class="site-header__inner container">

		<div class="site-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">Digi<span>cars</span></a>
			<?php endif; ?>
		</div>

		<nav class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'digicars' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav',
					'fallback_cb'    => 'digicars_default_nav',
				)
			);
			?>
		</nav>

		<div class="header-utils">
			<button type="button" class="icon-btn" aria-label="<?php esc_attr_e( 'Search', 'digicars' ); ?>" aria-controls="header-search" aria-expanded="false" data-search-toggle>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<circle cx="11" cy="11" r="7"></circle>
					<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
				</svg>
			</button>

			<a class="icon-btn btn--desktop" href="<?php echo esc_url( home_url( '/compare' ) ); ?>" aria-label="<?php esc_attr_e( 'Compare vehicles', 'digicars' ); ?>" data-compare-link>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M3 6h12"></path>
					<path d="M9 2 3 6l6 4"></path>
					<path d="M21 18H9"></path>
					<path d="m15 14 6 4-6 4"></path>
				</svg>
				<span class="compare-count" data-compare-count>0</span>
			</a>

			<a class="btn btn--signal btn--sm btn--desktop" href="<?php echo esc_url( home_url( '/concierge' ) ); ?>">
				<?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?>
			</a>

			<button type="button" class="icon-btn nav-toggle" aria-label="<?php esc_attr_e( 'Menu', 'digicars' ); ?>" aria-controls="mobile-menu" aria-expanded="false" data-nav-toggle>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="3" y1="6" x2="21" y2="6"></line>
					<line x1="3" y1="12" x2="21" y2="12"></line>
					<line x1="3" y1="18" x2="21" y2="18"></line>
				</svg>
			</button>
		</div>
	</div>

	<div class="site-header__search" id="header-search" hidden data-search-panel>
		<div class="container">
			<?php get_search_form(); ?>
		</div>
	</div>
</header>

<div class="mobile-menu" id="mobile-menu" data-mobile-menu>
	<div class="mobile-menu__head">
		<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">Digi<span>cars</span></a>
		<button type="button" class="icon-btn" aria-label="<?php esc_attr_e( 'Close menu', 'digicars' ); ?>" data-nav-close>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<line x1="18" y1="6" x2="6" y2="18"></line>
				<line x1="6" y1="6" x2="18" y2="18"></line>
			</svg>
		</button>
	</div>

	<nav class="mobile-menu__nav" aria-label="<?php esc_attr_e( 'Mobile', 'digicars' ); ?>">
		<?php
		$digicars_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
		if ( ! $digicars_shop_url ) {
			$digicars_shop_url = home_url( '/shop' );
		}
		$digicars_mobile_links = array(
			array( 'url' => $digicars_shop_url, 'label' => __( 'Cars in stock', 'digicars' ) ),
			array( 'url' => home_url( '/finance' ), 'label' => __( 'Finance', 'digicars' ) ),
			array( 'url' => home_url( '/sell' ), 'label' => __( 'Trade-in', 'digicars' ) ),
			array( 'url' => home_url( '/book-a-service' ), 'label' => __( 'Book a service', 'digicars' ) ),
			array( 'url' => home_url( '/car-torque' ), 'label' => __( 'Blog', 'digicars' ) ),
			array( 'url' => home_url( '/about' ), 'label' => __( 'About', 'digicars' ) ),
		);
		foreach ( $digicars_mobile_links as $digicars_link ) {
			printf(
				'<a class="mobile-menu__link" href="%1$s">%2$s</a>',
				esc_url( $digicars_link['url'] ),
				esc_html( $digicars_link['label'] )
			);
		}
		?>
	</nav>

	<div class="mobile-menu__footer">
		<a class="btn btn--signal btn--block" href="<?php echo esc_url( home_url( '/concierge' ) ); ?>">
			<?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?>
		</a>
		<p class="muted" style="margin-top:var(--s-4);">
			<a href="tel:0105951180">010 595 1180</a><br>
			<a href="mailto:info@digicars.co.za">info@digicars.co.za</a>
		</p>
	</div>
</div>

<main id="main" class="site-main">
