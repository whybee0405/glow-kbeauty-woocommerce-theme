<?php
/**
 * Demo data importer — Appearance → Import Demo Data.
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_theme_page(
		'Import Demo Data',
		'Import Demo Data',
		'manage_options',
		'glow-import-demo',
		'glow_render_import_page'
	);
} );

function glow_render_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$product_count = (int) wp_count_posts( 'product' )->publish;
	$log           = '';
	$did_import    = false;
	$wc_active     = class_exists( 'WooCommerce' );

	if (
		isset( $_POST['glow_run_import'] ) &&
		check_admin_referer( 'glow_import_demo', 'glow_import_nonce' )
	) {
		if ( $wc_active ) {
			@set_time_limit( 0 );
			@ini_set( 'memory_limit', '512M' );
			ignore_user_abort( true );

			ob_start();
			require get_template_directory() . '/dummy-products.php';
			$log = trim( ob_get_clean() );

			$did_import    = true;
			$product_count = (int) wp_count_posts( 'product' )->publish;
		}
	}
	?>
	<div class="wrap">
		<h1>Import Demo Data</h1>

		<?php if ( $did_import ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><strong>Import complete — <?php echo esc_html( $product_count ); ?> products now in your store.</strong></p>
			<p>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>">View products</a>&nbsp;
				<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View site</a>
			</p>
		</div>
		<?php elseif ( ! $wc_active ) : ?>
		<div class="notice notice-error inline" style="margin-top:16px;">
			<p><strong>WooCommerce must be active before importing.</strong> Install and activate it, then return here.</p>
		</div>
		<?php endif; ?>

		<div class="card" style="max-width:620px;padding:24px 24px 16px;margin-top:16px;">
			<h2 style="margin-top:0;">Glow K-Beauty demo content</h2>
			<p>One click creates a fully populated store so you can see the theme working immediately.</p>
			<ul style="margin-left:1.25em;list-style:disc;">
				<li>20 products across 9 categories (Cleanse through SPF, plus Sheet Masks and Lips)</li>
				<li>Routine step metadata, skin type tags, and key actives on every product</li>
				<li>Product featured images imported from the theme\'s image folder</li>
				<li>2–3 verified-buyer reviews per product with computed star ratings</li>
			</ul>
			<p style="color:#646970;font-size:13px;">Safe to re-run — products are matched by SKU and updated, not duplicated.</p>

			<?php if ( $product_count > 0 ) : ?>
			<p style="color:#646970;font-size:13px;margin-top:0;">
				<strong><?php echo esc_html( $product_count ); ?> products</strong> already in your store.
			</p>
			<?php endif; ?>

			<?php if ( $wc_active ) : ?>
			<form method="post" style="margin-top:16px;">
				<?php wp_nonce_field( 'glow_import_demo', 'glow_import_nonce' ); ?>
				<button type="submit" name="glow_run_import" value="1" class="button button-primary button-hero">
					<?php echo $product_count > 0 ? 'Re-import demo data' : 'Import demo data'; ?>
				</button>
				<p class="description" style="margin-top:10px;">This may take 20–40 seconds while product images are downloaded. Don\'t close the tab.</p>
			</form>
			<?php endif; ?>
		</div>

		<?php if ( $did_import && $log !== '' ) : ?>
		<details style="margin-top:16px;max-width:620px;">
			<summary style="cursor:pointer;color:#646970;font-size:13px;">Show import log</summary>
			<pre style="background:#f6f7f7;border:1px solid #dcdcde;padding:12px;font-size:12px;overflow:auto;max-height:280px;margin-top:8px;border-radius:3px;"><?php echo esc_html( $log ); ?></pre>
		</details>
		<?php endif; ?>
	</div>
	<?php
}
