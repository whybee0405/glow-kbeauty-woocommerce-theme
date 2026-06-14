<?php
/**
 * Glow K-Beauty — SEO: meta descriptions, Open Graph and JSON-LD.
 *
 * Every meta description on this site maps to the search intent of one
 * of five documented personas:
 *
 * 1. THE ROUTINE BUILDER (25–34, new to K-beauty, wants a guided start)
 *    Searches: "korean skincare routine south africa", "k-beauty routine
 *    order", "skincare routine for beginners".
 *    Served by: homepage hero + routine rail, routine-step categories.
 *    → The homepage meta description targets this persona.
 *
 * 2. THE INGREDIENT ANALYST (researches actives before buying)
 *    Searches: "snail mucin serum south africa", "niacinamide for pores",
 *    "centella asiatica products".
 *    Served by: product pages (key actives lead the meta description,
 *    Product schema carries brand/offer/aggregateRating), the homepage
 *    Ingredient Index.
 *
 * 3. THE BUSY MINIMALIST (35–45 professional, wants efficiency)
 *    Searches: "simple korean skincare routine", "quick skincare routine".
 *    Served by: concern tiles on the homepage, category archives with
 *    concise meta copy and fast filtering.
 *    → Category meta descriptions target this persona.
 *
 * 4. THE GIFT SHOPPER (buying for someone else, needs curation + trust)
 *    Searches: "k-beauty gift set south africa", "skincare gifts for her".
 *    Served by: best sellers, named-and-located reviews, the About page,
 *    Organization schema.
 *    → The About page meta description targets this persona.
 *
 * 5. THE SENSITIVE-SKIN SCEPTIC (reactive skin, burnt before)
 *    Searches: "fragrance free korean skincare", "sensitive skin k-beauty".
 *    Served by: FAQ page with FAQPage schema, skin-type archives, the
 *    full-disclosure accordion on product pages, patch-test guidance.
 *    → The FAQ/Help meta description targets this persona.
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

/* --------------------------------------------------------------------------
 * FAQ content — one source feeds both the Help page and FAQPage schema,
 * so the two can never drift apart.
 * ------------------------------------------------------------------------ */

/**
 * @return array[] Each item: question, answer (plain text, schema-safe).
 */
function glow_faq_items() {
	return array(
		array(
			'question' => __( 'How do I know your products are authentic?', 'glow-kbeauty' ),
			'answer'   => __( 'Every product we sell is sourced directly from the brand or its licensed Korean distributor — never from grey-market consolidators. Each shipment arrives with batch documentation, and we keep the batch number of every unit we sell on file. If you ever want the paperwork for something you bought, email us the order number and we will send it.', 'glow-kbeauty' ),
		),
		array(
			'question' => __( 'How do I find fragrance-free products?', 'glow-kbeauty' ),
			'answer'   => __( 'Use the Sensitive skin-type filter on any shop page — everything tagged sensitive is either fragrance-free or uses only trace functional fragrance, and the product page says which. Every product also lists its key actives up front, so you can rule things out before you click.', 'glow-kbeauty' ),
		),
		array(
			'question' => __( 'How should I patch test a new product?', 'glow-kbeauty' ),
			'answer'   => __( 'Apply a small amount to the inside of your forearm or behind your ear once a day for three days. If nothing flares, try it on a small area of your face for another two or three nights before using it fully. Introduce one new product at a time — if two new things go on together and your skin reacts, you will not know which one did it.', 'glow-kbeauty' ),
		),
		array(
			'question' => __( 'How long does delivery take?', 'glow-kbeauty' ),
			'answer'   => __( 'Orders ship from Johannesburg within one working day. Gauteng usually receives in 1–3 working days, Western Cape and KwaZulu-Natal in 2–4, and regional or outlying areas in 3–5. You will get a tracking number the moment your parcel is collected. Shipping is free over R500.', 'glow-kbeauty' ),
		),
		array(
			'question' => __( 'What is your returns policy if my skin reacts?', 'glow-kbeauty' ),
			'answer'   => __( 'Unopened products can be returned within 30 days for a full refund. If a product causes a genuine reaction, contact us within 14 days of delivery — we will refund or swap it even if it is opened, because a patch test cannot catch everything. We may ask for a photo and the batch number so we can flag it with the brand.', 'glow-kbeauty' ),
		),
		array(
			'question' => __( 'What order do I apply products in?', 'glow-kbeauty' ),
			'answer'   => __( 'Thinnest to thickest, water before oil: cleanse, exfoliate (2–3 times a week, not daily), tone, treat with serums or ampoules, moisturise, eye cream, and SPF every morning — including cloudy Joburg winter mornings. Our whole shop is organised in this order, so if you shop the steps left to right you cannot get the sequence wrong.', 'glow-kbeauty' ),
		),
		array(
			'question' => __( 'Do you ship outside South Africa?', 'glow-kbeauty' ),
			'answer'   => __( 'Not yet. We currently deliver anywhere in South Africa via tracked courier. Namibia and Botswana are on the roadmap — join the newsletter and you will hear about it the month it happens.', 'glow-kbeauty' ),
		),
	);
}

/* --------------------------------------------------------------------------
 * Meta description + keywords + Open Graph (wp_head priority 1)
 * ------------------------------------------------------------------------ */

/**
 * Pick the meta description for the current context. Each branch is
 * written for one persona\'s search intent (see header docblock).
 */
function glow_meta_description() {
	// Persona 1 — The Routine Builder.
	if ( is_front_page() ) {
		return __( 'Build your Korean skincare routine in the right order — cleanse to SPF, seven steps, zero guesswork. Authentic K-beauty, batch-verified and shipped across South Africa.', 'glow-kbeauty' );
	}

	// Persona 2 — The Ingredient Analyst.
	if ( glow_wc_active() && is_product() ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			$actives = glow_meta( $product->get_id(), '_key_ingredients' );
			$excerpt = wp_strip_all_tags( $product->get_short_description() );
			$excerpt = wp_trim_words( $excerpt, 18, '…' );
			$parts   = array();
			if ( $actives ) {
				/* translators: %s: comma-separated key actives. */
				$parts[] = sprintf( __( 'Key actives: %s.', 'glow-kbeauty' ), $actives );
			}
			if ( $excerpt ) {
				$parts[] = $excerpt;
			}
			$parts[] = __( 'Authentic, batch-verified, delivered across South Africa.', 'glow-kbeauty' );
			return implode( ' ', $parts );
		}
	}

	// Persona 3 — The Busy Minimalist.
	if ( ( glow_wc_active() && ( is_shop() || is_product_taxonomy() ) ) || is_tax( 'skin_concern' ) || is_tax( 'skin_type' ) ) {
		$term = get_queried_object();
		$name = ( $term && isset( $term->name ) ) ? $term->name : __( 'K-beauty', 'glow-kbeauty' );
		return sprintf(
			/* translators: %s: category or term name. */
			__( 'Shop %s without the noise — short, honest descriptions, full ingredient lists and fast filtering by skin type and concern. Free delivery over R500 in South Africa.', 'glow-kbeauty' ),
			$name
		);
	}

	// Persona 4 — The Gift Shopper.
	if ( is_page_template( 'page-about.php' ) ) {
		return __( 'A Johannesburg team with direct Seoul relationships. Every batch verified, every ingredient listed — K-beauty you can buy for yourself or someone you love without second-guessing it.', 'glow-kbeauty' );
	}

	// Persona 5 — The Sensitive-Skin Sceptic.
	if ( is_page_template( 'page-faq.php' ) ) {
		return __( 'Fragrance-free filtering, patch-test guidance, a returns policy that covers skin reactions, and delivery times by province. The questions sensitive skin actually asks, answered plainly.', 'glow-kbeauty' );
	}

	if ( is_page_template( 'page-contact.php' ) ) {
		return __( 'Talk to a person about your skin — WhatsApp, email, or Korean-language consultation. Johannesburg-based, replies within one working day, SAST hours.', 'glow-kbeauty' );
	}

	if ( is_search() ) {
		return sprintf(
			/* translators: %s: search query. */
			__( 'Search results for “%s” — authentic K-beauty, organised by routine step, shipped across South Africa.', 'glow-kbeauty' ),
			get_search_query()
		);
	}

	if ( is_singular() ) {
		$excerpt = wp_strip_all_tags( get_the_excerpt() );
		if ( $excerpt ) {
			return wp_trim_words( $excerpt, 28, '…' );
		}
	}

	return __( 'Authentic Korean skincare organised by the 7-step routine, batch-verified and shipped across South Africa.', 'glow-kbeauty' );
}

function glow_meta_keywords() {
	if ( is_front_page() ) {
		return 'korean skincare routine south africa, k-beauty routine order, skincare routine for beginners, k-beauty south africa';
	}
	if ( glow_wc_active() && is_product() ) {
		$actives = glow_meta( get_the_ID(), '_key_ingredients' );
		$brand   = glow_meta( get_the_ID(), '_product_brand' );
		return strtolower( implode( ', ', array_filter( array( $brand, $actives, 'k-beauty south africa' ) ) ) );
	}
	if ( is_page_template( 'page-faq.php' ) ) {
		return 'fragrance free korean skincare, sensitive skin k-beauty, patch test skincare, k-beauty delivery south africa';
	}
	return 'k-beauty, korean skincare, south africa, routine, ingredients';
}

function glow_og_image() {
	if ( glow_wc_active() && is_product() ) {
		$image_id = get_post_thumbnail_id();
		if ( $image_id ) {
			$src = wp_get_attachment_image_url( $image_id, 'large' );
			if ( $src ) {
				return $src;
			}
		}
		$svg = glow_meta( get_the_ID(), '_glow_svg' );
		if ( $svg ) {
			return get_template_directory_uri() . '/images/products/' . basename( $svg );
		}
	}

	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $src ) {
			return $src;
		}
	}

	return get_template_directory_uri() . '/screenshot.png';
}

function glow_seo_meta() {
	$description = glow_meta_description();
	$url         = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	$title       = wp_get_document_title();
	$type        = ( glow_wc_active() && is_product() ) ? 'product' : 'website';

	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );
	printf( '<meta name="keywords" content="%s" />' . "\n", esc_attr( glow_meta_keywords() ) );

	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( glow_og_image() ) );

	printf( '<meta name="twitter:card" content="summary_large_image" />' . "\n" );
	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $description ) );
	printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( glow_og_image() ) );
}
add_action( 'wp_head', 'glow_seo_meta', 1 );

/* --------------------------------------------------------------------------
 * JSON-LD structured data (wp_head priority 2)
 * ------------------------------------------------------------------------ */

function glow_jsonld_print( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

function glow_jsonld() {
	// Organization + WebSite with SearchAction — front page.
	if ( is_front_page() ) {
		glow_jsonld_print(
			array(
				'@context'          => 'https://schema.org',
				'@type'             => 'Organization',
				'name'              => get_bloginfo( 'name' ),
				'url'               => home_url( '/' ),
				'logo'              => glow_og_image(),
				'areaServed'        => 'ZA',
				'availableLanguage' => array( 'English', 'Korean' ),
				'contactPoint'      => array(
					'@type'       => 'ContactPoint',
					'contactType' => 'customer service',
					'email'       => get_option( 'admin_email' ),
					'areaServed'  => 'ZA',
				),
			)
		);

		glow_jsonld_print(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'WebSite',
				'name'            => get_bloginfo( 'name' ),
				'url'             => home_url( '/' ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => array(
						'@type'       => 'EntryPoint',
						'urlTemplate' => home_url( '/?s={search_term_string}&post_type=product' ),
					),
					'query-input' => 'required name=search_term_string',
				),
			)
		);
	}

	// Product + BreadcrumbList — single product.
	if ( glow_wc_active() && is_product() ) {
		$product = wc_get_product( get_the_ID() );

		if ( $product ) {
			$brand = glow_meta( $product->get_id(), '_product_brand' );

			$schema = array(
				'@context'    => 'https://schema.org',
				'@type'       => 'Product',
				'name'        => $product->get_name(),
				'description' => wp_strip_all_tags( $product->get_short_description() ? $product->get_short_description() : $product->get_description() ),
				'sku'         => $product->get_sku(),
				'image'       => glow_og_image(),
				'offers'      => array(
					'@type'         => 'Offer',
					'price'         => wc_format_decimal( $product->get_price(), 2 ),
					'priceCurrency' => get_woocommerce_currency(),
					'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
					'url'           => get_permalink( $product->get_id() ),
				),
			);

			if ( $brand ) {
				$schema['brand'] = array(
					'@type' => 'Brand',
					'name'  => $brand,
				);
			}

			if ( $product->get_review_count() > 0 ) {
				$schema['aggregateRating'] = array(
					'@type'       => 'AggregateRating',
					'ratingValue' => $product->get_average_rating(),
					'reviewCount' => $product->get_review_count(),
				);
			}

			glow_jsonld_print( $schema );

			$crumbs = array(
				array( get_bloginfo( 'name' ), home_url( '/' ) ),
				array( __( 'Shop', 'glow-kbeauty' ), wc_get_page_permalink( 'shop' ) ),
			);

			$terms = get_the_terms( $product->get_id(), 'product_cat' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term     = $terms[0];
				$link     = get_term_link( $term );
				$crumbs[] = array( $term->name, is_wp_error( $link ) ? home_url( '/' ) : $link );
			}

			$crumbs[] = array( $product->get_name(), get_permalink( $product->get_id() ) );

			glow_jsonld_print( glow_breadcrumb_schema( $crumbs ) );
		}
	}

	// BreadcrumbList — category and taxonomy archives.
	if ( glow_wc_active() && ( is_product_category() || is_tax( 'skin_concern' ) || is_tax( 'skin_type' ) ) ) {
		$term = get_queried_object();
		if ( $term && isset( $term->name ) ) {
			$link   = get_term_link( $term );
			$crumbs = array(
				array( get_bloginfo( 'name' ), home_url( '/' ) ),
				array( __( 'Shop', 'glow-kbeauty' ), wc_get_page_permalink( 'shop' ) ),
				array( $term->name, is_wp_error( $link ) ? home_url( '/' ) : $link ),
			);
			glow_jsonld_print( glow_breadcrumb_schema( $crumbs ) );
		}
	}

	// FAQPage — Help & FAQ template, built from glow_faq_items().
	if ( is_page_template( 'page-faq.php' ) ) {
		$entities = array();
		foreach ( glow_faq_items() as $item ) {
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $item['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $item['answer'],
				),
			);
		}

		glow_jsonld_print(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			)
		);
	}
}
add_action( 'wp_head', 'glow_jsonld', 2 );

/**
 * @param array[] $crumbs Array of [ name, url ] pairs in order.
 */
function glow_breadcrumb_schema( $crumbs ) {
	$items = array();
	foreach ( $crumbs as $i => $crumb ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb[0],
			'item'     => $crumb[1],
		);
	}

	return array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);
}
