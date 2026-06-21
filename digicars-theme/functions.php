<?php
/**
 * Digicars theme core.
 *
 * Multi-brand South African car marketplace. Vehicles are WooCommerce
 * products with cart/checkout disabled — the funnel is enquiry + finance.
 * Vehicle attributes are deliberately REST-exposed so the Concierge and a
 * later integration can read the catalogue via the WP/WC REST API.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/* -------------------------------------------------------------------------
 * 1. Theme setup
 * ---------------------------------------------------------------------- */

/**
 * Register theme supports, menus, image sizes and the text domain.
 */
function digicars_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'automatic-feed-links' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary', 'digicars' ),
			'footer'  => __( 'Footer', 'digicars' ),
		)
	);

	add_image_size( 'digicars-card', 640, 420, true );

	load_theme_textdomain( 'digicars', get_theme_file_path( 'languages' ) );
}
add_action( 'after_setup_theme', 'digicars_setup' );

/* -------------------------------------------------------------------------
 * 2. Enqueue assets
 * ---------------------------------------------------------------------- */

/**
 * Enqueue fonts, styles and scripts.
 */
function digicars_enqueue_assets() {
	// Google Fonts — one combined request, with preconnects.
	wp_enqueue_style(
		'digicars-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo+Expanded:wght@700;800&family=Hanken+Grotesk:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap',
		array(),
		null
	);

	// Main stylesheet (style.css) — handle is the text domain.
	$style_path = get_stylesheet_directory() . '/style.css';
	wp_enqueue_style(
		'digicars',
		get_stylesheet_uri(),
		array( 'digicars-fonts' ),
		file_exists( $style_path ) ? filemtime( $style_path ) : null
	);

	// WooCommerce-specific styles only when WooCommerce is active.
	if ( class_exists( 'WooCommerce' ) ) {
		$wc_css_path = get_theme_file_path( 'css/woocommerce.css' );
		wp_enqueue_style(
			'digicars-woocommerce',
			get_theme_file_uri( 'css/woocommerce.css' ),
			array( 'digicars' ),
			file_exists( $wc_css_path ) ? filemtime( $wc_css_path ) : null
		);
	}

	// Scripts (footer), versioned by filemtime.
	$main_path          = get_theme_file_path( 'js/main.js' );
	$concierge_path     = get_theme_file_path( 'js/concierge.js' );
	$affordability_path = get_theme_file_path( 'js/affordability.js' );

	wp_enqueue_script(
		'digicars-main',
		get_theme_file_uri( 'js/main.js' ),
		array(),
		file_exists( $main_path ) ? filemtime( $main_path ) : null,
		true
	);
	wp_enqueue_script(
		'digicars-concierge',
		get_theme_file_uri( 'js/concierge.js' ),
		array( 'digicars-main' ),
		file_exists( $concierge_path ) ? filemtime( $concierge_path ) : null,
		true
	);
	wp_enqueue_script(
		'digicars-affordability',
		get_theme_file_uri( 'js/affordability.js' ),
		array( 'digicars-main' ),
		file_exists( $affordability_path ) ? filemtime( $affordability_path ) : null,
		true
	);

	// Hero neural-canvas — front page only, no dependencies.
	if ( is_front_page() ) {
		$hero_canvas_path = get_theme_file_path( 'js/hero-canvas.js' );
		wp_enqueue_script(
			'digicars-hero-canvas',
			get_theme_file_uri( 'js/hero-canvas.js' ),
			array(),
			file_exists( $hero_canvas_path ) ? filemtime( $hero_canvas_path ) : null,
			true
		);
	}

	wp_localize_script(
		'digicars-main',
		'digicarsData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'digicars_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'digicars_enqueue_assets' );

/**
 * Preconnect to the Google Fonts asset host.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type Relation type being fetched.
 * @return array
 */
function digicars_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && wp_style_is( 'digicars-fonts', 'enqueued' ) ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'digicars_resource_hints', 10, 2 );

/* -------------------------------------------------------------------------
 * 3. Taxonomies (registered on the `product` object type)
 * ---------------------------------------------------------------------- */

/**
 * Register the public vehicle taxonomies. Body type uses the built-in
 * `product_cat`, so it is deliberately not registered here.
 */
function digicars_register_taxonomies() {
	$taxonomies = array(
		'vehicle_make'      => array(
			'slug'   => 'make',
			'single' => __( 'Make', 'digicars' ),
			'plural' => __( 'Makes', 'digicars' ),
		),
		'vehicle_condition' => array(
			'slug'   => 'condition',
			'single' => __( 'Condition', 'digicars' ),
			'plural' => __( 'Conditions', 'digicars' ),
		),
		'vehicle_fuel'      => array(
			'slug'   => 'fuel',
			'single' => __( 'Fuel', 'digicars' ),
			'plural' => __( 'Fuel Types', 'digicars' ),
		),
		'vehicle_dealer'    => array(
			'slug'   => 'dealer',
			'single' => __( 'Dealer', 'digicars' ),
			'plural' => __( 'Dealers', 'digicars' ),
		),
	);

	foreach ( $taxonomies as $taxonomy => $args ) {
		register_taxonomy(
			$taxonomy,
			'product',
			array(
				'labels'            => array(
					'name'          => $args['plural'],
					'singular_name' => $args['single'],
					'menu_name'     => $args['plural'],
				),
				'public'            => true,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $args['slug'] ),
			)
		);
	}
}
add_action( 'init', 'digicars_register_taxonomies' );

/* -------------------------------------------------------------------------
 * 4. Vehicle meta registration — the REST / embedding substrate.
 * ---------------------------------------------------------------------- */

/**
 * Return the full vehicle meta definition map.
 *
 * Each entry: key => [ 'type' => string|number|integer|boolean|array ].
 * Every key is registered single + show_in_rest so the catalogue is fully
 * readable (and writable by editors) through the REST API.
 *
 * @return array
 */
function digicars_meta_definitions() {
	return array(
		// Identity.
		'_vehicle_make'            => array( 'type' => 'string' ),
		'_vehicle_model'           => array( 'type' => 'string' ),
		'_vehicle_variant'         => array( 'type' => 'string' ),
		'_vehicle_year'            => array( 'type' => 'integer' ),
		'_vehicle_body_type'       => array( 'type' => 'string' ),
		'_vehicle_condition'       => array( 'type' => 'string' ),
		'_vehicle_stock_no'        => array( 'type' => 'string' ),
		'_vehicle_vin'             => array( 'type' => 'string' ),

		// Pricing / finance.
		'_vehicle_price'           => array( 'type' => 'number' ),
		'_vehicle_monthly_from'    => array( 'type' => 'integer' ),
		'_vehicle_availability'    => array( 'type' => 'string' ),

		// Powertrain.
		'_vehicle_fuel'            => array( 'type' => 'string' ),
		'_vehicle_transmission'    => array( 'type' => 'string' ),
		'_vehicle_drivetrain'      => array( 'type' => 'string' ),
		'_vehicle_engine'          => array( 'type' => 'string' ),
		'_vehicle_power_kw'        => array( 'type' => 'integer' ),
		'_vehicle_fuel_economy'    => array( 'type' => 'number' ),
		'_vehicle_co2'             => array( 'type' => 'integer' ),
		'_vehicle_range_km'        => array( 'type' => 'integer' ),
		'_vehicle_battery_kwh'     => array( 'type' => 'number' ),

		// History.
		'_vehicle_mileage'         => array( 'type' => 'integer' ),
		'_vehicle_previous_owners' => array( 'type' => 'integer' ),
		'_vehicle_service_history' => array( 'type' => 'string' ),
		'_vehicle_service_plan'    => array( 'type' => 'string' ),
		'_vehicle_warranty'        => array( 'type' => 'string' ),

		// Practicality.
		'_vehicle_doors'           => array( 'type' => 'integer' ),
		'_vehicle_seats'           => array( 'type' => 'integer' ),
		'_vehicle_boot_litres'     => array( 'type' => 'integer' ),
		'_vehicle_towing_capacity' => array( 'type' => 'integer' ),
		'_vehicle_colour'          => array( 'type' => 'string' ),

		// Safety.
		'_vehicle_safety_rating'   => array( 'type' => 'integer' ),
		'_vehicle_features'        => array( 'type' => 'array' ),

		// Location.
		'_vehicle_dealer'          => array( 'type' => 'string' ),
		'_vehicle_province'        => array( 'type' => 'string' ),

		// Semantic.
		'_vehicle_lifestyle_tags'  => array( 'type' => 'array' ),
		'_vehicle_ai_summary'      => array( 'type' => 'string' ),
	);
}

/**
 * Register every vehicle meta key on the `product` post type.
 */
function digicars_register_meta() {
	$auth_callback = function () {
		return current_user_can( 'edit_products' );
	};

	foreach ( digicars_meta_definitions() as $key => $def ) {
		$args = array(
			'single'        => true,
			'type'          => $def['type'],
			'show_in_rest'  => true,
			'auth_callback' => $auth_callback,
		);

		// Arrays of strings need an explicit REST schema for their items.
		if ( 'array' === $def['type'] ) {
			$args['show_in_rest'] = array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'string',
					),
				),
			);
		}

		register_post_meta( 'product', $key, $args );
	}
}
add_action( 'init', 'digicars_register_meta' );

/* -------------------------------------------------------------------------
 * 5. WooCommerce global product attributes.
 *
 * These global attributes (pa_make … pa_drivetrain) power native
 * WooCommerce filtering AND are exposed through the WC REST API — this is
 * the integration seam for REST-based catalogue reads by the Concierge and
 * the later integration. Creation is idempotent and gated behind a one-time
 * option so it never runs on every front-end request.
 * ---------------------------------------------------------------------- */

/**
 * Ensure the global vehicle attributes exist.
 */
function digicars_register_global_attributes() {
	if ( ! function_exists( 'wc_create_attribute' ) ) {
		return;
	}

	// Only attempt in admin, or once via a flag, to avoid per-request work.
	if ( ! is_admin() && get_option( 'digicars_attributes_ready' ) ) {
		return;
	}

	$wanted = array(
		'make'         => __( 'Make', 'digicars' ),
		'model'        => __( 'Model', 'digicars' ),
		'body'         => __( 'Body', 'digicars' ),
		'condition'    => __( 'Condition', 'digicars' ),
		'fuel'         => __( 'Fuel', 'digicars' ),
		'transmission' => __( 'Transmission', 'digicars' ),
		'drivetrain'   => __( 'Drivetrain', 'digicars' ),
	);

	$existing = array();
	foreach ( wc_get_attribute_taxonomies() as $tax ) {
		$existing[ $tax->attribute_name ] = true;
	}

	$created = false;
	foreach ( $wanted as $slug => $label ) {
		if ( isset( $existing[ $slug ] ) ) {
			continue;
		}

		$result = wc_create_attribute(
			array(
				'name'         => $label,
				'slug'         => $slug,
				'type'         => 'select',
				'order_by'     => 'name',
				'has_archives' => false,
			)
		);

		if ( ! is_wp_error( $result ) ) {
			$created = true;
		}
	}

	if ( $created ) {
		// Flush the attribute taxonomy cache so pa_* taxonomies register.
		delete_transient( 'wc_attribute_taxonomies' );
	}

	update_option( 'digicars_attributes_ready', 1 );
}
add_action( 'init', 'digicars_register_global_attributes', 20 );

/* -------------------------------------------------------------------------
 * 6. Helper functions (real bodies — called throughout the theme).
 * ---------------------------------------------------------------------- */

/**
 * Body types map: slug => [ label, icon ].
 *
 * @return array
 */
function digicars_body_types(): array {
	$slugs = array(
		'suv'         => 'SUV',
		'hatch'       => 'Hatchback',
		'sedan'       => 'Sedan',
		'coupe'       => 'Coupé',
		'convertible' => 'Convertible',
		'double-cab'  => 'Double Cab',
		'single-cab'  => 'Single Cab',
		'mpv'         => 'MPV',
		'minibus'     => 'Minibus',
	);

	$types = array();
	foreach ( $slugs as $slug => $label ) {
		$types[ $slug ] = array(
			'label' => $label,
			'icon'  => get_theme_file_uri( 'images/icons/body-' . $slug . '.svg' ),
		);
	}

	return $types;
}

/**
 * Vehicle makes map: slug => Label.
 *
 * @return array
 */
function digicars_makes(): array {
	return array(
		'chery'      => 'Chery',
		'omoda'      => 'Omoda',
		'jaecoo'     => 'Jaecoo',
		'volkswagen' => 'Volkswagen',
		'ford'       => 'Ford',
		'nissan'     => 'Nissan',
		'renault'    => 'Renault',
		'mahindra'   => 'Mahindra',
		'suzuki'     => 'Suzuki',
		'tata'       => 'Tata',
		'gwm'        => 'GWM',
		'haval'      => 'Haval',
		'toyota'     => 'Toyota',
		'hyundai'    => 'Hyundai',
		'kia'        => 'Kia',
	);
}

/**
 * Thin wrapper over get_post_meta() for single vehicle meta.
 *
 * @param int    $id  Product ID.
 * @param string $key Meta key.
 * @return mixed
 */
function digicars_meta( int $id, string $key ) {
	return get_post_meta( $id, $key, true );
}

/**
 * Estimate a monthly instalment.
 *
 * This formula MUST stay in lock-step with the front-end JS implementation.
 *
 * @param float $price   Cash price.
 * @param float $deposit Deposit amount.
 * @param int   $term    Term in months.
 * @param float $rate    Annual interest rate (e.g. 0.115).
 * @param float $balloon Balloon as a fraction of price (e.g. 0.10).
 * @return int
 */
function digicars_monthly_from( float $price, float $deposit = 0, int $term = 72, float $rate = 0.115, float $balloon = 0 ): int {
	$financed = max( 0, $price - $deposit );
	$residual = $price * $balloon;            // balloon is a fraction, e.g. 0.10
	$i        = $rate / 12;                    // monthly rate
	$n        = max( 1, $term );
	if ( $i <= 0 ) {
		return (int) ceil( ( $financed - $residual ) / $n );
	}
	$factor  = pow( 1 + $i, $n );
	$monthly = ( $financed - $residual / $factor ) * ( $i / ( 1 - 1 / $factor ) );
	return (int) ceil( $monthly );
}

/**
 * Build the set of badges for a vehicle.
 *
 * @param WC_Product $product Product object.
 * @return array List of [ 'label' => string, 'tone' => string ].
 */
function digicars_vehicle_badges( WC_Product $product ): array {
	$badges = array();
	$id     = $product->get_id();

	$condition = strtolower( (string) digicars_meta( $id, '_vehicle_condition' ) );
	$fuel      = strtolower( (string) digicars_meta( $id, '_vehicle_fuel' ) );

	if ( '' !== $condition ) {
		if ( false !== strpos( $condition, 'new' ) ) {
			$badges[] = array(
				'label' => __( 'New', 'digicars' ),
				'tone'  => 'new',
			);
		} elseif ( false !== strpos( $condition, 'demo' ) ) {
			$badges[] = array(
				'label' => __( 'Demo', 'digicars' ),
				'tone'  => 'demo',
			);
		} elseif ( false !== strpos( $condition, 'used' ) || false !== strpos( $condition, 'pre' ) ) {
			$badges[] = array(
				'label' => __( 'Used', 'digicars' ),
				'tone'  => 'used',
			);
		}
	}

	if ( false !== strpos( $fuel, 'electric' ) ) {
		$badges[] = array(
			'label' => __( 'EV', 'digicars' ),
			'tone'  => 'ev',
		);
	}

	if ( $product->is_featured() ) {
		$badges[] = array(
			'label' => __( 'Featured', 'digicars' ),
			'tone'  => 'featured',
		);
	}

	return $badges;
}

/**
 * Accessible star rating markup for a 0–5 score.
 *
 * @param float $rating Rating between 0 and 5.
 * @return string
 */
function digicars_stars( float $rating ): string {
	$rating = max( 0.0, min( 5.0, $rating ) );
	$filled = (int) round( $rating );

	$label = sprintf(
		/* translators: %s: star rating out of 5. */
		__( '%s out of 5 stars', 'digicars' ),
		number_format_i18n( $rating, ( floor( $rating ) === $rating ) ? 0 : 1 )
	);

	$out = '<span class="digicars-stars" role="img" aria-label="' . esc_attr( $label ) . '">';
	for ( $star = 1; $star <= 5; $star++ ) {
		$state = ( $star <= $filled ) ? 'is-filled' : 'is-empty';
		$glyph = ( $star <= $filled ) ? '★' : '☆';
		$out  .= '<span class="digicars-star ' . esc_attr( $state ) . '" aria-hidden="true">' . $glyph . '</span>';
	}
	$out .= '</span>';

	return $out;
}

/**
 * Concierge quick-filter chips: slug => [ label, query ].
 *
 * The `query` arrays are the contract consumed by the AJAX handler.
 *
 * @return array
 */
function digicars_concierge_chips(): array {
	return array(
		'under-5k-pm'     => array(
			'label' => __( 'Under R5 000 p/m', 'digicars' ),
			'query' => array( 'max_monthly' => 5000 ),
		),
		'family-suv'      => array(
			'label' => __( 'Family SUV', 'digicars' ),
			'query' => array(
				'body' => 'suv',
				'tags' => 'family',
			),
		),
		'first-car'       => array(
			'label' => __( 'First car', 'digicars' ),
			'query' => array(
				'tags'        => 'first-car',
				'max_monthly' => 3500,
			),
		),
		'ev'              => array(
			'label' => __( 'Electric', 'digicars' ),
			'query' => array( 'fuel' => 'electric' ),
		),
		'bakkie-for-work' => array(
			'label' => __( 'Bakkie for work', 'digicars' ),
			'query' => array(
				'body' => 'double-cab',
				'tags' => 'fleet',
			),
		),
		'trade-in'        => array(
			'label' => __( 'Trade-in / commuter', 'digicars' ),
			'query' => array( 'tags' => 'commuter' ),
		),
	);
}

/**
 * Build (or return cached) a factual natural-language vehicle description.
 *
 * @param int $id Product ID.
 * @return string
 */
function digicars_build_ai_summary( int $id ): string {
	$existing = (string) digicars_meta( $id, '_vehicle_ai_summary' );
	if ( '' !== trim( $existing ) ) {
		return $existing;
	}

	$year      = (int) digicars_meta( $id, '_vehicle_year' );
	$make      = (string) digicars_meta( $id, '_vehicle_make' );
	$model     = (string) digicars_meta( $id, '_vehicle_model' );
	$variant   = (string) digicars_meta( $id, '_vehicle_variant' );
	$condition = (string) digicars_meta( $id, '_vehicle_condition' );
	$body      = (string) digicars_meta( $id, '_vehicle_body_type' );
	$fuel      = (string) digicars_meta( $id, '_vehicle_fuel' );
	$trans     = (string) digicars_meta( $id, '_vehicle_transmission' );
	$mileage   = (int) digicars_meta( $id, '_vehicle_mileage' );
	$seats     = (int) digicars_meta( $id, '_vehicle_seats' );
	$price     = (float) digicars_meta( $id, '_vehicle_price' );
	$monthly   = (int) digicars_meta( $id, '_vehicle_monthly_from' );
	$features  = digicars_meta( $id, '_vehicle_features' );
	$tags      = digicars_meta( $id, '_vehicle_lifestyle_tags' );

	if ( 0 === $monthly && $price > 0 ) {
		$monthly = digicars_monthly_from( $price );
	}

	// Sentence 1 — identity.
	$name_parts = array_filter(
		array(
			$year ? (string) $year : '',
			$make,
			$model,
			$variant,
		),
		static function ( $part ) {
			return '' !== trim( (string) $part );
		}
	);
	$vehicle_name = trim( implode( ' ', $name_parts ) );
	if ( '' === $vehicle_name ) {
		$vehicle_name = __( 'This vehicle', 'digicars' );
	}

	$descriptors = array();
	if ( '' !== trim( $condition ) ) {
		$descriptors[] = strtolower( $condition );
	}
	if ( '' !== trim( $fuel ) ) {
		$descriptors[] = strtolower( $fuel ) . '-powered';
	}
	if ( '' !== trim( $body ) ) {
		$descriptors[] = strtolower( $body );
	}

	if ( $descriptors ) {
		$sentence1 = sprintf(
			/* translators: 1: vehicle name, 2: descriptor phrase. */
			__( 'The %1$s is a %2$s.', 'digicars' ),
			$vehicle_name,
			trim( implode( ' ', $descriptors ) )
		);
	} else {
		$sentence1 = sprintf(
			/* translators: %s: vehicle name. */
			__( 'The %s.', 'digicars' ),
			$vehicle_name
		);
	}

	// Sentence 2 — specifics.
	$specs = array();
	if ( '' !== trim( $trans ) ) {
		$specs[] = sprintf(
			/* translators: %s: transmission type. */
			__( 'a %s gearbox', 'digicars' ),
			strtolower( $trans )
		);
	}
	if ( $seats > 0 ) {
		$specs[] = sprintf(
			/* translators: %d: seat count. */
			_n( '%d seat', '%d seats', $seats, 'digicars' ),
			$seats
		);
	}
	if ( $mileage > 0 ) {
		$specs[] = sprintf(
			/* translators: %s: formatted mileage. */
			__( '%s km on the clock', 'digicars' ),
			number_format_i18n( $mileage )
		);
	}
	if ( is_array( $features ) && ! empty( $features ) ) {
		$first_feature = trim( (string) reset( $features ) );
		if ( '' !== $first_feature ) {
			$specs[] = strtolower( $first_feature );
		}
	}

	$sentence2 = '';
	if ( $specs ) {
		$sentence2 = sprintf(
			/* translators: %s: comma-joined specification list. */
			__( 'It offers %s.', 'digicars' ),
			digicars_join_list( $specs )
		);
	}

	// Sentence 3 — pricing.
	$sentence3 = '';
	if ( $price > 0 ) {
		if ( $monthly > 0 ) {
			$sentence3 = sprintf(
				/* translators: 1: cash price, 2: monthly instalment. */
				__( 'Priced at R%1$s, it is available from R%2$s per month.', 'digicars' ),
				number_format_i18n( $price ),
				number_format_i18n( $monthly )
			);
		} else {
			$sentence3 = sprintf(
				/* translators: %s: cash price. */
				__( 'Priced at R%s.', 'digicars' ),
				number_format_i18n( $price )
			);
		}
	}

	// Sentence 4 — lifestyle angle.
	$sentence4 = '';
	if ( is_array( $tags ) && ! empty( $tags ) ) {
		$clean_tags = array_filter(
			array_map(
				static function ( $tag ) {
					return strtolower( trim( str_replace( '-', ' ', (string) $tag ) ) );
				},
				$tags
			)
		);
		if ( $clean_tags ) {
			$sentence4 = sprintf(
				/* translators: %s: lifestyle suitability list. */
				__( 'Well suited to %s.', 'digicars' ),
				digicars_join_list( array_values( $clean_tags ) )
			);
		}
	}

	$summary = trim(
		implode(
			' ',
			array_filter( array( $sentence1, $sentence2, $sentence3, $sentence4 ) )
		)
	);

	return $summary;
}

/**
 * Join a list into a human-readable string ("a, b and c").
 *
 * @param array $items List of strings.
 * @return string
 */
function digicars_join_list( array $items ): string {
	$items = array_values( array_filter( array_map( 'trim', $items ) ) );
	$count = count( $items );

	if ( 0 === $count ) {
		return '';
	}
	if ( 1 === $count ) {
		return $items[0];
	}

	$last = array_pop( $items );
	return implode( ', ', $items ) . ' ' . __( 'and', 'digicars' ) . ' ' . $last;
}

if ( ! function_exists( 'digicars_post_card' ) ) {
	/**
	 * Echo a Car Torque blog post card (.post-card) for a given post.
	 *
	 * Markup matches the inline cards in index.php so the helper stays visually
	 * consistent across home.php, archive.php and the homepage Car Torque
	 * section. Everything is escaped; the post defaults to the current global.
	 *
	 * @param WP_Post|null $post Optional post object. Defaults to the global $post.
	 * @return void
	 */
	function digicars_post_card( $post = null ): void {
		$post = get_post( $post );
		if ( ! $post ) {
			return;
		}

		$id        = $post->ID;
		$permalink = get_permalink( $id );
		$cats      = get_the_category( $id );

		// Reading time (best-effort, optional).
		$content      = get_post_field( 'post_content', $id );
		$word_count   = str_word_count( wp_strip_all_tags( (string) $content ) );
		$reading_time = max( 1, (int) ceil( $word_count / 200 ) );

		$excerpt = get_the_excerpt( $id );
		$excerpt = wp_trim_words( $excerpt, 22 );
		?>
		<li class="post-card">
			<?php if ( has_post_thumbnail( $id ) ) : ?>
				<a class="post-card__media" href="<?php echo esc_url( $permalink ); ?>"><?php echo get_the_post_thumbnail( $id, 'digicars-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endif; ?>
			<div class="post-card__body stack-sm">
				<?php if ( ! empty( $cats ) ) : ?>
					<span class="eyebrow"><?php echo esc_html( $cats[0]->name ); ?></span>
				<?php endif; ?>
				<h2 class="t-3"><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a></h2>
				<p class="muted t-mono post-card__meta">
					<?php echo esc_html( get_the_date( '', $id ) ); ?>
					<span aria-hidden="true">&middot;</span>
					<?php
					printf(
						/* translators: %d: estimated reading time in minutes. */
						esc_html( _n( '%d min read', '%d min read', $reading_time, 'digicars' ) ),
						(int) $reading_time
					);
					?>
				</p>
				<?php if ( '' !== trim( $excerpt ) ) : ?>
					<p><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
				<a class="link-arrow" href="<?php echo esc_url( $permalink ); ?>"><?php esc_html_e( 'Read', 'digicars' ); ?></a>
			</div>
		</li>
		<?php
	}
}

/* -------------------------------------------------------------------------
 * 7. Disable WooCommerce commerce — enquiry + finance funnel only.
 * ---------------------------------------------------------------------- */

// Nothing is purchasable.
add_filter( 'woocommerce_is_purchasable', '__return_false' );

/**
 * Remove add-to-cart from loop and single product summaries.
 */
function digicars_strip_add_to_cart() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
}
add_action( 'init', 'digicars_strip_add_to_cart' );

/**
 * Bounce cart and checkout pages back to the homepage.
 */
function digicars_block_commerce_pages() {
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}
add_action( 'template_redirect', 'digicars_block_commerce_pages' );

// We render our own shop page title.
add_filter( 'woocommerce_show_page_title', '__return_false' );

// 12 vehicles per archive page.
add_filter(
	'loop_shop_per_page',
	function () {
		return 12;
	},
	20
);

/**
 * Use a › breadcrumb delimiter.
 *
 * @param array $defaults Breadcrumb defaults.
 * @return array
 */
function digicars_breadcrumb_defaults( $defaults ) {
	$defaults['delimiter'] = ' <span class="breadcrumb-sep">&rsaquo;</span> ';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'digicars_breadcrumb_defaults' );

/**
 * Remove default WooCommerce loop chrome we render ourselves.
 */
function digicars_strip_default_loop_chrome() {
	// Sale flash on loop and single.
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

	// Default sidebar.
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	// Result count + catalog ordering (we render custom controls).
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
}
add_action( 'init', 'digicars_strip_default_loop_chrome' );

// Tell WooCommerce to render 1 column so it adds `columns-1` to ul.products,
// which makes WC's own CSS write width:100% on each li.product.
// Our CSS grid in style.css then controls the actual column count visually.
add_filter( 'loop_shop_columns', function() { return 1; } );

/* -------------------------------------------------------------------------
 * 7b. Faceted catalogue — server-side filter handling.
 *
 * One pre_get_posts handler reads the catalogue GET params rendered by
 * archive-product.php and translates them into meta_query + tax_query +
 * ordering on the MAIN product-archive query. It is intentionally
 * self-contained, runs only on the front-end main archive query, and leaves
 * admin and every other query untouched.
 *
 * GET params consumed:
 *   condition, make, body, fuel, dealer        → tax_query
 *   search_by (price|monthly), price_min/max,
 *   pm_min/pm_max, year_min/max, km_max,
 *   transmission, province                      → meta_query
 *   orderby                                      → ordering
 * ---------------------------------------------------------------------- */

/**
 * Apply the catalogue facets to the main product-archive query.
 *
 * @param WP_Query $q The query being prepared.
 * @return void
 */
function digicars_apply_catalogue_filters( \WP_Query $q ): void {
	// Only touch the front-end main query on a product archive / vehicle tax.
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}

	$is_archive = false;
	if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'product' ) ) {
		$is_archive = true;
	} elseif ( function_exists( 'is_tax' ) && is_tax(
		array( 'product_cat', 'vehicle_make', 'vehicle_condition', 'vehicle_fuel', 'vehicle_dealer' )
	) ) {
		$is_archive = true;
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		$is_archive = true;
	}

	if ( ! $is_archive ) {
		return;
	}

	// Helper closures for reading sanitized params (no nonce — public GET facets).
	$get_int = static function ( $key ) {
		return isset( $_GET[ $key ] ) && '' !== $_GET[ $key ] // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			? intval( wp_unslash( $_GET[ $key ] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			: null;
	};
	$get_str = static function ( $key ) {
		return isset( $_GET[ $key ] ) && '' !== $_GET[ $key ] // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			: '';
	};

	/* ------- meta_query --------------------------------------------------- */
	$meta_query = array();

	$search_by = ( 'monthly' === $get_str( 'search_by' ) ) ? 'monthly' : 'price';

	if ( 'monthly' === $search_by ) {
		// Monthly instalment range on _vehicle_monthly_from.
		$pm_min = $get_int( 'pm_min' );
		$pm_max = $get_int( 'pm_max' );
		if ( null !== $pm_min && null !== $pm_max ) {
			$meta_query[] = array(
				'key'     => '_vehicle_monthly_from',
				'value'   => array( $pm_min, $pm_max ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			);
		} elseif ( null !== $pm_min ) {
			$meta_query[] = array(
				'key'     => '_vehicle_monthly_from',
				'value'   => $pm_min,
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		} elseif ( null !== $pm_max ) {
			$meta_query[] = array(
				'key'     => '_vehicle_monthly_from',
				'value'   => $pm_max,
				'type'    => 'NUMERIC',
				'compare' => '<=',
			);
		}
	} else {
		// Cash-price range on _vehicle_price.
		$price_min = $get_int( 'price_min' );
		$price_max = $get_int( 'price_max' );
		if ( null !== $price_min && null !== $price_max ) {
			$meta_query[] = array(
				'key'     => '_vehicle_price',
				'value'   => array( $price_min, $price_max ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			);
		} elseif ( null !== $price_min ) {
			$meta_query[] = array(
				'key'     => '_vehicle_price',
				'value'   => $price_min,
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		} elseif ( null !== $price_max ) {
			$meta_query[] = array(
				'key'     => '_vehicle_price',
				'value'   => $price_max,
				'type'    => 'NUMERIC',
				'compare' => '<=',
			);
		}
	}

	// Year range on _vehicle_year.
	$year_min = $get_int( 'year_min' );
	$year_max = $get_int( 'year_max' );
	if ( null !== $year_min && null !== $year_max ) {
		$meta_query[] = array(
			'key'     => '_vehicle_year',
			'value'   => array( $year_min, $year_max ),
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN',
		);
	} elseif ( null !== $year_min ) {
		$meta_query[] = array(
			'key'     => '_vehicle_year',
			'value'   => $year_min,
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	} elseif ( null !== $year_max ) {
		$meta_query[] = array(
			'key'     => '_vehicle_year',
			'value'   => $year_max,
			'type'    => 'NUMERIC',
			'compare' => '<=',
		);
	}

	// Maximum mileage on _vehicle_mileage.
	$km_max = $get_int( 'km_max' );
	if ( null !== $km_max ) {
		$meta_query[] = array(
			'key'     => '_vehicle_mileage',
			'value'   => $km_max,
			'type'    => 'NUMERIC',
			'compare' => '<=',
		);
	}

	// Transmission (exact).
	$transmission = $get_str( 'transmission' );
	if ( '' !== $transmission ) {
		$meta_query[] = array(
			'key'     => '_vehicle_transmission',
			'value'   => $transmission,
			'compare' => '=',
		);
	}

	// Province (exact).
	$province = $get_str( 'province' );
	if ( '' !== $province ) {
		$meta_query[] = array(
			'key'     => '_vehicle_province',
			'value'   => $province,
			'compare' => '=',
		);
	}

	if ( ! empty( $meta_query ) ) {
		if ( count( $meta_query ) > 1 ) {
			$meta_query['relation'] = 'AND';
		}
		// Merge with any existing meta_query rather than clobbering it.
		$existing = $q->get( 'meta_query' );
		if ( ! empty( $existing ) && is_array( $existing ) ) {
			$meta_query = array(
				'relation' => 'AND',
				$existing,
				$meta_query,
			);
		}
		$q->set( 'meta_query', $meta_query );
	}

	/* ------- tax_query ---------------------------------------------------- */
	$tax_query = array();

	$facet_taxonomies = array(
		'condition' => 'vehicle_condition',
		'make'      => 'vehicle_make',
		'body'      => 'product_cat',
		'fuel'      => 'vehicle_fuel',
		'dealer'    => 'vehicle_dealer',
	);

	foreach ( $facet_taxonomies as $param => $taxonomy ) {
		$slug = $get_str( $param );
		if ( '' === $slug ) {
			continue;
		}
		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => array( sanitize_title( $slug ) ),
		);
	}

	if ( ! empty( $tax_query ) ) {
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		$existing = $q->get( 'tax_query' );
		if ( ! empty( $existing ) && is_array( $existing ) ) {
			$tax_query = array(
				'relation' => 'AND',
				$existing,
				$tax_query,
			);
		}
		$q->set( 'tax_query', $tax_query );
	}

	/* ------- ordering ----------------------------------------------------- */
	$orderby = $get_str( 'orderby' );
	switch ( $orderby ) {
		case 'price':
			$q->set( 'orderby', 'meta_value_num' );
			$q->set( 'meta_key', '_vehicle_price' );
			$q->set( 'order', 'ASC' );
			break;
		case 'price-desc':
			$q->set( 'orderby', 'meta_value_num' );
			$q->set( 'meta_key', '_vehicle_price' );
			$q->set( 'order', 'DESC' );
			break;
		case 'date':
			$q->set( 'orderby', 'date' );
			$q->set( 'order', 'DESC' );
			break;
		// 'menu_order', 'popularity', 'rating' and the default are left to
		// WooCommerce's own ordering handler.
	}
}
add_action( 'pre_get_posts', 'digicars_apply_catalogue_filters' );

/* -------------------------------------------------------------------------
 * 8. AJAX handlers.
 * ---------------------------------------------------------------------- */

/**
 * Concierge matcher.
 *
 * Request:  chips[] (slugs), text (string), budget_monthly (int).
 * Response: success { count, ids, cards_html }.
 *
 * This request/response shape is the documented seam a later integration
 * replaces — keep it stable.
 */
function digicars_ajax_concierge_match() {
	check_ajax_referer( 'digicars_nonce', 'nonce' );

	$chip_slugs = array();
	if ( isset( $_POST['chips'] ) ) {
		$raw_chips  = wp_unslash( $_POST['chips'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$raw_chips  = is_array( $raw_chips ) ? $raw_chips : array( $raw_chips );
		$chip_slugs = array_map( 'sanitize_key', $raw_chips );
	}

	$text   = isset( $_POST['text'] ) ? sanitize_text_field( wp_unslash( $_POST['text'] ) ) : '';
	$budget = isset( $_POST['budget_monthly'] ) ? absint( $_POST['budget_monthly'] ) : 0;

	// Accumulate constraints from chips.
	$max_monthly = $budget > 0 ? $budget : 0;
	$body_slugs  = array();
	$fuel_slugs  = array();
	$tag_slugs   = array();
	$all_chips   = digicars_concierge_chips();

	foreach ( $chip_slugs as $slug ) {
		if ( ! isset( $all_chips[ $slug ]['query'] ) ) {
			continue;
		}
		$query = $all_chips[ $slug ]['query'];

		if ( isset( $query['max_monthly'] ) ) {
			$value = (int) $query['max_monthly'];
			// Take the tightest (lowest) monthly cap.
			$max_monthly = ( 0 === $max_monthly ) ? $value : min( $max_monthly, $value );
		}
		if ( isset( $query['body'] ) ) {
			$body_slugs[] = sanitize_title( $query['body'] );
		}
		if ( isset( $query['fuel'] ) ) {
			$fuel_slugs[] = sanitize_title( $query['fuel'] );
		}
		if ( isset( $query['tags'] ) ) {
			$tag_slugs = array_merge( $tag_slugs, (array) $query['tags'] );
		}
	}

	// Free-text keyword matching against make / model / body.
	$keyword_ids = array();
	if ( '' !== $text ) {
		$keyword_ids = digicars_keyword_match_ids( $text, $body_slugs );
	}

	// Build the wc_get_products query.
	$args = array(
		'status'  => 'publish',
		'limit'   => 6,
		'return'  => 'ids',
		'orderby' => 'date',
		'order'   => 'DESC',
	);

	$meta_query = array();
	if ( $max_monthly > 0 ) {
		$meta_query[] = array(
			'key'     => '_vehicle_monthly_from',
			'value'   => $max_monthly,
			'compare' => '<=',
			'type'    => 'NUMERIC',
		);
	}
	if ( ! empty( $meta_query ) ) {
		$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery
	}

	$tax_query = array();
	if ( ! empty( $body_slugs ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array_values( array_unique( $body_slugs ) ),
		);
	}
	if ( ! empty( $fuel_slugs ) ) {
		$tax_query[] = array(
			'taxonomy' => 'vehicle_fuel',
			'field'    => 'slug',
			'terms'    => array_values( array_unique( $fuel_slugs ) ),
		);
	}
	if ( ! empty( $tax_query ) ) {
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
	}

	// Lifestyle tags constrain via meta LIKE on the serialized array.
	if ( ! empty( $tag_slugs ) ) {
		$tag_meta = array( 'relation' => 'OR' );
		foreach ( array_unique( $tag_slugs ) as $tag ) {
			$tag_meta[] = array(
				'key'     => '_vehicle_lifestyle_tags',
				'value'   => sanitize_title( $tag ),
				'compare' => 'LIKE',
			);
		}
		if ( isset( $args['meta_query'] ) ) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				$args['meta_query'],
				$tag_meta,
			);
		} else {
			$args['meta_query'] = $tag_meta; // phpcs:ignore WordPress.DB.SlowDBQuery
		}
	}

	$ids = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$ids = wc_get_products( $args );
	}

	// Merge keyword matches in front, de-duplicate, cap at 6.
	if ( ! empty( $keyword_ids ) ) {
		$ids = array_merge( $keyword_ids, $ids );
	}
	$ids   = array_values( array_unique( array_map( 'intval', $ids ) ) );
	$ids   = array_slice( $ids, 0, 6 );
	$total = count( $ids );

	// Buffer the product card template for each result.
	$html = '';
	if ( function_exists( 'wc_get_template_part' ) && function_exists( 'wc_get_product' ) ) {
		global $post, $product;
		$prev_post    = $post;
		$prev_product = $product;

		foreach ( $ids as $id ) {
			$the_post = get_post( $id );
			if ( ! $the_post ) {
				continue;
			}
			$post = $the_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			setup_postdata( $post );
			$product = wc_get_product( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride

			ob_start();
			wc_get_template_part( 'content', 'product' );
			$html .= ob_get_clean();
		}

		$post    = $prev_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$product = $prev_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		wp_reset_postdata();
	}

	wp_send_json_success(
		array(
			'count'      => $total,
			'ids'        => $ids,
			'cards_html' => $html,
		)
	);
}
add_action( 'wp_ajax_digicars_concierge_match', 'digicars_ajax_concierge_match' );
add_action( 'wp_ajax_nopriv_digicars_concierge_match', 'digicars_ajax_concierge_match' );

/**
 * Resolve free-text into matching product IDs by make / model / body.
 *
 * @param string $text       Raw search text.
 * @param array  $body_slugs Body slugs already implied by chips (context).
 * @return array Product IDs.
 */
function digicars_keyword_match_ids( string $text, array $body_slugs = array() ): array {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$text  = strtolower( $text );
	$words = preg_split( '/[\s,]+/', $text, -1, PREG_SPLIT_NO_EMPTY );
	if ( empty( $words ) ) {
		return array();
	}

	$makes      = digicars_makes();
	$body_types = digicars_body_types();

	$matched_makes  = array();
	$matched_bodies = $body_slugs;
	$free_words     = array();

	foreach ( $words as $word ) {
		$found = false;
		foreach ( $makes as $slug => $label ) {
			if ( $word === $slug || $word === strtolower( $label ) ) {
				$matched_makes[] = $label;
				$found           = true;
				break;
			}
		}
		if ( $found ) {
			continue;
		}
		foreach ( $body_types as $slug => $info ) {
			if ( $word === $slug || $word === strtolower( $info['label'] ) ) {
				$matched_bodies[] = $slug;
				$found            = true;
				break;
			}
		}
		if ( ! $found ) {
			$free_words[] = $word;
		}
	}

	// Nothing concrete to match on.
	if ( empty( $matched_makes ) && empty( $matched_bodies ) && empty( $free_words ) ) {
		return array();
	}

	$args = array(
		'status' => 'publish',
		'limit'  => 6,
		'return' => 'ids',
	);

	if ( ! empty( $matched_makes ) ) {
		$args['meta_query'] = array( 'relation' => 'OR' ); // phpcs:ignore WordPress.DB.SlowDBQuery
		foreach ( array_unique( $matched_makes ) as $make ) {
			$args['meta_query'][] = array(
				'key'     => '_vehicle_make',
				'value'   => $make,
				'compare' => 'LIKE',
			);
		}
	}

	if ( ! empty( $matched_bodies ) ) {
		$args['category'] = array_values( array_unique( $matched_bodies ) );
	}

	// Remaining free words search model + title.
	if ( ! empty( $free_words ) ) {
		$args['s'] = implode( ' ', $free_words );
	}

	$ids = wc_get_products( $args );

	return array_map( 'intval', (array) $ids );
}

/**
 * Enquiry / lead capture handler.
 *
 * Validates and emails the enquiry best-effort, then returns a friendly
 * confirmation. Validation failures return wp_send_json_error.
 */
function digicars_ajax_enquiry() {
	check_ajax_referer( 'digicars_nonce', 'nonce' );

	$name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$topic      = isset( $_POST['topic'] ) ? sanitize_text_field( wp_unslash( $_POST['topic'] ) ) : '';
	$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$vehicle    = isset( $_POST['vehicle'] ) ? sanitize_text_field( wp_unslash( $_POST['vehicle'] ) ) : '';
	$vehicle_id = isset( $_POST['vehicle_id'] ) ? absint( $_POST['vehicle_id'] ) : 0;

	// Validation: a name plus at least one contact method.
	$errors = array();
	if ( '' === $name ) {
		$errors[] = __( 'Please tell us your name.', 'digicars' );
	}
	if ( '' === $email && '' === $phone ) {
		$errors[] = __( 'Please provide an email address or phone number.', 'digicars' );
	}
	if ( '' !== $email && ! is_email( $email ) ) {
		$errors[] = __( 'That email address does not look right.', 'digicars' );
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error(
			array(
				'ok'      => false,
				'message' => implode( ' ', $errors ),
				'errors'  => $errors,
			)
		);
	}

	$lines = array(
		sprintf( 'Name: %s', $name ),
		sprintf( 'Email: %s', $email ),
		sprintf( 'Phone: %s', $phone ),
		sprintf( 'Topic: %s', $topic ),
		sprintf( 'Vehicle: %s', $vehicle ),
		sprintf( 'Vehicle ID: %s', $vehicle_id ? $vehicle_id : '—' ),
		'',
		'Message:',
		$message,
	);

	$subject = sprintf(
		/* translators: %s: enquiry topic or default. */
		__( 'Digicars enquiry: %s', 'digicars' ),
		'' !== $topic ? $topic : __( 'New lead', 'digicars' )
	);

	// Best-effort delivery — do not block the user on mail failure.
	wp_mail( get_option( 'admin_email' ), $subject, implode( "\n", $lines ) );

	wp_send_json_success(
		array(
			'ok'      => true,
			'message' => __( 'Thanks — a Digicars consultant will be in touch within one working day.', 'digicars' ),
		)
	);
}
add_action( 'wp_ajax_digicars_enquiry', 'digicars_ajax_enquiry' );
add_action( 'wp_ajax_nopriv_digicars_enquiry', 'digicars_ajax_enquiry' );

/* -------------------------------------------------------------------------
 * 9. Finish — load the SEO module (the integration's other seam).
 * ---------------------------------------------------------------------- */

$digicars_seo = get_theme_file_path( 'inc/seo.php' );
if ( file_exists( $digicars_seo ) ) {
	require_once $digicars_seo;
}

if ( is_admin() ) {
	$digicars_import = get_theme_file_path( 'inc/admin-import.php' );
	if ( file_exists( $digicars_import ) ) {
		require_once $digicars_import;
	}
}
