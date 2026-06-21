<?php
/**
 * Demo data importer — Appearance → Import Demo Data.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_theme_page(
		'Import Demo Data',
		'Import Demo Data',
		'manage_options',
		'digicars-import-demo',
		'digicars_render_import_page'
	);
} );

/**
 * Create the required site pages if they don't already exist.
 * Idempotent: skips any page whose slug already resolves.
 */
function digicars_create_demo_pages(): void {
	$pages = array(
		array(
			'slug'    => 'finance',
			'title'   => 'Vehicle Finance',
			'excerpt' => 'Get pre-approved finance from major South African banks online.',
		),
		array(
			'slug'    => 'sell',
			'title'   => 'Trade-in or Sell Your Car',
			'excerpt' => 'Get an instant online valuation and sell your car to Digicars.',
		),
		array(
			'slug'    => 'book-a-service',
			'title'   => 'Book a Service',
			'excerpt' => 'Book your next service at a Digicars partner workshop online.',
		),
		array(
			'slug'    => 'about',
			'title'   => 'About Digicars',
			'excerpt' => 'Digital-first automotive marketplace built for South Africa.',
		),
		array(
			'slug'    => 'compare',
			'title'   => 'Compare Vehicles',
			'excerpt' => 'Side-by-side comparison of vehicles in your Digicars shortlist.',
		),
	);

	foreach ( $pages as $page ) {
		$existing = get_page_by_path( $page['slug'], OBJECT, 'page' );
		if ( $existing instanceof WP_Post ) {
			continue;
		}
		wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $page['title'],
			'post_name'    => $page['slug'],
			'post_excerpt' => $page['excerpt'],
			'post_content' => '',
		) );
	}
}

function digicars_render_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$vehicle_count = (int) wp_count_posts( 'product' )->publish;
	$post_count    = (int) wp_count_posts( 'post' )->publish;
	$log           = '';
	$did_import    = false;
	$wc_active     = class_exists( 'WooCommerce' );

	if (
		isset( $_POST['digicars_run_import'] ) &&
		check_admin_referer( 'digicars_import_demo', 'digicars_import_nonce' )
	) {
		if ( $wc_active ) {
			@set_time_limit( 0 );
			@ini_set( 'memory_limit', '512M' );
			ignore_user_abort( true );

			digicars_create_demo_pages();

			ob_start();
			require get_template_directory() . '/dummy-products.php';
			require get_template_directory() . '/dummy-posts.php';
			$log = trim( ob_get_clean() );

			$did_import    = true;
			$vehicle_count = (int) wp_count_posts( 'product' )->publish;
			$post_count    = (int) wp_count_posts( 'post' )->publish;
		}
	}
	?>
	<div class="wrap">
		<h1>Import Demo Data</h1>

		<?php if ( $did_import ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><strong>Import complete — <?php echo esc_html( $vehicle_count ); ?> vehicles, <?php echo esc_html( $post_count ); ?> blog posts, and all nav pages created.</strong></p>
			<p>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>">View vehicles</a>&nbsp;
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">View posts</a>&nbsp;
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">View pages</a>&nbsp;
				<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View site</a>
			</p>
		</div>
		<?php elseif ( ! $wc_active ) : ?>
		<div class="notice notice-error inline" style="margin-top:16px;">
			<p><strong>WooCommerce must be active before importing.</strong> Install and activate it, then return here.</p>
		</div>
		<?php endif; ?>

		<div class="card" style="max-width:640px;padding:24px 24px 16px;margin-top:16px;">
			<h2 style="margin-top:0;">Digicars demo content</h2>
			<p>One click creates a fully populated catalogue and blog, and creates all the site pages linked in the navigation.</p>

			<h3 style="margin-bottom:6px;">Vehicles (23)</h3>
			<ul style="margin-left:1.25em;list-style:disc;margin-top:0;">
				<li>Sedans, hatchbacks, SUVs, bakkies, an EV — across all price bands</li>
				<li>Makes: VW, Ford, Suzuki, GWM, Chery, Omoda, Jaecoo, Mahindra and more</li>
				<li>Full specs per vehicle: year, mileage, fuel, transmission, colour, province</li>
				<li>AI summary, lifestyle tags, and 2&ndash;3 customer reviews per vehicle</li>
			</ul>

			<h3 style="margin-bottom:6px;">Car Torque blog posts (4)</h3>
			<ul style="margin-left:1.25em;list-style:disc;margin-top:0;">
				<li>Buying advice, EV round-ups, and road-test write-ups</li>
				<li>Tagged and categorised for the native WordPress blog</li>
			</ul>

			<p style="color:#646970;font-size:13px;margin-top:8px;">Safe to re-run — vehicles are matched by stock number, posts by slug. Neither will be duplicated.</p>

			<?php if ( $vehicle_count > 0 || $post_count > 0 ) : ?>
			<p style="color:#646970;font-size:13px;margin-top:0;">
				Currently in your site: <strong><?php echo esc_html( $vehicle_count ); ?> vehicles</strong> and <strong><?php echo esc_html( $post_count ); ?> posts</strong>.
			</p>
			<?php endif; ?>

			<?php if ( $wc_active ) : ?>
			<form method="post" style="margin-top:16px;">
				<?php wp_nonce_field( 'digicars_import_demo', 'digicars_import_nonce' ); ?>
				<button type="submit" name="digicars_run_import" value="1" class="button button-primary button-hero">
					<?php echo ( $vehicle_count > 0 || $post_count > 0 ) ? 'Re-import demo data' : 'Import demo data'; ?>
				</button>
				<p class="description" style="margin-top:10px;">May take 30&ndash;60 seconds while vehicle data is created. Do not close the tab.</p>
			</form>
			<?php endif; ?>
		</div>

		<?php if ( $did_import && $log !== '' ) : ?>
		<details style="margin-top:16px;max-width:640px;">
			<summary style="cursor:pointer;color:#646970;font-size:13px;">Show import log</summary>
			<pre style="background:#f6f7f7;border:1px solid #dcdcde;padding:12px;font-size:12px;overflow:auto;max-height:280px;margin-top:8px;border-radius:3px;"><?php echo esc_html( $log ); ?></pre>
		</details>
		<?php endif; ?>
	</div>
	<?php
}
