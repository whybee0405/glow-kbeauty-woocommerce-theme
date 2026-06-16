<?php
/**
 * Site footer: brand, link columns, payment badges, oversized wordmark.
 *
 * @package Glow_KBeauty
 */
?>

<footer class="site-footer">
	<div class="container">

		<div class="footer-grid">
			<div class="footer-brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( 'Glow — home', 'glow-kbeauty' ); ?>">
					<?php glow_inline_logo(); ?>
				</a>
				<p><?php esc_html_e( 'Authentic Korean skincare, organised by the routine that makes it work. Sourced in Seoul, verified batch by batch, shipped from Johannesburg.', 'glow-kbeauty' ); ?></p>
			</div>

			<?php foreach ( glow_footer_columns() as $heading => $links ) : ?>
				<div class="footer-col">
					<h4><?php echo esc_html( $heading ); ?></h4>
					<ul>
						<?php foreach ( $links as $link ) : ?>
							<li><a href="<?php echo esc_url( $link[1] ); ?>"><?php echo esc_html( $link[0] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <?php esc_html_e( 'Johannesburg, South Africa', 'glow-kbeauty' ); ?></p>
			<div class="payment-badges" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'glow-kbeauty' ); ?>">
				<span>PayFast</span>
				<span>Visa</span>
				<span>Mastercard</span>
				<span>EFT</span>
			</div>
		</div>

	</div>

	<p class="footer-wordmark" aria-hidden="true">Layer by layer.</p>
</footer>

<div class="toast" role="status" aria-live="polite" data-toast>
	<span class="toast-dot" aria-hidden="true"></span>
	<span data-toast-message></span>
</div>

 <script src="https://helix.cloudia.co.za/v1/widget/embed.js?key=6428bd19-fb03-468d-8352-0f26eda2a0ff"></script>

<?php wp_footer(); ?>
</body>
</html>
