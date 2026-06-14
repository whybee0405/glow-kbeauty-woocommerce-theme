<?php
/**
 * Site header: notice bar, sticky header, mobile menu, search overlay.
 *
 * @package Glow_KBeauty
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'glow-kbeauty' ); ?></a>

<div class="notice-bar">
	<div class="container">
		<span><?php echo esc_html( get_theme_mod( 'glow_notice_1', __( 'Free shipping over R500', 'glow-kbeauty' ) ) ); ?></span>
		<span class="dot" aria-hidden="true">·</span>
		<span class="notice-extra"><?php echo esc_html( get_theme_mod( 'glow_notice_2', __( 'Every batch verified', 'glow-kbeauty' ) ) ); ?></span>
		<span class="dot notice-extra" aria-hidden="true">·</span>
		<span class="notice-extra"><?php echo esc_html( get_theme_mod( 'glow_notice_3', __( 'Authentic K-beauty', 'glow-kbeauty' ) ) ); ?></span>
	</div>
</div>

<header class="site-header" data-header>
	<div class="container header-grid">

		<div class="header-brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( 'Glow — home', 'glow-kbeauty' ); ?>">
					<?php glow_inline_logo(); ?>
				</a>
			<?php endif; ?>
		</div>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'glow-kbeauty' ); ?>">
			<ul>
				<li class="has-flyout">
					<button class="nav-shop-trigger" type="button" aria-expanded="false" data-flyout-trigger>
						<?php esc_html_e( 'Shop', 'glow-kbeauty' ); ?>
						<svg class="nav-caret" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m1 1 4 4 4-4"/></svg>
					</button>
				</li>
				<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'glow-kbeauty' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'glow-kbeauty' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'glow-kbeauty' ); ?></a></li>
			</ul>
		</nav>

		<div class="header-utils">
			<button class="util-btn search-toggle" type="button" aria-label="<?php esc_attr_e( 'Search products', 'glow-kbeauty' ); ?>" aria-expanded="false" data-search-toggle>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.8-3.8"/></svg>
			</button>

			<?php if ( glow_wc_active() ) : ?>
				<a class="util-btn" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'My account', 'glow-kbeauty' ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/></svg>
				</a>

				<a class="util-btn cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Shopping bag', 'glow-kbeauty' ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 8h14l-1.2 12.2a1.5 1.5 0 0 1-1.5 1.3H7.7a1.5 1.5 0 0 1-1.5-1.3Z"/><path d="M8.5 8V6.5a3.5 3.5 0 0 1 7 0V8"/></svg>
					<span class="cart-count" data-cart-count><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
				</a>
			<?php endif; ?>

			<button class="util-btn menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'glow-kbeauty' ); ?>" aria-expanded="false" data-menu-toggle>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
			</button>
		</div>

	</div>

	<!-- Shop mega-menu flyout — full width, positioned from .site-header -->
	<div class="nav-flyout" hidden data-flyout>
		<div class="container flyout-inner">

			<div class="flyout-col">
				<p class="flyout-label"><?php esc_html_e( 'By Routine', 'glow-kbeauty' ); ?></p>
				<ul class="flyout-list">
					<?php foreach ( glow_routine_steps() as $step ) : ?>
						<li>
							<a href="<?php echo esc_url( glow_step_url( $step['slug'] ) ); ?>">
								<span class="flyout-no"><?php echo esc_html( $step['no'] ); ?></span>
								<?php echo esc_html( $step['name'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="flyout-col">
				<p class="flyout-label"><?php esc_html_e( 'By Concern', 'glow-kbeauty' ); ?></p>
				<ul class="flyout-list">
					<li><a href="<?php echo esc_url( glow_tax_url( 'dehydrated-dull', 'skin_concern' ) ); ?>"><?php esc_html_e( 'Dehydrated & dull', 'glow-kbeauty' ); ?></a></li>
					<li><a href="<?php echo esc_url( glow_tax_url( 'breakouts-texture', 'skin_concern' ) ); ?>"><?php esc_html_e( 'Breakouts & texture', 'glow-kbeauty' ); ?></a></li>
					<li><a href="<?php echo esc_url( glow_tax_url( 'fine-lines-firmness', 'skin_concern' ) ); ?>"><?php esc_html_e( 'Fine lines & firmness', 'glow-kbeauty' ); ?></a></li>
					<li><a href="<?php echo esc_url( glow_tax_url( 'sensitive-reactive', 'skin_concern' ) ); ?>"><?php esc_html_e( 'Sensitive & reactive', 'glow-kbeauty' ); ?></a></li>
				</ul>
			</div>

			<div class="flyout-col">
				<p class="flyout-label"><?php esc_html_e( 'By Ingredient', 'glow-kbeauty' ); ?></p>
				<ul class="flyout-list">
					<?php
					$glow_flyout_ings = array(
						array( __( 'Snail mucin', 'glow-kbeauty' ), 'snail mucin' ),
						array( __( 'Centella asiatica', 'glow-kbeauty' ), 'centella' ),
						array( __( 'Niacinamide', 'glow-kbeauty' ), 'niacinamide' ),
						array( __( 'Hyaluronic acid', 'glow-kbeauty' ), 'hyaluronic' ),
						array( __( 'Rice extract', 'glow-kbeauty' ), 'rice extract' ),
					);
					foreach ( $glow_flyout_ings as $ing ) :
						$ing_url = add_query_arg( array( 's' => rawurlencode( $ing[1] ), 'post_type' => 'product' ), home_url( '/' ) );
					?>
						<li><a href="<?php echo esc_url( $ing_url ); ?>"><?php echo esc_html( $ing[0] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

		</div>
	</div>
</header>

<div class="mobile-menu" data-mobile-menu hidden>
	<div class="mobile-menu-top">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( 'Glow — home', 'glow-kbeauty' ); ?>">
			<?php glow_inline_logo(); ?>
		</a>
		<button class="mobile-menu-close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'glow-kbeauty' ); ?>" data-menu-close>
			<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
		</button>
	</div>

	<nav aria-label="<?php esc_attr_e( 'Mobile', 'glow-kbeauty' ); ?>">
		<p class="mobile-section-head"><?php esc_html_e( 'Shop', 'glow-kbeauty' ); ?></p>
		<ul class="mobile-shop-links">
			<li><a href="<?php echo esc_url( glow_wc_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'All products', 'glow-kbeauty' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#routine' ) ); ?>"><?php esc_html_e( 'By Routine', 'glow-kbeauty' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#concerns' ) ); ?>"><?php esc_html_e( 'By Concern', 'glow-kbeauty' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/#ingredients' ) ); ?>"><?php esc_html_e( 'By Ingredient', 'glow-kbeauty' ); ?></a></li>
		</ul>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'glow-kbeauty' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'glow-kbeauty' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'glow-kbeauty' ); ?></a></li>
		</ul>
	</nav>

	<p class="mobile-menu-foot"><?php esc_html_e( 'Free shipping over R500 · Johannesburg, SA', 'glow-kbeauty' ); ?></p>
</div>

<div class="search-overlay" data-search-overlay hidden>
	<div class="search-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search products', 'glow-kbeauty' ); ?>">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form-row">
			<label class="screen-reader-text" for="glow-search-field"><?php esc_html_e( 'Search products', 'glow-kbeauty' ); ?></label>
			<input type="search" id="glow-search-field" name="s" placeholder="<?php esc_attr_e( 'Try “snail mucin” or “sunscreen”', 'glow-kbeauty' ); ?>" data-search-field />
			<input type="hidden" name="post_type" value="product" />
			<button class="btn btn-solid" type="submit"><?php esc_html_e( 'Search', 'glow-kbeauty' ); ?></button>
		</form>
		<p class="search-hint"><?php esc_html_e( 'Searching by ingredient works — we index actives', 'glow-kbeauty' ); ?></p>
	</div>
</div>
