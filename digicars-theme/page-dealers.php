<?php
/**
 * Template Name: Find a Dealer
 *
 * Branch footprint. A grid of dealer cards for the Digicars Gauteng branches,
 * each with address, contact, hours, a Google Maps directions link and a
 * "browse stock" link filtered to that dealer where possible.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

get_header();

$digicars_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
if ( ! $digicars_shop_url ) {
	$digicars_shop_url = home_url( '/shop' );
}

/**
 * Branch footprint. `dealer` is the vehicle_dealer taxonomy slug used to
 * filter the shop; `maps` is a Google Maps directions query.
 */
$digicars_dealers = array(
	array(
		'name'    => __( 'Chery Sandton', 'digicars' ),
		'brand'   => __( 'Chery new & demo', 'digicars' ),
		'address' => '24 Rivonia Road, Sandhurst, Sandton, 2196',
		'phone'   => '010 595 1180',
		'tel'     => '0105951180',
		'email'   => 'sandton@digicars.co.za',
		'hours'   => __( 'Mon–Fri 08:00–17:30 · Sat 08:00–13:00', 'digicars' ),
		'dealer'  => 'chery-sandton',
		'maps'    => '24 Rivonia Road Sandhurst Sandton 2196',
	),
	array(
		'name'    => __( 'Chery Northcliff', 'digicars' ),
		'brand'   => __( 'Chery new & demo', 'digicars' ),
		'address' => '453 Beyers Naudé Drive, Northcliff, Randburg, 2195',
		'phone'   => '010 595 1184',
		'tel'     => '0105951184',
		'email'   => 'northcliff@digicars.co.za',
		'hours'   => __( 'Mon–Fri 08:00–17:30 · Sat 08:00–13:00', 'digicars' ),
		'dealer'  => 'chery-northcliff',
		'maps'    => '453 Beyers Naude Drive Northcliff Randburg 2195',
	),
	array(
		'name'    => __( 'Omoda & Jaecoo Sandton', 'digicars' ),
		'brand'   => __( 'Omoda & Jaecoo new & demo', 'digicars' ),
		'address' => '30 Rivonia Road, Sandhurst, Sandton, 2196',
		'phone'   => '010 595 1186',
		'tel'     => '0105951186',
		'email'   => 'omoda.sandton@digicars.co.za',
		'hours'   => __( 'Mon–Fri 08:00–17:30 · Sat 08:00–13:00', 'digicars' ),
		'dealer'  => 'omoda-jaecoo-sandton',
		'maps'    => '30 Rivonia Road Sandhurst Sandton 2196',
	),
	array(
		'name'    => __( 'Omoda & Jaecoo Melrose Arch', 'digicars' ),
		'brand'   => __( 'Omoda & Jaecoo new & demo', 'digicars' ),
		'address' => '1 Melrose Boulevard, Melrose Arch, Johannesburg, 2196',
		'phone'   => '010 595 1188',
		'tel'     => '0105951188',
		'email'   => 'melrosearch@digicars.co.za',
		'hours'   => __( 'Mon–Fri 08:00–17:30 · Sat 08:00–13:00', 'digicars' ),
		'dealer'  => 'omoda-jaecoo-melrose-arch',
		'maps'    => '1 Melrose Boulevard Melrose Arch Johannesburg 2196',
	),
	array(
		'name'    => __( 'Digicars Head Office, Sandown', 'digicars' ),
		'brand'   => __( 'Group head office · multi-brand pre-owned', 'digicars' ),
		'address' => '168 Grayston Drive, Sandown, Sandton, 2196',
		'phone'   => '010 595 1180',
		'tel'     => '0105951180',
		'email'   => 'info@digicars.co.za',
		'hours'   => __( 'Mon–Fri 08:00–17:00 · Sat by appointment', 'digicars' ),
		'dealer'  => '',
		'maps'    => '168 Grayston Drive Sandown Sandton 2196',
	),
);
?>

<?php /* 1. Hero ------------------------------------------------------------ */ ?>
<section class="section section--tight">
	<div class="container">
		<div class="section-head">
			<div class="section-head__copy stack">
				<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Find a dealer', 'digicars' ); ?></p>
				<h1 class="t-1"><?php esc_html_e( 'Find a Digicars dealer.', 'digicars' ); ?></h1>
				<p class="t-lead">
					<?php esc_html_e( 'Buy online and collect in person, or come see us first. Our Gauteng branches handle sales, finance, trade-ins and service. Pop in, or get directions and call ahead.', 'digicars' ); ?>
				</p>
			</div>
		</div>
	</div>
</section>

<?php /* 2. Dealer grid ---------------------------------------------------- */ ?>
<section class="section section--flush-top">
	<div class="container">
		<div class="grid dealer-card-grid" style="--cols:3;">
			<?php foreach ( $digicars_dealers as $digicars_dealer ) : ?>
				<?php
				$digicars_maps_url  = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $digicars_dealer['maps'] );
				$digicars_stock_url = '' !== $digicars_dealer['dealer']
					? add_query_arg( 'dealer', $digicars_dealer['dealer'], $digicars_shop_url )
					: $digicars_shop_url;
				?>
				<article class="dealer-card stack">
					<header class="dealer-card__head stack-sm">
						<h2 class="t-3 dealer-card__name"><?php echo esc_html( $digicars_dealer['name'] ); ?></h2>
						<p class="eyebrow dealer-card__brand"><?php echo esc_html( $digicars_dealer['brand'] ); ?></p>
					</header>

					<dl class="dealer-card__meta">
						<dt class="sr-only"><?php esc_html_e( 'Address', 'digicars' ); ?></dt>
						<dd>
							<address class="dealer-card__address"><?php echo esc_html( $digicars_dealer['address'] ); ?></address>
						</dd>
						<dt class="sr-only"><?php esc_html_e( 'Phone', 'digicars' ); ?></dt>
						<dd><a href="tel:<?php echo esc_attr( $digicars_dealer['tel'] ); ?>"><?php echo esc_html( $digicars_dealer['phone'] ); ?></a></dd>
						<dt class="sr-only"><?php esc_html_e( 'Email', 'digicars' ); ?></dt>
						<dd><a href="mailto:<?php echo esc_attr( $digicars_dealer['email'] ); ?>"><?php echo esc_html( $digicars_dealer['email'] ); ?></a></dd>
						<dt class="sr-only"><?php esc_html_e( 'Opening hours', 'digicars' ); ?></dt>
						<dd class="dealer-card__hours muted"><?php echo esc_html( $digicars_dealer['hours'] ); ?></dd>
					</dl>

					<div class="dealer-card__actions cluster">
						<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $digicars_maps_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Get directions', 'digicars' ); ?>
						</a>
						<a class="link-arrow dealer-card__stock" href="<?php echo esc_url( $digicars_stock_url ); ?>">
							<?php esc_html_e( 'Browse stock', 'digicars' ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<p class="muted" style="margin-top:var(--s-7);max-width:60ch;font-size:0.9rem;">
			<?php esc_html_e( 'All branches are in Gauteng. Buying from another province? We deliver nationwide — ask the Concierge or call 010 595 1180.', 'digicars' ); ?>
		</p>
	</div>
</section>

<?php
get_footer();
