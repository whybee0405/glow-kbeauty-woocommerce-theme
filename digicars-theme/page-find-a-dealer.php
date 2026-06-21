<?php
/**
 * Slug-based template for /find-a-dealer.
 *
 * WordPress auto-matches this file to any page with the slug "find-a-dealer"
 * without requiring manual template selection in wp-admin. It includes the
 * same dealer data as page-dealers.php so the URL /find-a-dealer always works.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Delegate entirely to the Find a Dealer template logic.
// Load page-dealers.php from the theme directory.
$digicars_dealer_template = get_theme_file_path( 'page-dealers.php' );
if ( file_exists( $digicars_dealer_template ) ) {
	include $digicars_dealer_template;
} else {
	get_header();
	?>
	<section class="section">
		<div class="container container--narrow">
			<div class="stack">
				<h1 class="t-1"><?php esc_html_e( 'Find a Dealer', 'digicars' ); ?></h1>
				<p class="t-lead">
					<?php
					printf(
						/* translators: %s: phone number link */
						esc_html__( 'Call us on %s to find your nearest Digicars branch.', 'digicars' ),
						'<a href="tel:0105951180">010 595 1180</a>'
					);
					?>
				</p>
			</div>
		</div>
	</section>
	<?php
	get_footer();
}
