<?php
/**
 * Template Name: Compare
 *
 * Side-by-side vehicle comparison. Primary path is server-side rendering from
 * a `?ids=1,2,3` query string (the compare link in the header appends stored
 * ids); works without JS. main.js may hydrate from localStorage as a fallback.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Resolve requested vehicle IDs from the query string.
$digicars_ids = array();
if ( isset( $_GET['ids'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$digicars_raw = sanitize_text_field( wp_unslash( $_GET['ids'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	foreach ( explode( ',', $digicars_raw ) as $digicars_one ) {
		$digicars_one = absint( $digicars_one );
		if ( $digicars_one ) {
			$digicars_ids[] = $digicars_one;
		}
	}
	$digicars_ids = array_slice( array_unique( $digicars_ids ), 0, 4 );
}

// Load valid products.
$digicars_vehicles = array();
if ( function_exists( 'wc_get_product' ) ) {
	foreach ( $digicars_ids as $digicars_id ) {
		$digicars_product = wc_get_product( $digicars_id );
		if ( $digicars_product ) {
			$digicars_vehicles[] = $digicars_product;
		}
	}
}

/**
 * Spec rows: label => callback returning a display string for a product.
 * A row renders only if at least one vehicle returns a non-empty value.
 */
$digicars_money = static function ( $value ) {
	return $value ? 'R ' . number_format( (float) $value, 0, '.', ' ' ) : '';
};
$digicars_km = static function ( $value ) {
	return ( (int) $value > 0 ) ? number_format( (int) $value, 0, '.', ' ' ) . ' km' : '';
};
$digicars_rows = array(
	__( 'Price', 'digicars' )         => static function ( $p ) use ( $digicars_money ) {
		return $digicars_money( digicars_meta( $p->get_id(), '_vehicle_price' ) ?: $p->get_price() );
	},
	__( 'From / month', 'digicars' )  => static function ( $p ) {
		$m = digicars_meta( $p->get_id(), '_vehicle_monthly_from' );
		if ( ! $m && function_exists( 'digicars_monthly_from' ) ) {
			$m = digicars_monthly_from( (float) ( digicars_meta( $p->get_id(), '_vehicle_price' ) ?: $p->get_price() ) );
		}
		return $m ? 'From R ' . number_format( (int) $m, 0, '.', ' ' ) . ' pm*' : '';
	},
	__( 'Year', 'digicars' )          => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_year' ) );
	},
	__( 'Mileage', 'digicars' )       => static function ( $p ) use ( $digicars_km ) {
		return $digicars_km( digicars_meta( $p->get_id(), '_vehicle_mileage' ) );
	},
	__( 'Transmission', 'digicars' )  => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_transmission' ) );
	},
	__( 'Drivetrain', 'digicars' )    => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_drivetrain' ) );
	},
	__( 'Fuel', 'digicars' )          => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_fuel' ) );
	},
	__( 'Engine', 'digicars' )        => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_engine' ) );
	},
	__( 'Power', 'digicars' )         => static function ( $p ) {
		$kw = digicars_meta( $p->get_id(), '_vehicle_power_kw' );
		return $kw ? esc_html( $kw ) . ' kW' : '';
	},
	__( 'Fuel economy', 'digicars' )  => static function ( $p ) {
		$e = digicars_meta( $p->get_id(), '_vehicle_fuel_economy' );
		return $e ? esc_html( $e ) . ' L/100km' : '';
	},
	__( 'Range', 'digicars' )         => static function ( $p ) use ( $digicars_km ) {
		return $digicars_km( digicars_meta( $p->get_id(), '_vehicle_range_km' ) );
	},
	__( 'Seats', 'digicars' )         => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_seats' ) );
	},
	__( 'Doors', 'digicars' )         => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_doors' ) );
	},
	__( 'Body type', 'digicars' )     => static function ( $p ) {
		return esc_html( ucwords( str_replace( '-', ' ', (string) digicars_meta( $p->get_id(), '_vehicle_body_type' ) ) ) );
	},
	__( 'Colour', 'digicars' )        => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_colour' ) );
	},
	__( 'Safety rating', 'digicars' ) => static function ( $p ) {
		$s = digicars_meta( $p->get_id(), '_vehicle_safety_rating' );
		return $s ? esc_html( $s ) . '/5 NCAP' : '';
	},
	__( 'Service plan', 'digicars' )  => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_service_plan' ) );
	},
	__( 'Warranty', 'digicars' )      => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_warranty' ) );
	},
	__( 'Condition', 'digicars' )     => static function ( $p ) {
		return esc_html( ucfirst( (string) digicars_meta( $p->get_id(), '_vehicle_condition' ) ) );
	},
	__( 'Dealer', 'digicars' )        => static function ( $p ) {
		return esc_html( digicars_meta( $p->get_id(), '_vehicle_dealer' ) );
	},
);

$digicars_title = static function ( $p ) {
	$bits = array(
		digicars_meta( $p->get_id(), '_vehicle_year' ),
		digicars_meta( $p->get_id(), '_vehicle_make' ),
		digicars_meta( $p->get_id(), '_vehicle_model' ),
		digicars_meta( $p->get_id(), '_vehicle_variant' ),
	);
	$bits = array_filter( array_map( 'trim', array_map( 'strval', $bits ) ) );
	return $bits ? implode( ' ', $bits ) : $p->get_name();
};

$digicars_shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );
?>

<main id="main" class="site-main">
	<section class="section" data-compare-page>
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Compare', 'digicars' ); ?></p>
			<h1 class="t-1"><?php esc_html_e( 'Compare vehicles side by side.', 'digicars' ); ?></h1>

			<?php if ( count( $digicars_vehicles ) >= 2 ) : ?>
				<div class="compare-scroll">
					<table class="compare-table">
						<thead>
							<tr>
								<th scope="col" class="compare-table__rowhead"><span class="sr-only"><?php esc_html_e( 'Attribute', 'digicars' ); ?></span></th>
								<?php foreach ( $digicars_vehicles as $digicars_v ) : ?>
									<th scope="col" class="compare-table__vehicle">
										<a href="<?php echo esc_url( get_permalink( $digicars_v->get_id() ) ); ?>" class="compare-table__media">
											<?php echo wp_kses_post( $digicars_v->get_image( 'digicars-card' ) ); ?>
										</a>
										<span class="compare-table__name"><?php echo esc_html( $digicars_title( $digicars_v ) ); ?></span>
										<span class="cluster cluster--tight">
											<button type="button" class="btn btn--signal btn--sm" data-enquire data-vehicle-id="<?php echo esc_attr( $digicars_v->get_id() ); ?>" data-vehicle-name="<?php echo esc_attr( $digicars_title( $digicars_v ) ); ?>"><?php esc_html_e( 'Enquire', 'digicars' ); ?></button>
											<button type="button" class="btn btn--ghost btn--sm" data-compare-toggle data-vehicle-id="<?php echo esc_attr( $digicars_v->get_id() ); ?>" aria-pressed="true"><?php esc_html_e( 'Remove', 'digicars' ); ?></button>
										</span>
									</th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $digicars_rows as $digicars_label => $digicars_cb ) :
								$digicars_values = array();
								$digicars_has    = false;
								foreach ( $digicars_vehicles as $digicars_v ) {
									$digicars_val      = (string) $digicars_cb( $digicars_v );
									$digicars_values[] = $digicars_val;
									if ( '' !== $digicars_val ) {
										$digicars_has = true;
									}
								}
								if ( ! $digicars_has ) {
									continue;
								}
								?>
								<tr>
									<th scope="row" class="compare-table__rowhead"><?php echo esc_html( $digicars_label ); ?></th>
									<?php foreach ( $digicars_values as $digicars_val ) : ?>
										<td class="t-mono"><?php echo '' !== $digicars_val ? wp_kses_post( $digicars_val ) : '<span class="muted">&mdash;</span>'; ?></td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else : ?>
				<div class="catalogue__empty stack">
					<p class="t-lead"><?php esc_html_e( 'Add vehicles to compare from any listing, then they line up here side by side.', 'digicars' ); ?></p>
					<div class="cluster">
						<a class="btn btn--signal" href="<?php echo esc_url( $digicars_shop ); ?>"><?php esc_html_e( 'Browse stock', 'digicars' ); ?></a>
						<button type="button" class="btn btn--outline" data-concierge-open><?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?></button>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
