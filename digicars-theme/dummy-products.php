<?php
/**
 * Digicars dummy vehicle importer.
 *
 * Idempotent WP-CLI seeder for the Digicars catalogue. Run with:
 *
 *   wp eval-file wp-content/themes/digicars-theme/dummy-products.php
 *
 * Each vehicle is keyed on its unique `_vehicle_stock_no`. On re-run an
 * existing product with that stock number is UPDATED rather than duplicated,
 * so the importer is safe to run repeatedly. All `_vehicle_*` meta keys map
 * exactly onto digicars_meta_definitions() in functions.php. Taxonomy terms
 * (product_cat = body type, vehicle_make, vehicle_condition, vehicle_fuel,
 * vehicle_dealer) are created on demand. Thumbnail sideloading and review
 * seeding are best-effort and never abort the import.
 *
 * @package Digicars
 */

/* -------------------------------------------------------------------------
 * Environment guard — only run under WP-CLI / a real WP+WC bootstrap.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'wp_insert_post' ) || ! function_exists( 'wc_get_product' ) ) {
	$digicars_notice = "Digicars importer: WordPress + WooCommerce not loaded. Run via: wp eval-file wp-content/themes/digicars-theme/dummy-products.php\n";
	if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( '\\WP_CLI' ) ) {
		\WP_CLI::warning( $digicars_notice );
	} else {
		echo $digicars_notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

/* -------------------------------------------------------------------------
 * Small CLI logging helper.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'digicars_seed_log' ) ) {
	/**
	 * Print a progress line to the CLI (or stdout as a fallback).
	 *
	 * @param string $message Line to print.
	 * @return void
	 */
	function digicars_seed_log( string $message ): void {
		if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( '\\WP_CLI' ) ) {
			\WP_CLI::log( $message );
		} else {
			echo $message . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/* -------------------------------------------------------------------------
 * Term resolver — create taxonomy terms on demand, return the term_id.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'digicars_seed_term' ) ) {
	/**
	 * Ensure a term exists in a taxonomy and return its ID.
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @param string $name     Human label.
	 * @param string $slug     Desired slug (optional).
	 * @return int Term ID, or 0 on failure.
	 */
	function digicars_seed_term( string $taxonomy, string $name, string $slug = '' ): int {
		if ( '' === $slug ) {
			$slug = sanitize_title( $name );
		}

		$existing = get_term_by( 'slug', $slug, $taxonomy );
		if ( $existing instanceof WP_Term ) {
			return (int) $existing->term_id;
		}

		$existing = get_term_by( 'name', $name, $taxonomy );
		if ( $existing instanceof WP_Term ) {
			return (int) $existing->term_id;
		}

		$result = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
		if ( is_wp_error( $result ) ) {
			// Race / already-exists: try once more by slug.
			$retry = get_term_by( 'slug', $slug, $taxonomy );
			return $retry instanceof WP_Term ? (int) $retry->term_id : 0;
		}

		return (int) $result['term_id'];
	}
}

/* -------------------------------------------------------------------------
 * The catalogue. 23 vehicles, full attribute schema.
 *
 * Notes on fields:
 *   - `body`       => product_cat body-type slug (digicars_body_types()).
 *   - `make_slug`  => vehicle_make slug (digicars_makes()).
 *   - `condition`  => new|demo|used.
 *   - `fuel`       => petrol|diesel|electric|hybrid.
 *   - EVs carry range_km + battery_kwh and OMIT fuel_economy/co2.
 *   - `featured`   => homepage "best sellers".
 *   - `rating` / `reviews` => synthetic social proof (reviews optional).
 * ---------------------------------------------------------------------- */

$digicars_vehicles = array(

	/* 1. New family SUV — Chery Tiggo 7 Pro (petrol). FEATURED. */
	array(
		'stock_no'        => 'DC-2026-0001',
		'vin'             => 'LVTDB21B8RD000101',
		'make'            => 'Chery',
		'make_slug'       => 'chery',
		'model'           => 'Tiggo 7 Pro',
		'variant'         => '1.5T Elite DCT',
		'year'            => 2026,
		'body'            => 'suv',
		'condition'       => 'new',
		'price'           => 469900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'DSG',
		'drivetrain'      => 'FWD',
		'engine'          => '1.5L Turbo Petrol',
		'power_kw'        => 108,
		'fuel_economy'    => 7.4,
		'co2'             => 172,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '5yr/60 000km service plan',
		'warranty'        => '5yr/150 000km warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 626,
		'towing_capacity' => 1500,
		'colour'          => 'Phantom Grey',
		'safety_rating'   => 5,
		'features'        => array( '10.25-inch touchscreen', 'Wireless Apple CarPlay', '360-degree camera', 'Panoramic sunroof', 'Adaptive cruise control', 'Lane-keep assist', 'Heated front seats', 'Six airbags' ),
		'tags'            => array( 'family', 'commuter' ),
		'dealer'          => 'Digicars Sandton',
		'province'        => 'Gauteng',
		'featured'        => true,
		'rating'          => 4.7,
		'reviews'         => array(
			array( 'author' => 'Thabo M.', 'email' => 'thabo.m@example.co.za', 'rating' => 5, 'text' => 'Spec for the money is unreal. The Tiggo feels a class above its price and the sunroof sold my wife instantly.' ),
			array( 'author' => 'Renske V.', 'email' => 'renske.v@example.co.za', 'rating' => 4, 'text' => 'Comfortable and quiet on the highway. The DCT can be a touch hesitant in traffic but otherwise a great family buy.' ),
		),
	),

	/* 2. New compact SUV — Omoda C5 (petrol). FEATURED. */
	array(
		'stock_no'        => 'DC-2026-0002',
		'vin'             => 'LVTDB21B8RD000202',
		'make'            => 'Omoda',
		'make_slug'       => 'omoda',
		'model'           => 'C5',
		'variant'         => '1.5T Elegance',
		'year'            => 2026,
		'body'            => 'suv',
		'condition'       => 'new',
		'price'           => 429900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'CVT',
		'drivetrain'      => 'FWD',
		'engine'          => '1.5L Turbo Petrol',
		'power_kw'        => 115,
		'fuel_economy'    => 6.9,
		'co2'             => 161,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '5yr/70 000km service plan',
		'warranty'        => '5yr/150 000km warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 378,
		'towing_capacity' => 1300,
		'colour'          => 'Vinland Green',
		'safety_rating'   => 5,
		'features'        => array( 'Dual 10.25-inch displays', 'Wireless charging pad', 'Sony sound system', 'Blind-spot monitoring', 'Rear cross-traffic alert', 'Keyless entry', 'LED matrix headlights' ),
		'tags'            => array( 'commuter', 'first-car' ),
		'dealer'          => 'Digicars Melrose Arch',
		'province'        => 'Gauteng',
		'featured'        => true,
		'rating'          => 4.5,
		'reviews'         => array(
			array( 'author' => 'Lerato K.', 'email' => 'lerato.k@example.co.za', 'rating' => 5, 'text' => 'Genuinely a head-turner. People assume it cost double. Cabin tech is brilliant.' ),
			array( 'author' => 'Dewald P.', 'email' => 'dewald.p@example.co.za', 'rating' => 4, 'text' => 'Love the styling and kit. Boot is a little small for a big family but perfect for two.' ),
		),
	),

	/* 3. New premium SUV — Jaecoo J7 (petrol AWD). FEATURED. */
	array(
		'stock_no'        => 'DC-2026-0003',
		'vin'             => 'LVTDB21B8RD000303',
		'make'            => 'Jaecoo',
		'make_slug'       => 'jaecoo',
		'model'           => 'J7',
		'variant'         => '1.6T AWD Inferno',
		'year'            => 2026,
		'body'            => 'suv',
		'condition'       => 'new',
		'price'           => 599900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'DSG',
		'drivetrain'      => 'AWD',
		'engine'          => '1.6L Turbo Petrol',
		'power_kw'        => 145,
		'fuel_economy'    => 8.1,
		'co2'             => 189,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '5yr/75 000km service plan',
		'warranty'        => '5yr/150 000km warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 500,
		'towing_capacity' => 2000,
		'colour'          => 'Mineral White',
		'safety_rating'   => 5,
		'features'        => array( '50W wireless charging', '14.8-inch portrait screen', 'All-wheel drive with terrain modes', 'Power tailgate', 'Ventilated leather seats', 'Head-up display', 'Sony 8-speaker audio', '540-degree camera' ),
		'tags'            => array( 'family', 'luxury', 'off-road' ),
		'dealer'          => 'Digicars Sandton',
		'province'        => 'Gauteng',
		'featured'        => true,
		'rating'          => 4.8,
		'reviews'         => array(
			array( 'author' => 'Sipho D.', 'email' => 'sipho.d@example.co.za', 'rating' => 5, 'text' => 'The interior genuinely embarrasses German rivals at the price. AWD gives real confidence on gravel to our farm.' ),
		),
	),

	/* 4. New hatch — VW Polo (petrol). */
	array(
		'stock_no'        => 'DC-2026-0004',
		'vin'             => 'WVWZZZAWZRY000404',
		'make'            => 'Volkswagen',
		'make_slug'       => 'volkswagen',
		'model'           => 'Polo',
		'variant'         => '1.0 TSI Life',
		'year'            => 2025,
		'body'            => 'hatch',
		'condition'       => 'new',
		'price'           => 379900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Manual',
		'drivetrain'      => 'FWD',
		'engine'          => '1.0L TSI Petrol',
		'power_kw'        => 70,
		'fuel_economy'    => 5.3,
		'co2'             => 121,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '3yr/45 000km service plan',
		'warranty'        => '3yr/120 000km warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 351,
		'towing_capacity' => 1000,
		'colour'          => 'Reflex Silver',
		'safety_rating'   => 5,
		'features'        => array( 'App-Connect smartphone integration', 'Digital Cockpit', 'Cruise control', 'Front Assist', 'Rear park distance control', 'Multifunction steering wheel' ),
		'tags'            => array( 'first-car', 'commuter' ),
		'dealer'          => 'Digicars Northcliff',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.6,
		'reviews'         => array(
			array( 'author' => 'Ayesha B.', 'email' => 'ayesha.b@example.co.za', 'rating' => 5, 'text' => 'My first new car and I could not be happier. Frugal, solid and easy to park everywhere.' ),
		),
	),

	/* 5. New EV hatch — GWM Ora 03 (electric). FEATURED. */
	array(
		'stock_no'        => 'DC-2026-0005',
		'vin'             => 'LGWEF6A50RX000505',
		'make'            => 'GWM',
		'make_slug'       => 'gwm',
		'model'           => 'Ora 03',
		'variant'         => '400 Ultra Luxury',
		'year'            => 2026,
		'body'            => 'hatch',
		'condition'       => 'new',
		'price'           => 686950,
		'availability'    => 'In stock',
		'fuel'            => 'electric',
		'transmission'    => 'Automatic',
		'drivetrain'      => 'FWD',
		'engine'          => 'Single electric motor',
		'power_kw'        => 126,
		'range_km'        => 420,
		'battery_kwh'     => 63.0,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '5yr/60 000km service plan',
		'warranty'        => '8yr/150 000km battery warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 228,
		'towing_capacity' => 0,
		'colour'          => 'Mint Green',
		'safety_rating'   => 5,
		'features'        => array( 'Retro design with massage seats', 'Dual 10.25-inch displays', 'Vehicle-to-load (V2L)', 'Wireless charging', 'Facial recognition start', 'Level 2 driver assistance', 'Heated and ventilated seats' ),
		'tags'            => array( 'eco', 'commuter', 'luxury' ),
		'dealer'          => 'Digicars Melrose Arch',
		'province'        => 'Gauteng',
		'featured'        => true,
		'rating'          => 4.4,
		'reviews'         => array(
			array( 'author' => 'Nadia F.', 'email' => 'nadia.f@example.co.za', 'rating' => 5, 'text' => 'Cheapest school run I have ever done. Charges overnight at home and the cabin feels like a lounge.' ),
			array( 'author' => 'Kabelo S.', 'email' => 'kabelo.s@example.co.za', 'rating' => 4, 'text' => 'Range is honest at around 400km. Just plan longer trips around charging and it is superb.' ),
		),
	),

	/* 6. New sedan — Suzuki Ciaz (petrol). */
	array(
		'stock_no'        => 'DC-2026-0006',
		'vin'             => 'MA3FCEB1S00000606',
		'make'            => 'Suzuki',
		'make_slug'       => 'suzuki',
		'model'           => 'Ciaz',
		'variant'         => '1.5 GLX Auto',
		'year'            => 2025,
		'body'            => 'sedan',
		'condition'       => 'new',
		'price'           => 309900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Automatic',
		'drivetrain'      => 'FWD',
		'engine'          => '1.5L Petrol',
		'power_kw'        => 77,
		'fuel_economy'    => 5.8,
		'co2'             => 135,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '4yr/60 000km service plan',
		'warranty'        => '5yr/200 000km warranty',
		'doors'           => 4,
		'seats'           => 5,
		'boot_litres'     => 510,
		'towing_capacity' => 1000,
		'colour'          => 'Pearl Arctic White',
		'safety_rating'   => 4,
		'features'        => array( 'Touchscreen infotainment', 'Cruise control', 'Reverse camera', 'Automatic climate control', 'Keyless push start', 'Dual front airbags' ),
		'tags'            => array( 'family', 'commuter', 'fleet' ),
		'dealer'          => 'Digicars Northcliff',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.3,
		'reviews'         => array(),
	),

	/* 7. New double-cab bakkie — Ford Ranger (diesel 4x4). FEATURED. */
	array(
		'stock_no'        => 'DC-2026-0007',
		'vin'             => 'AFAPXXMJ2PRD00707',
		'make'            => 'Ford',
		'make_slug'       => 'ford',
		'model'           => 'Ranger',
		'variant'         => '2.0 BiTurbo Wildtrak 4x4',
		'year'            => 2026,
		'body'            => 'double-cab',
		'condition'       => 'new',
		'price'           => 879900,
		'availability'    => 'In stock',
		'fuel'            => 'diesel',
		'transmission'    => 'Automatic',
		'drivetrain'      => '4x4',
		'engine'          => '2.0L BiTurbo Diesel',
		'power_kw'        => 154,
		'fuel_economy'    => 7.6,
		'co2'             => 199,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '6yr/90 000km service plan',
		'warranty'        => '4yr/120 000km warranty',
		'doors'           => 4,
		'seats'           => 5,
		'boot_litres'     => 0,
		'towing_capacity' => 3500,
		'colour'          => 'Sedona Orange',
		'safety_rating'   => 5,
		'features'        => array( '12-inch portrait touchscreen', 'Electronic locking rear diff', 'Selectable drive modes', '360-degree camera', 'Adaptive cruise control', 'Tow bar with trailer sway control', 'Matrix LED headlights', 'Heated leather seats' ),
		'tags'            => array( 'off-road', 'fleet', 'family' ),
		'dealer'          => 'Digicars Sandton',
		'province'        => 'Gauteng',
		'featured'        => true,
		'rating'          => 4.9,
		'reviews'         => array(
			array( 'author' => 'Johan vR.', 'email' => 'johan.vr@example.co.za', 'rating' => 5, 'text' => 'Tows the boat without breaking a sweat and still comfortable enough for the daily commute. Faultless so far.' ),
			array( 'author' => 'Mpho L.', 'email' => 'mpho.l@example.co.za', 'rating' => 5, 'text' => 'Wildtrak spec is worth every cent. Off-road tech is brilliant on the 4x4 trails.' ),
		),
	),

	/* 8. New single-cab workhorse — Mahindra Pik Up (diesel). */
	array(
		'stock_no'        => 'DC-2026-0008',
		'vin'             => 'MA1TA2MRXR2A00808',
		'make'            => 'Mahindra',
		'make_slug'       => 'mahindra',
		'model'           => 'Pik Up',
		'variant'         => '2.2 mHawk S4 Single Cab',
		'year'            => 2025,
		'body'            => 'single-cab',
		'condition'       => 'new',
		'price'           => 379900,
		'availability'    => 'In stock',
		'fuel'            => 'diesel',
		'transmission'    => 'Manual',
		'drivetrain'      => 'RWD',
		'engine'          => '2.2L mHawk Diesel',
		'power_kw'        => 103,
		'fuel_economy'    => 7.9,
		'co2'             => 208,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '5yr/90 000km service plan',
		'warranty'        => '5yr/150 000km warranty',
		'doors'           => 2,
		'seats'           => 3,
		'boot_litres'     => 0,
		'towing_capacity' => 2500,
		'colour'          => 'Diamond White',
		'safety_rating'   => 4,
		'features'        => array( 'Heavy-duty load bin', 'Eco and Power drive modes', 'Bluetooth audio', 'Dual front airbags', 'ABS with EBD', 'Full-size spare wheel' ),
		'tags'            => array( 'fleet', 'off-road' ),
		'dealer'          => 'Digicars Northcliff',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.1,
		'reviews'         => array(),
	),

	/* 9. New MPV — Suzuki Ertiga (petrol). */
	array(
		'stock_no'        => 'DC-2026-0009',
		'vin'             => 'MA3ERLF1S00000909',
		'make'            => 'Suzuki',
		'make_slug'       => 'suzuki',
		'model'           => 'Ertiga',
		'variant'         => '1.5 GL Auto',
		'year'            => 2025,
		'body'            => 'mpv',
		'condition'       => 'new',
		'price'           => 339900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Automatic',
		'drivetrain'      => 'FWD',
		'engine'          => '1.5L Petrol',
		'power_kw'        => 77,
		'fuel_economy'    => 6.2,
		'co2'             => 144,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '4yr/60 000km service plan',
		'warranty'        => '5yr/200 000km warranty',
		'doors'           => 5,
		'seats'           => 7,
		'boot_litres'     => 209,
		'towing_capacity' => 1000,
		'colour'          => 'Magma Grey',
		'safety_rating'   => 4,
		'features'        => array( 'Seven-seat configuration', 'Touchscreen infotainment', 'Rear air-con vents', 'Cruise control', 'Reverse camera', 'Four airbags' ),
		'tags'            => array( 'family', 'fleet' ),
		'dealer'          => 'Digicars Melrose Arch',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.4,
		'reviews'         => array(
			array( 'author' => 'Yusuf E.', 'email' => 'yusuf.e@example.co.za', 'rating' => 4, 'text' => 'Seven seats for this price is hard to beat. We use the third row for the kids on school lifts daily.' ),
		),
	),

	/* 10. New minibus — Nissan NV350 Impendulo (petrol). */
	array(
		'stock_no'        => 'DC-2026-0010',
		'vin'             => 'JN1TBNE26U0001010',
		'make'            => 'Nissan',
		'make_slug'       => 'nissan',
		'model'           => 'NV350 Impendulo',
		'variant'         => '2.5i 16-seater',
		'year'            => 2025,
		'body'            => 'minibus',
		'condition'       => 'new',
		'price'           => 629900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Manual',
		'drivetrain'      => 'RWD',
		'engine'          => '2.5L Petrol',
		'power_kw'        => 102,
		'fuel_economy'    => 10.8,
		'co2'             => 251,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '3yr/100 000km service plan',
		'warranty'        => '3yr/100 000km warranty',
		'doors'           => 4,
		'seats'           => 16,
		'boot_litres'     => 0,
		'towing_capacity' => 0,
		'colour'          => 'Polar White',
		'safety_rating'   => 3,
		'features'        => array( '16-seat passenger layout', 'Heavy-duty suspension', 'Dual air-conditioning', 'Driver airbag', 'ABS', 'Large load capacity' ),
		'tags'            => array( 'fleet' ),
		'dealer'          => 'Digicars Northcliff',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.0,
		'reviews'         => array(),
	),

	/* 11. New coupe — Ford Mustang GT (petrol RWD V8). FEATURED. */
	array(
		'stock_no'        => 'DC-2026-0011',
		'vin'             => '1FA6P8TH7R5101111',
		'make'            => 'Ford',
		'make_slug'       => 'ford',
		'model'           => 'Mustang',
		'variant'         => '5.0 GT Fastback',
		'year'            => 2026,
		'body'            => 'coupe',
		'condition'       => 'new',
		'price'           => 1199900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Automatic',
		'drivetrain'      => 'RWD',
		'engine'          => '5.0L V8 Petrol',
		'power_kw'        => 330,
		'fuel_economy'    => 12.8,
		'co2'             => 299,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '5yr/90 000km service plan',
		'warranty'        => '4yr/120 000km warranty',
		'doors'           => 2,
		'seats'           => 4,
		'boot_litres'     => 408,
		'towing_capacity' => 0,
		'colour'          => 'Race Red',
		'safety_rating'   => 4,
		'features'        => array( '5.0L Coyote V8', 'Active valve exhaust', 'Brembo performance brakes', 'Digital instrument cluster', 'Selectable drive modes', 'Recaro-style sports seats', 'Launch control' ),
		'tags'            => array( 'performance', 'luxury' ),
		'dealer'          => 'Digicars Sandton',
		'province'        => 'Gauteng',
		'featured'        => true,
		'rating'          => 4.8,
		'reviews'         => array(
			array( 'author' => 'Riaan B.', 'email' => 'riaan.b@example.co.za', 'rating' => 5, 'text' => 'That V8 soundtrack never gets old. A proper occasion every single time you start it.' ),
		),
	),

	/* 12. New convertible — Ford Mustang EcoBoost Convertible (petrol RWD). */
	array(
		'stock_no'        => 'DC-2026-0012',
		'vin'             => '1FATP8FF8R5101212',
		'make'            => 'Ford',
		'make_slug'       => 'ford',
		'model'           => 'Mustang',
		'variant'         => '2.3 EcoBoost Convertible',
		'year'            => 2025,
		'body'            => 'convertible',
		'condition'       => 'new',
		'price'           => 999900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Automatic',
		'drivetrain'      => 'RWD',
		'engine'          => '2.3L EcoBoost Petrol',
		'power_kw'        => 213,
		'fuel_economy'    => 9.9,
		'co2'             => 232,
		'mileage'         => 0,
		'previous_owners' => 0,
		'service_history' => 'Full (new vehicle)',
		'service_plan'    => '5yr/90 000km service plan',
		'warranty'        => '4yr/120 000km warranty',
		'doors'           => 2,
		'seats'           => 4,
		'boot_litres'     => 332,
		'towing_capacity' => 0,
		'colour'          => 'Oxford White',
		'safety_rating'   => 4,
		'features'        => array( 'Power-folding soft top', 'Heated and cooled front seats', 'B&O premium audio', 'Digital cockpit', 'Adaptive cruise control', 'Wireless Apple CarPlay' ),
		'tags'            => array( 'luxury', 'performance' ),
		'dealer'          => 'Digicars Melrose Arch',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.6,
		'reviews'         => array(),
	),

	/* 13. Demo SUV — Haval Jolion (petrol). */
	array(
		'stock_no'        => 'DC-2026-0013',
		'vin'             => 'LGWFF4A30PD001313',
		'make'            => 'Haval',
		'make_slug'       => 'haval',
		'model'           => 'Jolion',
		'variant'         => '1.5T Super Luxury DCT',
		'year'            => 2025,
		'body'            => 'suv',
		'condition'       => 'demo',
		'price'           => 414900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'DSG',
		'drivetrain'      => 'FWD',
		'engine'          => '1.5L Turbo Petrol',
		'power_kw'        => 105,
		'fuel_economy'    => 7.1,
		'co2'             => 166,
		'mileage'         => 3200,
		'previous_owners' => 0,
		'service_history' => 'Full (demo unit)',
		'service_plan'    => '5yr/60 000km service plan',
		'warranty'        => '5yr/100 000km warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 430,
		'towing_capacity' => 1300,
		'colour'          => 'Hamilton White',
		'safety_rating'   => 5,
		'features'        => array( '12.3-inch touchscreen', 'Wireless charging', 'Adaptive cruise control', 'Lane-keep assist', 'Heated seats', 'Panoramic sunroof', 'Seven airbags' ),
		'tags'            => array( 'family', 'commuter' ),
		'dealer'          => 'Digicars Sandton',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.5,
		'reviews'         => array(
			array( 'author' => 'Carmen N.', 'email' => 'carmen.n@example.co.za', 'rating' => 5, 'text' => 'Bought it as a demo and saved a fortune. Practically brand new with the full warranty intact.' ),
		),
	),

	/* 14. Demo double-cab — GWM P-Series (diesel 4x4). */
	array(
		'stock_no'        => 'DC-2026-0014',
		'vin'             => 'LGWDD2A50PD001414',
		'make'            => 'GWM',
		'make_slug'       => 'gwm',
		'model'           => 'P-Series',
		'variant'         => '2.0TD LT 4x4',
		'year'            => 2025,
		'body'            => 'double-cab',
		'condition'       => 'demo',
		'price'           => 569900,
		'availability'    => 'In stock',
		'fuel'            => 'diesel',
		'transmission'    => 'Automatic',
		'drivetrain'      => '4x4',
		'engine'          => '2.0L Turbo Diesel',
		'power_kw'        => 120,
		'fuel_economy'    => 8.4,
		'co2'             => 221,
		'mileage'         => 5800,
		'previous_owners' => 0,
		'service_history' => 'Full (demo unit)',
		'service_plan'    => '5yr/60 000km service plan',
		'warranty'        => '5yr/100 000km warranty',
		'doors'           => 4,
		'seats'           => 5,
		'boot_litres'     => 0,
		'towing_capacity' => 3000,
		'colour'          => 'Pittsburgh Silver',
		'safety_rating'   => 5,
		'features'        => array( 'Diff lock', 'Tyre pressure monitoring', '9-inch touchscreen', 'Leather seats', '360-degree camera', 'Adaptive cruise control', 'Tow bar fitted' ),
		'tags'            => array( 'off-road', 'fleet', 'family' ),
		'dealer'          => 'Digicars Northcliff',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.3,
		'reviews'         => array(),
	),

	/* 15. Demo EV SUV — Hyundai Kona Electric (electric). FEATURED. */
	array(
		'stock_no'        => 'DC-2026-0015',
		'vin'             => 'KMHK581GFPU001515',
		'make'            => 'Hyundai',
		'make_slug'       => 'hyundai',
		'model'           => 'Kona Electric',
		'variant'         => '150kW Ultimate',
		'year'            => 2025,
		'body'            => 'suv',
		'condition'       => 'demo',
		'price'           => 749900,
		'availability'    => 'In stock',
		'fuel'            => 'electric',
		'transmission'    => 'Automatic',
		'drivetrain'      => 'FWD',
		'engine'          => 'Single electric motor',
		'power_kw'        => 150,
		'range_km'        => 490,
		'battery_kwh'     => 65.4,
		'mileage'         => 4100,
		'previous_owners' => 0,
		'service_history' => 'Full (demo unit)',
		'service_plan'    => '6yr/90 000km service plan',
		'warranty'        => '8yr/160 000km battery warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 466,
		'towing_capacity' => 0,
		'colour'          => 'Atlas White',
		'safety_rating'   => 5,
		'features'        => array( 'Vehicle-to-load (V2L)', '12.3-inch dual displays', 'Heat pump', 'Heated and ventilated seats', 'Highway Driving Assist', 'Bose premium audio', '100kW DC fast charging' ),
		'tags'            => array( 'eco', 'family', 'commuter' ),
		'dealer'          => 'Digicars Melrose Arch',
		'province'        => 'Gauteng',
		'featured'        => true,
		'rating'          => 4.7,
		'reviews'         => array(
			array( 'author' => 'Tlhalefang M.', 'email' => 'tlhalefang.m@example.co.za', 'rating' => 5, 'text' => 'Real-world 450km is doable. Fast charging on trips is painless and home charging is dirt cheap.' ),
			array( 'author' => 'Pieter G.', 'email' => 'pieter.g@example.co.za', 'rating' => 4, 'text' => 'Brilliant EV. The V2L feature powered our braai during loadshedding which sealed the deal.' ),
		),
	),

	/* 16. Used hatch — VW Golf (petrol). */
	array(
		'stock_no'        => 'DC-2026-0016',
		'vin'             => 'WVWZZZ1KZBW001616',
		'make'            => 'Volkswagen',
		'make_slug'       => 'volkswagen',
		'model'           => 'Golf',
		'variant'         => '1.4 TSI Comfortline',
		'year'            => 2019,
		'body'            => 'hatch',
		'condition'       => 'used',
		'price'           => 289900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'DSG',
		'drivetrain'      => 'FWD',
		'engine'          => '1.4L TSI Petrol',
		'power_kw'        => 92,
		'fuel_economy'    => 5.4,
		'co2'             => 125,
		'mileage'         => 68000,
		'previous_owners' => 1,
		'service_history' => 'Full VW service history',
		'service_plan'    => 'Service plan expired',
		'warranty'        => '6-month dealer warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 380,
		'towing_capacity' => 1200,
		'colour'          => 'Tungsten Silver',
		'safety_rating'   => 5,
		'features'        => array( 'Adaptive cruise control', 'App-Connect', 'Park assist', 'Multifunction display', 'Front and rear PDC', 'Bluetooth' ),
		'tags'            => array( 'commuter', 'first-car' ),
		'dealer'          => 'Digicars Northcliff',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.4,
		'reviews'         => array(
			array( 'author' => 'Bongani Z.', 'email' => 'bongani.z@example.co.za', 'rating' => 4, 'text' => 'Tidy one-owner Golf with full history. Drives tight, exactly as described in the listing.' ),
		),
	),

	/* 17. Used sedan — Toyota Corolla (petrol). */
	array(
		'stock_no'        => 'DC-2026-0017',
		'vin'             => 'JTDBR32E600001717',
		'make'            => 'Toyota',
		'make_slug'       => 'toyota',
		'model'           => 'Corolla',
		'variant'         => '1.8 Prestige CVT',
		'year'            => 2021,
		'body'            => 'sedan',
		'condition'       => 'used',
		'price'           => 359900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'CVT',
		'drivetrain'      => 'FWD',
		'engine'          => '1.8L Petrol',
		'power_kw'        => 103,
		'fuel_economy'    => 6.8,
		'co2'             => 158,
		'mileage'         => 52000,
		'previous_owners' => 1,
		'service_history' => 'Full Toyota service history',
		'service_plan'    => 'Service plan to 90 000km',
		'warranty'        => 'Balance of 3yr/100 000km warranty',
		'doors'           => 4,
		'seats'           => 5,
		'boot_litres'     => 470,
		'towing_capacity' => 750,
		'colour'          => 'Celestite Grey',
		'safety_rating'   => 5,
		'features'        => array( 'Toyota Safety Sense', 'Adaptive cruise control', '8-inch touchscreen', 'Reverse camera', 'Dual-zone climate control', 'LED headlights', 'Seven airbags' ),
		'tags'            => array( 'family', 'commuter', 'fleet' ),
		'dealer'          => 'Digicars Sandton',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.8,
		'reviews'         => array(
			array( 'author' => 'Zanele Q.', 'email' => 'zanele.q@example.co.za', 'rating' => 5, 'text' => 'Bulletproof reliability and the full Toyota history gave me total peace of mind. Sips fuel.' ),
		),
	),

	/* 18. Used double-cab — Toyota Hilux (diesel 4x4). */
	array(
		'stock_no'        => 'DC-2026-0018',
		'vin'             => 'AHTFR22G50A001818',
		'make'            => 'Toyota',
		'make_slug'       => 'toyota',
		'model'           => 'Hilux',
		'variant'         => '2.8 GD-6 Raider 4x4 Auto',
		'year'            => 2020,
		'body'            => 'double-cab',
		'condition'       => 'used',
		'price'           => 549900,
		'availability'    => 'In stock',
		'fuel'            => 'diesel',
		'transmission'    => 'Automatic',
		'drivetrain'      => '4x4',
		'engine'          => '2.8L GD-6 Diesel',
		'power_kw'        => 130,
		'fuel_economy'    => 8.5,
		'co2'             => 224,
		'mileage'         => 97000,
		'previous_owners' => 1,
		'service_history' => 'Full Toyota service history',
		'service_plan'    => 'Service plan expired',
		'warranty'        => '6-month dealer warranty',
		'doors'           => 4,
		'seats'           => 5,
		'boot_litres'     => 0,
		'towing_capacity' => 3500,
		'colour'          => 'Graphite Grey',
		'safety_rating'   => 5,
		'features'        => array( 'Rear diff lock', 'Toyota Safety Sense', '8-inch touchscreen', 'Tow bar', 'Cruise control', 'Reverse camera', 'Side steps' ),
		'tags'            => array( 'off-road', 'fleet', 'family' ),
		'dealer'          => 'Digicars Northcliff',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.9,
		'reviews'         => array(
			array( 'author' => 'Andries S.', 'email' => 'andries.s@example.co.za', 'rating' => 5, 'text' => 'A Hilux with full history is gold. Hard-working bakkie that just refuses to give trouble.' ),
		),
	),

	/* 19. Used SUV — Renault Duster (diesel). */
	array(
		'stock_no'        => 'DC-2026-0019',
		'vin'             => 'VF1HSRA00R0001919',
		'make'            => 'Renault',
		'make_slug'       => 'renault',
		'model'           => 'Duster',
		'variant'         => '1.5 dCi Dynamique',
		'year'            => 2018,
		'body'            => 'suv',
		'condition'       => 'used',
		'price'           => 219900,
		'availability'    => 'In stock',
		'fuel'            => 'diesel',
		'transmission'    => 'Manual',
		'drivetrain'      => 'FWD',
		'engine'          => '1.5L dCi Diesel',
		'power_kw'        => 80,
		'fuel_economy'    => 5.0,
		'co2'             => 133,
		'mileage'         => 112000,
		'previous_owners' => 2,
		'service_history' => 'Partial service history',
		'service_plan'    => 'No service plan',
		'warranty'        => '3-month dealer warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 478,
		'towing_capacity' => 1500,
		'colour'          => 'Cosmos Blue',
		'safety_rating'   => 4,
		'features'        => array( 'Touchscreen navigation', 'Cruise control', 'Roof rails', 'Rear park sensors', 'Bluetooth', 'Air-conditioning' ),
		'tags'            => array( 'commuter', 'off-road', 'first-car' ),
		'dealer'          => 'Digicars Melrose Arch',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.0,
		'reviews'         => array(),
	),

	/* 20. Used hybrid SUV — Toyota Corolla Cross Hybrid (hybrid). FEATURED. */
	array(
		'stock_no'        => 'DC-2026-0020',
		'vin'             => 'JTDKB20U703002020',
		'make'            => 'Toyota',
		'make_slug'       => 'toyota',
		'model'           => 'Corolla Cross',
		'variant'         => '1.8 Hybrid XR',
		'year'            => 2023,
		'body'            => 'suv',
		'condition'       => 'used',
		'price'           => 469900,
		'availability'    => 'In stock',
		'fuel'            => 'hybrid',
		'transmission'    => 'CVT',
		'drivetrain'      => 'FWD',
		'engine'          => '1.8L Petrol-electric Hybrid',
		'power_kw'        => 90,
		'fuel_economy'    => 4.3,
		'co2'             => 101,
		'mileage'         => 31000,
		'previous_owners' => 1,
		'service_history' => 'Full Toyota service history',
		'service_plan'    => 'Service plan to 90 000km',
		'warranty'        => 'Balance of 3yr/100 000km + 8yr hybrid battery',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 380,
		'towing_capacity' => 750,
		'colour'          => 'Platinum White Pearl',
		'safety_rating'   => 5,
		'features'        => array( 'Self-charging hybrid', 'Toyota Safety Sense', 'Adaptive cruise control', '9-inch touchscreen', 'Wireless Apple CarPlay', 'Blind-spot monitor', 'Reverse camera' ),
		'tags'            => array( 'eco', 'family', 'commuter' ),
		'dealer'          => 'Digicars Sandton',
		'province'        => 'Gauteng',
		'featured'        => true,
		'rating'          => 4.7,
		'reviews'         => array(
			array( 'author' => 'Fatima D.', 'email' => 'fatima.d@example.co.za', 'rating' => 5, 'text' => 'Averaging 4.5L/100km in city traffic. The hybrid saving pays for itself and it is whisper quiet.' ),
		),
	),

	/* 21. Used sedan — Hyundai Elantra (petrol). */
	array(
		'stock_no'        => 'DC-2026-0021',
		'vin'             => 'KMHD841CBPU002121',
		'make'            => 'Hyundai',
		'make_slug'       => 'hyundai',
		'model'           => 'Elantra',
		'variant'         => '1.6 Executive Auto',
		'year'            => 2017,
		'body'            => 'sedan',
		'condition'       => 'used',
		'price'           => 189900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Automatic',
		'drivetrain'      => 'FWD',
		'engine'          => '1.6L Petrol',
		'power_kw'        => 94,
		'fuel_economy'    => 6.6,
		'co2'             => 154,
		'mileage'         => 119000,
		'previous_owners' => 2,
		'service_history' => 'Full service history',
		'service_plan'    => 'No service plan',
		'warranty'        => '3-month dealer warranty',
		'doors'           => 4,
		'seats'           => 5,
		'boot_litres'     => 458,
		'towing_capacity' => 750,
		'colour'          => 'Phantom Black',
		'safety_rating'   => 4,
		'features'        => array( 'Reverse camera', 'Cruise control', 'Bluetooth', 'Leather seats', 'Climate control', 'Park sensors' ),
		'tags'            => array( 'first-car', 'commuter' ),
		'dealer'          => 'Digicars Northcliff',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.1,
		'reviews'         => array(),
	),

	/* 22. Used single-cab — Nissan NP200 (petrol). */
	array(
		'stock_no'        => 'DC-2026-0022',
		'vin'             => 'ADNUSN1D5R0002222',
		'make'            => 'Nissan',
		'make_slug'       => 'nissan',
		'model'           => 'NP200',
		'variant'         => '1.6 16v Single Cab',
		'year'            => 2020,
		'body'            => 'single-cab',
		'condition'       => 'used',
		'price'           => 169900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Manual',
		'drivetrain'      => 'FWD',
		'engine'          => '1.6L Petrol',
		'power_kw'        => 64,
		'fuel_economy'    => 7.6,
		'co2'             => 179,
		'mileage'         => 88000,
		'previous_owners' => 1,
		'service_history' => 'Partial service history',
		'service_plan'    => 'No service plan',
		'warranty'        => '3-month dealer warranty',
		'doors'           => 2,
		'seats'           => 2,
		'boot_litres'     => 0,
		'towing_capacity' => 850,
		'colour'          => 'Snow White',
		'safety_rating'   => 3,
		'features'        => array( 'Tow bar', 'Load bin liner', 'Air-conditioning', 'Power steering', 'Bluetooth radio', 'Driver airbag' ),
		'tags'            => array( 'fleet', 'first-car' ),
		'dealer'          => 'Digicars Melrose Arch',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 3.9,
		'reviews'         => array(),
	),

	/* 23. Demo hatch — Tata Altroz (petrol). */
	array(
		'stock_no'        => 'DC-2026-0023',
		'vin'             => 'MAT625012R0002323',
		'make'            => 'Tata',
		'make_slug'       => 'tata',
		'model'           => 'Altroz',
		'variant'         => '1.2 XT',
		'year'            => 2025,
		'body'            => 'hatch',
		'condition'       => 'demo',
		'price'           => 259900,
		'availability'    => 'In stock',
		'fuel'            => 'petrol',
		'transmission'    => 'Manual',
		'drivetrain'      => 'FWD',
		'engine'          => '1.2L Petrol',
		'power_kw'        => 65,
		'fuel_economy'    => 6.1,
		'co2'             => 142,
		'mileage'         => 2600,
		'previous_owners' => 0,
		'service_history' => 'Full (demo unit)',
		'service_plan'    => '3yr/45 000km service plan',
		'warranty'        => '5yr/150 000km warranty',
		'doors'           => 5,
		'seats'           => 5,
		'boot_litres'     => 345,
		'towing_capacity' => 0,
		'colour'          => 'Harbour Blue',
		'safety_rating'   => 5,
		'features'        => array( '5-star Global NCAP body', 'Touchscreen infotainment', 'Cruise control', 'Rear camera', 'Dual airbags', 'Cooled glovebox' ),
		'tags'            => array( 'first-car', 'commuter' ),
		'dealer'          => 'Digicars Sandton',
		'province'        => 'Gauteng',
		'featured'        => false,
		'rating'          => 4.2,
		'reviews'         => array(),
	),
);

/* -------------------------------------------------------------------------
 * Helix AI — WooCommerce pa_* attribute helpers.
 *
 * Helix reads the standard pa_* attribute taxonomy system. These helpers
 * create global attributes on demand and assign terms to each product.
 * They run AFTER the existing _vehicle_* meta + custom taxonomy block so
 * both systems stay in sync and neither overwrites the other.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'digicars_ensure_wc_attribute' ) ) {
	/**
	 * Ensure a WooCommerce global attribute taxonomy exists and is registered.
	 *
	 * @param string $slug Attribute slug WITHOUT the pa_ prefix, e.g. 'make'.
	 * @return bool True if the taxonomy is ready for use.
	 */
	function digicars_ensure_wc_attribute( string $slug ): bool {
		$taxonomy = 'pa_' . $slug;

		if ( taxonomy_exists( $taxonomy ) ) {
			return true;
		}

		// Flush WC cache so freshly-created attributes are visible.
		wp_cache_delete( 'wc_attribute_taxonomies', 'woocommerce' );

		// Check if the row already exists in the DB (created earlier this run).
		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			foreach ( (array) wc_get_attribute_taxonomies() as $attr ) {
				if ( $attr->attribute_name === $slug ) {
					if ( function_exists( 'wc_register_attribute_taxonomies' ) ) {
						wc_register_attribute_taxonomies();
					}
					return taxonomy_exists( $taxonomy );
				}
			}
		}

		// Create the global attribute for the first time.
		if ( ! function_exists( 'wc_create_attribute' ) ) {
			return false;
		}

		$label  = ucwords( str_replace( '-', ' ', $slug ) );
		$result = wc_create_attribute( array(
			'name'         => $label,
			'slug'         => $slug,
			'type'         => 'select',
			'order_by'     => 'menu_order',
			'has_archives' => false,
		) );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		wp_cache_delete( 'wc_attribute_taxonomies', 'woocommerce' );

		if ( function_exists( 'wc_register_attribute_taxonomies' ) ) {
			wc_register_attribute_taxonomies();
		}

		return taxonomy_exists( $taxonomy );
	}
}

if ( ! function_exists( 'digicars_set_product_pa_attributes' ) ) {
	/**
	 * Create attribute terms and wire them to a product, then update the
	 * _product_attributes meta so the WC product page renders them.
	 *
	 * @param int   $product_id WP post ID of the product.
	 * @param array $attrs      Map of attribute-slug (without pa_) => value string.
	 */
	function digicars_set_product_pa_attributes( int $product_id, array $attrs ): void {
		$product_attributes = array();
		$position           = 0;

		foreach ( $attrs as $slug => $value ) {
			$value = (string) $value;
			if ( '' === $value ) {
				continue;
			}

			if ( ! digicars_ensure_wc_attribute( $slug ) ) {
				continue;
			}

			$taxonomy = 'pa_' . $slug;

			// Resolve or create the term.
			$term = get_term_by( 'name', $value, $taxonomy );
			if ( ! $term instanceof WP_Term ) {
				$inserted = wp_insert_term( $value, $taxonomy );
				$term     = ! is_wp_error( $inserted )
					? get_term( (int) $inserted['term_id'], $taxonomy )
					: get_term_by( 'name', $value, $taxonomy );
			}

			if ( $term instanceof WP_Term ) {
				wp_set_object_terms( $product_id, array( (int) $term->term_id ), $taxonomy, false );
			}

			$product_attributes[ $taxonomy ] = array(
				'name'         => $taxonomy,
				'value'        => '',
				'position'     => $position++,
				'is_visible'   => 1,
				'is_variation' => 0,
				'is_taxonomy'  => 1,
			);
		}

		// Merge with existing (preserves any non-PA attributes already set).
		$existing = get_post_meta( $product_id, '_product_attributes', true );
		update_post_meta(
			$product_id,
			'_product_attributes',
			array_merge( is_array( $existing ) ? $existing : array(), $product_attributes )
		);
	}
}

/* -------------------------------------------------------------------------
 * Vehicles certified as CPO (Certified Pre-Owned).
 * Three used units with full service history and single-owner records.
 * ---------------------------------------------------------------------- */
$digicars_cpo_stocks = array(
	'DC-2026-0016', // VW Golf 1.4 TSI — 1 owner, 68k km, full VW history
	'DC-2026-0017', // Toyota Corolla — 1 owner, 52k km, full Toyota history
	'DC-2026-0020', // Toyota Corolla Cross Hybrid — 1 owner, 31k km, full Toyota history
);

/* -------------------------------------------------------------------------
 * Import loop.
 * ---------------------------------------------------------------------- */

$digicars_created = 0;
$digicars_updated = 0;
$digicars_total   = count( $digicars_vehicles );

digicars_seed_log( sprintf( 'Digicars importer: processing %d vehicles...', $digicars_total ) );

foreach ( $digicars_vehicles as $v ) {

	$stock_no = (string) $v['stock_no'];

	/* ---- Idempotency: find an existing product by stock number. -------- */
	$existing_ids = get_posts(
		array(
			'post_type'        => 'product',
			'post_status'      => 'any',
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_vehicle_stock_no',
					'value' => $stock_no,
				),
			),
		)
	);

	$product_id = ! empty( $existing_ids ) ? (int) $existing_ids[0] : 0;
	$is_update  = $product_id > 0;

	/* ---- Title + descriptions. ---------------------------------------- */
	$title = trim( sprintf( '%d %s %s %s', (int) $v['year'], $v['make'], $v['model'], $v['variant'] ) );

	$cond_label = ucfirst( $v['condition'] );
	$km_phrase  = ( (int) $v['mileage'] > 0 )
		? sprintf( 'with %s km on the clock', number_format( (int) $v['mileage'], 0, '.', ' ' ) )
		: 'with delivery mileage only';

	$feature_list = is_array( $v['features'] ) ? $v['features'] : array();
	$feature_html = '';
	if ( ! empty( $feature_list ) ) {
		$feature_html = "<ul>\n";
		foreach ( $feature_list as $feat ) {
			$feature_html .= '<li>' . esc_html( $feat ) . "</li>\n";
		}
		$feature_html .= "</ul>\n";
	}

	$long_description = sprintf(
		"<p>This %s %s is %s, finished in %s and available now at %s in %s. Powered by a %s driving the %s through a %s gearbox, it delivers %d kW and is a practical %d-seat %s.</p>\n",
		strtolower( $cond_label ),
		esc_html( $title ),
		esc_html( $km_phrase ),
		esc_html( $v['colour'] ),
		esc_html( $v['dealer'] ),
		esc_html( $v['province'] ),
		esc_html( $v['engine'] ),
		strtoupper( esc_html( $v['drivetrain'] ) ),
		strtolower( esc_html( $v['transmission'] ) ),
		(int) $v['power_kw'],
		(int) $v['seats'],
		esc_html( $v['body'] )
	);
	$long_description .= sprintf(
		"<p>It comes backed by a %s and %s, with %s on file. Every Digicars vehicle is inspected, roadworthy-prepared and offered with on-the-spot finance pre-approval and a trade-in valuation.</p>\n",
		esc_html( $v['warranty'] ),
		esc_html( $v['service_plan'] ),
		esc_html( strtolower( $v['service_history'] ) )
	);
	if ( '' !== $feature_html ) {
		$long_description .= "<p><strong>Key features</strong></p>\n" . $feature_html;
	}

	$short_description = sprintf(
		'%s %s in %s, %s. %s — finance from this dealer available.',
		$cond_label,
		esc_html( $v['model'] . ' ' . $v['variant'] ),
		esc_html( $v['colour'] ),
		esc_html( $km_phrase ),
		( 'electric' === $v['fuel'] )
			? sprintf( '%d km range, %s kWh battery', (int) $v['range_km'], rtrim( rtrim( (string) $v['battery_kwh'], '0' ), '.' ) )
			: sprintf( '%s L/100km, %d kW', (string) $v['fuel_economy'], (int) $v['power_kw'] )
	);

	/* ---- Insert / update the product post. ---------------------------- */
	$postarr = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'post_title'     => $title,
		'post_content'   => $long_description,
		'post_excerpt'   => $short_description,
		'comment_status' => 'open',
	);

	if ( $is_update ) {
		$postarr['ID'] = $product_id;
		$result        = wp_update_post( $postarr, true );
	} else {
		$result = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $result ) ) {
		digicars_seed_log( sprintf( '  ! FAILED %s - %s', $stock_no, $result->get_error_message() ) );
		continue;
	}

	$product_id = (int) ( $is_update ? $product_id : $result );

	/* ---- WooCommerce product-type term (simple). ---------------------- */
	wp_set_object_terms( $product_id, 'simple', 'product_type', false );

	/* ---- Compute derived monthly instalment. -------------------------- */
	$price = (float) $v['price'];
	if ( function_exists( 'digicars_monthly_from' ) ) {
		$monthly = digicars_monthly_from( $price );
	} else {
		// Inline mirror of digicars_monthly_from() defaults (term 72, rate 0.115).
		$term    = 72;
		$rate    = 0.115;
		$i       = $rate / 12;
		$factor  = pow( 1 + $i, $term );
		$monthly = (int) ceil( $price * ( $i / ( 1 - 1 / $factor ) ) );
	}

	/* ---- Write all _vehicle_* meta (matches digicars_meta_definitions). */
	$meta = array(
		// Identity.
		'_vehicle_make'            => $v['make'],
		'_vehicle_model'           => $v['model'],
		'_vehicle_variant'         => $v['variant'],
		'_vehicle_year'            => (int) $v['year'],
		'_vehicle_body_type'       => $v['body'],
		'_vehicle_condition'       => $v['condition'],
		'_vehicle_stock_no'        => $stock_no,
		'_vehicle_vin'             => $v['vin'],

		// Pricing / finance.
		'_vehicle_price'           => $price,
		'_vehicle_monthly_from'    => (int) $monthly,
		'_vehicle_availability'    => $v['availability'],

		// Powertrain.
		'_vehicle_fuel'            => $v['fuel'],
		'_vehicle_transmission'    => $v['transmission'],
		'_vehicle_drivetrain'      => $v['drivetrain'],
		'_vehicle_engine'          => $v['engine'],
		'_vehicle_power_kw'        => (int) $v['power_kw'],

		// History.
		'_vehicle_mileage'         => (int) $v['mileage'],
		'_vehicle_previous_owners' => (int) $v['previous_owners'],
		'_vehicle_service_history' => $v['service_history'],
		'_vehicle_service_plan'    => $v['service_plan'],
		'_vehicle_warranty'        => $v['warranty'],

		// Practicality.
		'_vehicle_doors'           => (int) $v['doors'],
		'_vehicle_seats'           => (int) $v['seats'],
		'_vehicle_boot_litres'     => (int) $v['boot_litres'],
		'_vehicle_towing_capacity' => (int) $v['towing_capacity'],
		'_vehicle_colour'          => $v['colour'],

		// Safety.
		'_vehicle_safety_rating'   => (int) $v['safety_rating'],
		'_vehicle_features'        => array_values( $feature_list ),

		// Location.
		'_vehicle_dealer'          => $v['dealer'],
		'_vehicle_province'        => $v['province'],

		// Semantic.
		'_vehicle_lifestyle_tags'  => array_values( (array) $v['tags'] ),
	);

	// Powertrain split: EVs carry range_km + battery_kwh and NO economy/co2.
	if ( 'electric' === $v['fuel'] ) {
		$meta['_vehicle_range_km']    = (int) $v['range_km'];
		$meta['_vehicle_battery_kwh'] = (float) $v['battery_kwh'];
		// Clear any stale combustion figures from a previous run.
		delete_post_meta( $product_id, '_vehicle_fuel_economy' );
		delete_post_meta( $product_id, '_vehicle_co2' );
	} else {
		$meta['_vehicle_fuel_economy'] = (float) $v['fuel_economy'];
		$meta['_vehicle_co2']          = (int) $v['co2'];
		// Clear any stale EV figures from a previous run.
		delete_post_meta( $product_id, '_vehicle_range_km' );
		delete_post_meta( $product_id, '_vehicle_battery_kwh' );
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( $product_id, $key, $value );
	}

	// WooCommerce price meta so WC reads it consistently (display only; not purchasable).
	update_post_meta( $product_id, '_price', $price );
	update_post_meta( $product_id, '_regular_price', $price );
	update_post_meta( $product_id, '_visibility', 'visible' );
	update_post_meta( $product_id, '_stock_status', 'instock' );
	update_post_meta( $product_id, '_virtual', 'no' );
	update_post_meta( $product_id, '_manage_stock', 'no' );
	update_post_meta( $product_id, '_sku', $stock_no );

	/* ---- AI summary (built after meta is in place). ------------------- */
	$summary = '';
	if ( function_exists( 'digicars_build_ai_summary' ) ) {
		// Clear any prior summary so the helper regenerates from fresh meta.
		delete_post_meta( $product_id, '_vehicle_ai_summary' );
		$summary = digicars_build_ai_summary( $product_id );
	}
	if ( '' === trim( (string) $summary ) ) {
		$summary = sprintf(
			'%s is a %s %s %s. It produces %d kW through a %s gearbox and %s drivetrain. Priced at R%s, from R%s per month, and well suited to %s buyers.',
			$title,
			strtolower( $v['condition'] ),
			strtolower( $v['fuel'] ),
			strtolower( $v['body'] ),
			(int) $v['power_kw'],
			strtolower( $v['transmission'] ),
			strtoupper( $v['drivetrain'] ),
			number_format( $price, 0, '.', ' ' ),
			number_format( (int) $monthly, 0, '.', ' ' ),
			implode( ', ', (array) $v['tags'] )
		);
	}
	update_post_meta( $product_id, '_vehicle_ai_summary', $summary );

	/* ---- Taxonomies (terms created on demand). ------------------------ */
	$body_types = function_exists( 'digicars_body_types' ) ? digicars_body_types() : array();
	$body_label = isset( $body_types[ $v['body'] ]['label'] ) ? $body_types[ $v['body'] ]['label'] : ucfirst( $v['body'] );

	$cond_names = array(
		'new'  => 'New',
		'demo' => 'Demo',
		'used' => 'Used',
	);
	$fuel_names = array(
		'petrol'   => 'Petrol',
		'diesel'   => 'Diesel',
		'electric' => 'Electric',
		'hybrid'   => 'Hybrid',
	);

	$term_assignments = array(
		'product_cat'       => digicars_seed_term( 'product_cat', $body_label, $v['body'] ),
		'vehicle_make'      => digicars_seed_term( 'vehicle_make', $v['make'], $v['make_slug'] ),
		'vehicle_condition' => digicars_seed_term( 'vehicle_condition', isset( $cond_names[ $v['condition'] ] ) ? $cond_names[ $v['condition'] ] : ucfirst( $v['condition'] ), $v['condition'] ),
		'vehicle_fuel'      => digicars_seed_term( 'vehicle_fuel', isset( $fuel_names[ $v['fuel'] ] ) ? $fuel_names[ $v['fuel'] ] : ucfirst( $v['fuel'] ), $v['fuel'] ),
		'vehicle_dealer'    => digicars_seed_term( 'vehicle_dealer', $v['dealer'] ),
	);

	foreach ( $term_assignments as $taxonomy => $term_id ) {
		if ( $term_id > 0 ) {
			wp_set_object_terms( $product_id, array( $term_id ), $taxonomy, false );
		}
	}

	/* ---- Helix AI: WooCommerce pa_* product attributes. --------------- *
	 *
	 * Helix reads from standard WooCommerce global attribute taxonomies
	 * (pa_make, pa_model, ...) rather than _vehicle_* post meta. We set
	 * both so the custom theme templates AND Helix stay in sync.
	 *
	 * Body type and transmission are normalised to Helix's expected values.
	 * engine-cc is parsed from the descriptive engine string (e.g. '2.8L').
	 * certified-used is driven by the $digicars_cpo_stocks array above.
	 * --------------------------------------------------------------------- */
	preg_match( '/^(\d+(?:\.\d+)?)L/i', $v['engine'], $cc_match );
	$engine_cc = isset( $cc_match[1] ) ? (int) round( (float) $cc_match[1] * 1000 ) : 0;

	$trans_raw        = strtolower( $v['transmission'] );
	$trans_normalized = ( 'manual' === $trans_raw ) ? 'manual' : 'automatic';

	static $body_helix_map = array(
		'hatch'       => 'hatchback',
		'double-cab'  => 'bakkie',
		'single-cab'  => 'bakkie',
		'mpv'         => 'minivan',
		'minibus'     => 'minivan',
		'convertible' => 'coupe',
	);
	$body_helix = isset( $body_helix_map[ $v['body'] ] ) ? $body_helix_map[ $v['body'] ] : $v['body'];

	$certified_used = in_array( $stock_no, $digicars_cpo_stocks, true ) ? 'yes' : 'no';

	digicars_set_product_pa_attributes( $product_id, array(
		'make'            => (string) $v['make'],
		'model'           => (string) $v['model'],
		'year'            => (string) $v['year'],
		'condition'       => (string) $v['condition'],
		'mileage-km'      => (string) $v['mileage'],
		'fuel-type'       => (string) $v['fuel'],
		'transmission'    => $trans_normalized,
		'body-type'       => $body_helix,
		'colour'          => (string) $v['colour'],
		'engine-cc'       => (string) $engine_cc,
		'doors'           => (string) $v['doors'],
		'safety-rating'   => (string) $v['safety_rating'],
		'ncap-stars'      => (string) $v['safety_rating'],
		'finance-from-zar' => (string) (int) $monthly,
		'certified-used'  => $certified_used,
	) );

	/* ---- Featured flag (homepage best sellers). ----------------------- */
	$is_featured = ! empty( $v['featured'] );
	$wc_product  = wc_get_product( $product_id );
	if ( $wc_product instanceof WC_Product ) {
		$wc_product->set_featured( $is_featured );
		$wc_product->save();
	} else {
		// Fallback: drive the product_visibility term directly.
		wp_set_object_terms( $product_id, $is_featured ? array( 'featured' ) : array(), 'product_visibility', false );
	}

	/* ---- Synthetic ratings + (optional) review comments. -------------- */
	if ( isset( $v['rating'] ) && $v['rating'] > 0 ) {
		$reviews      = ( isset( $v['reviews'] ) && is_array( $v['reviews'] ) ) ? $v['reviews'] : array();
		$review_count = count( $reviews );

		if ( $review_count > 0 ) {
			// Insert review comments idempotently (match on author email + content).
			foreach ( $reviews as $rev ) {
				$author = (string) $rev['author'];
				$body   = (string) $rev['text'];
				$email  = isset( $rev['email'] ) ? (string) $rev['email'] : '';

				$dupes = get_comments(
					array(
						'post_id'      => $product_id,
						'type'         => 'review',
						'author_email' => $email,
						'fields'       => 'ids',
						'number'       => 10,
					)
				);

				$already = false;
				foreach ( (array) $dupes as $cid ) {
					$existing_comment = get_comment( $cid );
					if ( $existing_comment && trim( $existing_comment->comment_content ) === trim( $body ) ) {
						$already = true;
						break;
					}
				}
				if ( $already ) {
					continue;
				}

				$comment_id = wp_insert_comment(
					array(
						'comment_post_ID'      => $product_id,
						'comment_author'       => $author,
						'comment_author_email' => $email,
						'comment_content'      => $body,
						'comment_type'         => 'review',
						'comment_approved'     => 1,
						'comment_date'         => current_time( 'mysql' ),
					)
				);
				if ( $comment_id && ! is_wp_error( $comment_id ) ) {
					add_comment_meta( $comment_id, 'rating', (int) $rev['rating'], true );
					add_comment_meta( $comment_id, 'verified', 1, true );
				}
			}

			// Recompute aggregates from the real comments where WC helpers exist.
			$avg = 0.0;
			$cnt = 0;
			if ( $wc_product instanceof WC_Product && class_exists( 'WC_Comments' ) ) {
				$avg = (float) \WC_Comments::get_average_rating_for_product( $wc_product );
				$cnt = (int) \WC_Comments::get_review_count_for_product( $wc_product );
			}
			if ( $avg > 0 ) {
				update_post_meta( $product_id, '_wc_average_rating', $avg );
				update_post_meta( $product_id, '_wc_review_count', $cnt );
			} else {
				update_post_meta( $product_id, '_wc_average_rating', (float) $v['rating'] );
				update_post_meta( $product_id, '_wc_review_count', $review_count );
			}
		} else {
			// No comment bodies — set synthetic aggregate meta only.
			update_post_meta( $product_id, '_wc_average_rating', (float) $v['rating'] );
			update_post_meta( $product_id, '_wc_review_count', 0 );
		}
	}

	/* ---- Thumbnail sideload (best-effort, never fatal). --------------- */
	if ( ! has_post_thumbnail( $product_id ) && function_exists( 'get_theme_file_path' ) ) {
		$candidates = array(
			get_theme_file_path( 'images/vehicles/' . $v['body'] . '-render.jpg' ),
			get_theme_file_path( 'images/vehicles/' . $v['body'] . '-render.png' ),
			get_theme_file_path( 'images/vehicles/' . $v['body'] . '-render.svg' ),
			get_theme_file_path( 'images/vehicles/_default.jpg' ),
			get_theme_file_path( 'images/vehicles/_default.svg' ),
		);
		foreach ( $candidates as $img_path ) {
			if ( ! $img_path || ! file_exists( $img_path ) ) {
				continue;
			}
			// Ensure the admin image helpers are available.
			if ( ! function_exists( 'wp_generate_attachment_metadata' ) && file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
			}
			if ( ! function_exists( 'wp_generate_attachment_metadata' ) || ! function_exists( 'wp_insert_attachment' ) ) {
				break; // Helpers unavailable; skip silently.
			}

			$upload = wp_upload_bits( basename( $img_path ), null, file_get_contents( $img_path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
				$filetype   = wp_check_filetype( $upload['file'], null );
				$attachment = array(
					'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'image/jpeg',
					'post_title'     => $title,
					'post_content'   => '',
					'post_status'    => 'inherit',
				);
				$attach_id = wp_insert_attachment( $attachment, $upload['file'], $product_id );
				if ( $attach_id && ! is_wp_error( $attach_id ) ) {
					$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
					wp_update_attachment_metadata( $attach_id, $attach_data );
					set_post_thumbnail( $product_id, $attach_id );
				}
			}
			break; // Only attempt one candidate.
		}
	}

	/* ---- Tally + progress line. --------------------------------------- */
	if ( $is_update ) {
		++$digicars_updated;
		$verb = 'updated';
	} else {
		++$digicars_created;
		$verb = 'created';
	}

	digicars_seed_log(
		sprintf(
			'  [%s] #%d %s - %s (%s, %s, R%s, from R%s p/m%s)',
			$verb,
			$product_id,
			$stock_no,
			$title,
			$v['condition'],
			$v['body'],
			number_format( $price, 0, '.', ' ' ),
			number_format( (int) $monthly, 0, '.', ' ' ),
			$is_featured ? ', featured' : ''
		)
	);
}

/* -------------------------------------------------------------------------
 * Summary.
 * ---------------------------------------------------------------------- */

digicars_seed_log(
	sprintf(
		'Digicars importer done: %d created, %d updated, %d total processed.',
		$digicars_created,
		$digicars_updated,
		$digicars_total
	)
);
