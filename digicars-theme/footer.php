<?php
/**
 * Footer template.
 *
 * Closes the main content region, then renders the carbon site footer: brand
 * lead + Concierge CTA, the navigation grid, contact and socials, the legal
 * bottom bar with payment badges, and the oversized display wordmark.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

$digicars_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
if ( ! $digicars_shop_url ) {
	$digicars_shop_url = home_url( '/shop' );
}
?>
</main>

<footer class="site-footer surface-carbon" role="contentinfo">
	<div class="container">

		<div class="footer-lead cluster cluster--between" style="margin-bottom:var(--s-8);">
			<p class="footer-lead">
				<?php esc_html_e( 'The showroom that thinks. Tell us what you need and the Concierge finds your next car.', 'digicars' ); ?>
			</p>
			<a class="btn btn--signal" href="<?php echo esc_url( home_url( '/concierge' ) ); ?>">
				<?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?>
			</a>
		</div>

		<div class="footer-grid">

			<div class="footer-col">
				<div class="footer-col__title"><?php esc_html_e( 'Digicars', 'digicars' ); ?></div>
				<p class="muted" style="max-width:32ch;">
					<?php esc_html_e( 'Fueled by passion. Driven by technology. A digital-first, multi-brand car marketplace.', 'digicars' ); ?>
				</p>
			</div>

			<div class="footer-col">
				<div class="footer-col__title"><?php esc_html_e( 'Browse', 'digicars' ); ?></div>
				<nav class="footer-col__list" aria-label="<?php esc_attr_e( 'Browse', 'digicars' ); ?>">
					<a href="<?php echo esc_url( $digicars_shop_url ); ?>"><?php esc_html_e( 'All cars', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'condition', 'new', $digicars_shop_url ) ); ?>"><?php esc_html_e( 'New', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'condition', 'demo', $digicars_shop_url ) ); ?>"><?php esc_html_e( 'Demo', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'condition', 'used', $digicars_shop_url ) ); ?>"><?php esc_html_e( 'Used', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/specials' ) ); ?>"><?php esc_html_e( 'Specials', 'digicars' ); ?></a>
				</nav>
			</div>

			<div class="footer-col">
				<div class="footer-col__title"><?php esc_html_e( 'Services', 'digicars' ); ?></div>
				<nav class="footer-col__list" aria-label="<?php esc_attr_e( 'Services', 'digicars' ); ?>">
					<a href="<?php echo esc_url( home_url( '/finance' ) ); ?>"><?php esc_html_e( 'Finance', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/sell' ) ); ?>"><?php esc_html_e( 'Trade-in / sell your car', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/book-a-service' ) ); ?>"><?php esc_html_e( 'Book a service', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/find-a-dealer' ) ); ?>"><?php esc_html_e( 'Find a dealer', 'digicars' ); ?></a>
				</nav>
			</div>

			<div class="footer-col">
				<div class="footer-col__title"><?php esc_html_e( 'Company', 'digicars' ); ?></div>
				<nav class="footer-col__list" aria-label="<?php esc_attr_e( 'Company', 'digicars' ); ?>">
					<a href="<?php echo esc_url( home_url( '/about' ) ); ?>"><?php esc_html_e( 'About', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/car-torque' ) ); ?>"><?php esc_html_e( 'Car Torque (blog)', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/faq' ) ); ?>"><?php esc_html_e( 'FAQ', 'digicars' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/concierge' ) ); ?>"><?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?></a>
				</nav>
			</div>

			<div class="footer-col">
				<div class="footer-col__title"><?php esc_html_e( 'Contact', 'digicars' ); ?></div>
				<div class="footer-col__list">
					<a href="mailto:info@digicars.co.za">info@digicars.co.za</a>
					<a href="tel:0105951180">010 595 1180</a>
					<address style="font-style:normal;">
						<?php esc_html_e( '168 Grayston Drive, Sandown, Sandton', 'digicars' ); ?>
					</address>
					<div class="cluster" style="margin-top:var(--s-3);">
						<a class="icon-btn" href="https://facebook.com/DigiCarsSA" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Digicars on Facebook', 'digicars' ); ?>">
							<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
								<path d="M14 9h3l.5-3H14V4.5c0-.9.3-1.5 1.6-1.5H17V.3C16.7.3 15.6.2 14.4.2 11.9.2 10.2 1.7 10.2 4.4V6H7.3v3h2.9v8H14V9Z"></path>
							</svg>
						</a>
						<a class="icon-btn" href="https://instagram.com/digicarssa" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Digicars on Instagram', 'digicars' ); ?>">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<rect x="3" y="3" width="18" height="18" rx="5"></rect>
								<circle cx="12" cy="12" r="4"></circle>
								<line x1="17.5" y1="6.5" x2="17.5" y2="6.5"></line>
							</svg>
						</a>
						<a class="icon-btn" href="https://twitter.com/digicarsza" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Digicars on X', 'digicars' ); ?>">
							<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
								<path d="M17.5 3h3l-6.6 7.5L21.7 21h-6l-4.7-6.1L5.6 21H2.5l7-8L2 3h6.2l4.2 5.6L17.5 3Zm-1 16h1.7L7.6 4.7H5.8L16.5 19Z"></path>
							</svg>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="footer-bottom">
			<div>
				<?php
				printf(
					/* translators: %s: current year. */
					__( '&copy; %s Digi Cars Group. All rights reserved.', 'digicars' ),
					esc_html( gmdate( 'Y' ) )
				);
				?>
			</div>
			<nav class="cluster" aria-label="<?php esc_attr_e( 'Legal', 'digicars' ); ?>">
				<a href="<?php echo esc_url( home_url( '/terms' ) ); ?>"><?php esc_html_e( 'Terms', 'digicars' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/privacy' ) ); ?>"><?php esc_html_e( 'Privacy', 'digicars' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/paia' ) ); ?>"><?php esc_html_e( 'PAIA', 'digicars' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/consumer-protection' ) ); ?>"><?php esc_html_e( 'Consumer Protection Act', 'digicars' ); ?></a>
			</nav>
			<div class="cluster" aria-label="<?php esc_attr_e( 'Accepted payment and finance methods', 'digicars' ); ?>">
				<span class="badge badge--used">Visa</span>
				<span class="badge badge--used">Mastercard</span>
				<span class="badge badge--used">EFT</span>
				<span class="badge badge--used">PayFast</span>
			</div>
		</div>
	</div>

	<div class="footer-wordmark" aria-hidden="true">Digicars</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
