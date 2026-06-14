<?php
/**
 * Glow K-Beauty setup page — lets site admins import demo products,
 * categories, skin types, concerns and reviews without WP-CLI.
 *
 * Appearance → Glow Setup
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

function glow_setup_admin_menu() {
	add_theme_page(
		__( 'Glow Setup', 'glow-kbeauty' ),
		__( 'Glow Setup', 'glow-kbeauty' ),
		'manage_options',
		'glow-setup',
		'glow_setup_page'
	);
}
add_action( 'admin_menu', 'glow_setup_admin_menu' );

function glow_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$message = '';
	$type    = 'info';

	if (
		isset( $_POST['glow_import_nonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['glow_import_nonce'] ) ), 'glow_import_demo' )
	) {
		if ( ! glow_wc_active() ) {
			$message = __( 'WooCommerce must be installed and active before importing demo products.', 'glow-kbeauty' );
			$type    = 'error';
		} else {
			ob_start();
			require get_template_directory() . '/dummy-products.php';
			$log     = trim( ob_get_clean() );
			$message = __( 'Demo data imported. 20 products, categories, skin types, concerns and reviews are ready.', 'glow-kbeauty' );
			$type    = 'success';
		}
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Glow K-Beauty — Setup', 'glow-kbeauty' ); ?></h1>

		<?php if ( $message ) : ?>
			<div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
		<?php endif; ?>

		<div style="max-width:680px;">

			<h2><?php esc_html_e( 'Demo Products', 'glow-kbeauty' ); ?></h2>
			<p><?php esc_html_e( 'Imports 20 K-beauty products with full metadata, categories, skin types, concerns and verified reviews. The import is idempotent — running it again updates existing products rather than duplicating them.', 'glow-kbeauty' ); ?></p>

			<?php if ( ! glow_wc_active() ) : ?>
				<p style="color:#b32d2e;">
					<?php esc_html_e( '⚠ WooCommerce is not active. Install and activate WooCommerce first, then come back here.', 'glow-kbeauty' ); ?>
				</p>
			<?php else : ?>
				<form method="post">
					<?php wp_nonce_field( 'glow_import_demo', 'glow_import_nonce' ); ?>
					<?php submit_button( __( 'Import Demo Products', 'glow-kbeauty' ), 'primary', 'submit', false ); ?>
				</form>
			<?php endif; ?>

			<hr style="margin:2em 0;" />

			<h2><?php esc_html_e( 'Quick-start Checklist', 'glow-kbeauty' ); ?></h2>
			<ol style="line-height:1.9;">
				<li><?php esc_html_e( 'Activate WooCommerce and run the setup wizard (skip the payment step for now).', 'glow-kbeauty' ); ?></li>
				<li><?php esc_html_e( 'Click Import Demo Products above.', 'glow-kbeauty' ); ?></li>
				<li>
					<?php
					printf(
						/* translators: %s: Settings → Reading link. */
						esc_html__( 'Create a blank page called "Home", then go to %s and set it as the static front page.', 'glow-kbeauty' ),
						'<a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '">' . esc_html__( 'Settings → Reading', 'glow-kbeauty' ) . '</a>'
					);
					?>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: Pages link. */
						esc_html__( 'Create three pages in %s and assign their templates under Page Attributes → Template:', 'glow-kbeauty' ),
						'<a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '">' . esc_html__( 'Pages', 'glow-kbeauty' ) . '</a>'
					);
					?>
					<ul style="margin:.5em 0 .5em 1.5em;list-style:disc;">
						<li><strong>About</strong> → <?php esc_html_e( 'template: About', 'glow-kbeauty' ); ?></li>
						<li><strong>Contact</strong> → <?php esc_html_e( 'template: Contact', 'glow-kbeauty' ); ?></li>
						<li><strong>Help</strong> → <?php esc_html_e( 'template: Help & FAQ', 'glow-kbeauty' ); ?></li>
					</ul>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: Permalinks link. */
						esc_html__( 'Go to %s and click Save Changes to flush rewrite rules.', 'glow-kbeauty' ),
						'<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">' . esc_html__( 'Settings → Permalinks', 'glow-kbeauty' ) . '</a>'
					);
					?>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: Customizer link. */
						esc_html__( 'Optional: visit %s → Glow K-Beauty to update your notice bar, contact details and branding.', 'glow-kbeauty' ),
						'<a href="' . esc_url( admin_url( 'customize.php' ) ) . '">' . esc_html__( 'Appearance → Customize', 'glow-kbeauty' ) . '</a>'
					);
					?>
				</li>
			</ol>

		</div>
	</div>
	<?php
}
