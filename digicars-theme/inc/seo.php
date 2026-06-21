<?php
/**
 * Digicars SEO: search-intent meta tags, FAQ source of truth, and JSON-LD.
 *
 * This module is the discoverability layer for the Digicars marketplace. It
 * does three jobs, all driven by the current request context:
 *
 *   1. Emits a context-switched <meta name="description"> / keywords plus Open
 *      Graph and Twitter Card tags on wp_head (priority 1).
 *   2. Emits JSON-LD structured data on wp_head (priority 2): Organization /
 *      AutoDealer + WebSite SearchAction always, then Car, BreadcrumbList,
 *      FAQPage or BlogPosting depending on context.
 *   3. Exposes digicars_faq_items() — the SINGLE source of FAQ copy consumed by
 *      BOTH the FAQ page template and the FAQPage schema, so the two can never
 *      drift apart.
 *
 * Everything degrades gracefully: every WooCommerce / conditional-tag call is
 * guarded with function_exists()/is_* checks so the file cannot fatal when
 * WooCommerce is inactive or a template renders outside the expected context.
 *
 * -------------------------------------------------------------------------
 * SEARCH-INTENT PERSONAS
 *
 * Each persona maps to a real South African car-buying search intent. The
 * meta-description logic below selects copy per context to speak to the
 * persona most likely to land on that page.
 *
 *   PERSONA 1 — First-Car Buyer (budget-led)
 *     Young / first-time buyer shopping on absolute price. Searches like
 *     "cheapest cars under R150 000", "first car finance South Africa",
 *     "best small cars for beginners". Wants reassurance that buying online is
 *     safe and that finance is reachable on a modest income.
 *
 *   PERSONA 2 — Family Upgrader (space / safety / SUV)
 *     Growing family trading up for room and safety. Searches like
 *     "best family SUV South Africa", "7 seater under R500k",
 *     "safest family car". Cares about seats, boot space, NCAP and ISOFIX.
 *
 *   PERSONA 3 — Affordability / Finance Seeker (monthly-repayment-led)
 *     Shops by what fits the monthly budget, not the sticker price. Searches
 *     like "cars under R3000 per month", "car finance South Africa",
 *     "pre-approved car loan". Wants instalment estimates and pre-qualification.
 *
 *   PERSONA 4 — Brand / Model Researcher (specs / compare)
 *     Mid-funnel researcher comparing specific models. Searches like
 *     "Chery Tiggo specs", "Omoda C5 vs Haval Jolion", "Polo fuel economy".
 *     Wants accurate, structured specs — served by the Car schema on the PDP.
 *
 *   PERSONA 5 — Trade-in Upgrader (sell + buy)
 *     Has a car to dispose of as part of the next purchase. Searches like
 *     "sell my car instant offer", "trade in value", "trade in and upgrade".
 *     Wants a fast, honest valuation and a clean swap into the next vehicle.
 * ---------------------------------------------------------------------- */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/* -------------------------------------------------------------------------
 * Small internal helpers (namespaced with the digicars_seo_ prefix).
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'digicars_seo_truncate' ) ) {
	/**
	 * Collapse whitespace and truncate a description to ~155 characters.
	 *
	 * @param string $text  Raw description text.
	 * @param int    $limit Maximum length before an ellipsis is appended.
	 * @return string
	 */
	function digicars_seo_truncate( string $text, int $limit = 155 ): string {
		$text = function_exists( 'wp_strip_all_tags' ) ? wp_strip_all_tags( $text ) : strip_tags( $text );
		$text = trim( preg_replace( '/\s+/', ' ', $text ) );
		if ( function_exists( 'mb_strlen' ) ) {
			if ( mb_strlen( $text ) <= $limit ) {
				return $text;
			}
			$cut = mb_substr( $text, 0, $limit - 1 );
			$sp  = mb_strrpos( $cut, ' ' );
			if ( false !== $sp && $sp > 0 ) {
				$cut = mb_substr( $cut, 0, $sp );
			}
			return rtrim( $cut, " ,.;:-" ) . '…';
		}
		if ( strlen( $text ) <= $limit ) {
			return $text;
		}
		$cut = substr( $text, 0, $limit - 1 );
		$sp  = strrpos( $cut, ' ' );
		if ( false !== $sp && $sp > 0 ) {
			$cut = substr( $cut, 0, $sp );
		}
		return rtrim( $cut, " ,.;:-" ) . '…';
	}
}

if ( ! function_exists( 'digicars_seo_home' ) ) {
	/**
	 * Safe home_url() wrapper.
	 *
	 * @param string $path Optional path.
	 * @return string
	 */
	function digicars_seo_home( string $path = '' ): string {
		return function_exists( 'home_url' ) ? home_url( $path ) : $path;
	}
}

if ( ! function_exists( 'digicars_seo_default_image' ) ) {
	/**
	 * Default share image (hero). The file may not exist yet — that is fine,
	 * the URL is still a sane fallback for og:image / twitter:image.
	 *
	 * @return string
	 */
	function digicars_seo_default_image(): string {
		if ( function_exists( 'get_theme_file_uri' ) ) {
			return get_theme_file_uri( 'images/hero/hero-showroom.svg' );
		}
		return '';
	}
}

if ( ! function_exists( 'digicars_seo_logo' ) ) {
	/**
	 * Brand logo URL for Organization schema / og fallback.
	 *
	 * @return string
	 */
	function digicars_seo_logo(): string {
		if ( function_exists( 'get_theme_file_uri' ) ) {
			return get_theme_file_uri( 'images/brand/digicars-logo.png' );
		}
		return '';
	}
}

if ( ! function_exists( 'digicars_seo_current_url' ) ) {
	/**
	 * Best-effort canonical URL for the current request.
	 *
	 * @return string
	 */
	function digicars_seo_current_url(): string {
		if ( function_exists( 'is_singular' ) && is_singular() && function_exists( 'get_permalink' ) ) {
			$link = get_permalink();
			if ( $link ) {
				return $link;
			}
		}
		if ( function_exists( 'is_tax' ) && ( is_tax() || ( function_exists( 'is_category' ) && is_category() ) || ( function_exists( 'is_tag' ) && is_tag() ) ) ) {
			$obj = function_exists( 'get_queried_object' ) ? get_queried_object() : null;
			if ( $obj && isset( $obj->term_id ) && function_exists( 'get_term_link' ) ) {
				$link = get_term_link( $obj );
				if ( $link && ! is_wp_error( $link ) ) {
					return $link;
				}
			}
		}
		if ( function_exists( 'is_shop' ) && is_shop() && function_exists( 'wc_get_page_permalink' ) ) {
			$link = wc_get_page_permalink( 'shop' );
			if ( $link ) {
				return $link;
			}
		}
		return digicars_seo_home( '/' );
	}
}

/* -------------------------------------------------------------------------
 * FAQ — single source of truth (feeds the FAQ page AND the FAQPage schema).
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'digicars_faq_items' ) ) {
	/**
	 * The canonical Digicars FAQ.
	 *
	 * This array is the ONLY place FAQ copy lives. The FAQ page template and the
	 * FAQPage JSON-LD both read from here, so they can never drift apart.
	 *
	 * @return array<int,array{q:string,a:string}>
	 */
	function digicars_faq_items(): array {
		return array(
			array(
				'q' => __( 'How does buying a car online with Digicars work?', 'digicars' ),
				'a' => __( 'Digicars is a phygital marketplace: you do the slow, frustrating parts online and skip the dealership queue. Browse the full range, send an enquiry, and apply for finance from your phone or laptop. Once you are approved and happy, we handle the paperwork digitally and you either collect the car from the nearest branch or we deliver it to your door. You only deal with people when it actually helps.', 'digicars' ),
			),
			array(
				'q' => __( 'Can I get car finance through Digicars, and how do I know what I can afford?', 'digicars' ),
				'a' => __( 'Yes. We work with South Africa\'s major banks and vehicle finance houses — including WesBank, Absa, Standard Bank, Nedbank and MFC — and submit your application to several at once to find the best deal. Use the affordability calculator to estimate a monthly instalment, then complete a free, no-obligation pre-qualification so you know your budget before you choose a car. Approval depends on your credit profile, affordability and a deposit where applicable.', 'digicars' ),
			),
			array(
				'q' => __( 'Do you accept trade-ins?', 'digicars' ),
				'a' => __( 'Absolutely — most of our buyers trade in their current car. Tell us the make, model, year and mileage and we will give you an instant indicative offer online, then confirm the final figure after a quick verification. The trade-in value is settled against your new vehicle or any outstanding finance, so you can sell and upgrade in a single, simple transaction.', 'digicars' ),
			),
			array(
				'q' => __( 'What condition are the cars in and how are they checked?', 'digicars' ),
				'a' => __( 'Every used and demo vehicle goes through a multi-point inspection before it is listed, and we verify the odometer, service history and finance status. New cars come straight from franchise stock. Each listing shows the mileage, condition, previous owners and any service or warranty plan, and you can request the full inspection report or a video walkaround before you commit.', 'digicars' ),
			),
			array(
				'q' => __( 'Can you deliver, and which provinces do you cover?', 'digicars' ),
				'a' => __( 'We deliver nationwide across all nine provinces — Gauteng, Western Cape, KwaZulu-Natal, Eastern Cape, Free State, Mpumalanga, Limpopo, North West and the Northern Cape. Delivery to a major centre is usually a few working days; outlying areas take a little longer. Prefer to fetch it yourself? Collect from your nearest Digicars branch at no charge.', 'digicars' ),
			),
			array(
				'q' => __( 'What warranties and service plans do the cars come with?', 'digicars' ),
				'a' => __( 'New vehicles carry the manufacturer\'s factory warranty and, where applicable, a service or maintenance plan. Many used and demo cars still have the balance of the original plan, and we can add an extended warranty or service plan for extra peace of mind. The exact cover is listed on each vehicle, and a consultant can talk you through the options before you finalise.', 'digicars' ),
			),
			array(
				'q' => __( 'How do I book a test drive?', 'digicars' ),
				'a' => __( 'Open the vehicle you are interested in and send an enquiry, or use the Concierge to ask. A consultant will get back to you within one working day to arrange a test drive at the branch where the car is, or a viewing at a time that suits you. You are never obligated to buy — the test drive is there so you can be sure before you decide.', 'digicars' ),
			),
		);
	}
}

/* -------------------------------------------------------------------------
 * Meta description / keywords + Open Graph + Twitter Card.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'digicars_seo_build_meta' ) ) {
	/**
	 * Resolve the meta payload for the current context.
	 *
	 * @return array{title:string,description:string,keywords:string,og_type:string,image:string,url:string}
	 */
	function digicars_seo_build_meta(): array {
		$site_name = function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'name' ) : 'Digicars';
		if ( '' === trim( $site_name ) ) {
			$site_name = 'Digicars';
		}

		$data = array(
			'title'       => $site_name,
			'description' => __( 'Digicars is South Africa\'s digital-first car marketplace. Browse new, demo and used cars by budget or monthly repayment, get pre-qualified for finance online, and have your car delivered or collect it in-store.', 'digicars' ),
			'keywords'    => 'cars for sale South Africa, car finance, buy car online, used cars, new cars, monthly car repayment',
			'og_type'     => 'website',
			'image'       => digicars_seo_default_image(),
			'url'         => digicars_seo_current_url(),
		);

		/* ---- Single vehicle (product) -------------------------------------- */
		if (
			function_exists( 'is_product' ) && is_product()
			&& function_exists( 'wc_get_product' )
		) {
			$id      = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
			$product = $id ? wc_get_product( $id ) : null;

			if ( $product ) {
				$year    = (string) digicars_meta( $id, '_vehicle_year' );
				$make    = (string) digicars_meta( $id, '_vehicle_make' );
				$model   = (string) digicars_meta( $id, '_vehicle_model' );
				$variant = (string) digicars_meta( $id, '_vehicle_variant' );

				$name_parts = array_filter(
					array( trim( $year ), trim( $make ), trim( $model ), trim( $variant ) ),
					static function ( $p ) {
						return '' !== $p;
					}
				);
				$name = trim( implode( ' ', $name_parts ) );
				if ( '' === $name ) {
					$name = method_exists( $product, 'get_name' ) ? (string) $product->get_name() : $site_name;
				}

				$data['title']   = $name . ' — ' . $site_name;
				$data['og_type'] = 'product';

				// Prefer a trimmed AI summary when available.
				$summary = '';
				if ( function_exists( 'digicars_build_ai_summary' ) ) {
					$summary = trim( (string) digicars_build_ai_summary( $id ) );
				}

				if ( '' !== $summary ) {
					$data['description'] = $summary . ' ' . __( 'Enquire or pre-qualify for finance online.', 'digicars' );
				} else {
					$monthly = (int) digicars_meta( $id, '_vehicle_monthly_from' );
					if ( 0 === $monthly ) {
						$price = (float) digicars_meta( $id, '_vehicle_price' );
						if ( $price > 0 && function_exists( 'digicars_monthly_from' ) ) {
							$monthly = digicars_monthly_from( $price );
						}
					}

					$specs = array();
					$km    = (int) digicars_meta( $id, '_vehicle_mileage' );
					if ( $km > 0 ) {
						$specs[] = number_format_i18n( $km ) . ' km';
					}
					$trans = (string) digicars_meta( $id, '_vehicle_transmission' );
					if ( '' !== trim( $trans ) ) {
						$specs[] = $trans;
					}
					$fuel = (string) digicars_meta( $id, '_vehicle_fuel' );
					if ( '' !== trim( $fuel ) ) {
						$specs[] = $fuel;
					}

					$lead = $name;
					if ( $monthly > 0 ) {
						$lead .= ' — ' . sprintf(
							/* translators: %s: monthly instalment, formatted. */
							__( 'from R%s pm.', 'digicars' ),
							number_format_i18n( $monthly )
						);
					} else {
						$lead .= '.';
					}

					$spec_str = $specs ? ' ' . implode( ', ', $specs ) . '.' : '';

					$data['description'] = $lead . $spec_str . ' ' . __( 'Enquire or pre-qualify for finance online.', 'digicars' );
				}

				$data['keywords'] = trim( implode( ', ', array_filter( array( $make, $model, $variant, ( $make . ' ' . $model . ' for sale' ), __( 'car finance South Africa', 'digicars' ) ) ) ), ', ' );

				// Product image.
				$img = digicars_seo_product_image( $id, $product );
				if ( '' !== $img ) {
					$data['image'] = $img;
				}

				return $data;
			}
		}

		/* ---- Product taxonomy / body-type archive (Persona 2) -------------- */
		if (
			( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() )
			|| ( function_exists( 'is_product_category' ) && is_product_category() )
		) {
			$obj  = function_exists( 'get_queried_object' ) ? get_queried_object() : null;
			$term = ( $obj && isset( $obj->name ) ) ? (string) $obj->name : __( 'cars', 'digicars' );

			$data['title']       = sprintf( /* translators: %1$s term, %2$s site. */ __( '%1$s for sale — %2$s', 'digicars' ), $term, $site_name );
			$data['description'] = sprintf(
				/* translators: %s: taxonomy term name (e.g. SUV / Family). */
				__( 'Looking for the best %s in South Africa? Compare space, safety and 7-seater options for your family, see the monthly repayment on each, and get pre-qualified for finance online.', 'digicars' ),
				$term
			);
			$data['keywords'] = trim( $term . ', ' . __( 'best family SUV South Africa, 7 seater, family car, car finance', 'digicars' ) );
			return $data;
		}

		/* ---- Page templates / specific pages ------------------------------- */
		if ( function_exists( 'is_page' ) && is_page() ) {
			$slug          = digicars_seo_page_slug();
			$template_slug = digicars_seo_page_template_slug();

			if ( 'finance' === $slug || false !== strpos( $slug, 'finance' ) || false !== strpos( $template_slug, 'finance' ) || false !== strpos( $slug, 'afford' ) ) {
				$data['title']       = __( 'Car Finance & Affordability', 'digicars' ) . ' — ' . $site_name;
				$data['description'] = __( 'Find a car that fits your monthly budget. Estimate your repayment, get pre-qualified online across South Africa\'s major banks, and shop cars by what you can afford per month — not just the sticker price.', 'digicars' );
				$data['keywords']    = __( 'car finance South Africa, cars under R3000 per month, pre-approved car loan, vehicle finance, monthly repayment', 'digicars' );
				return $data;
			}

			if ( 'about' === $slug || false !== strpos( $slug, 'about' ) ) {
				$data['title']       = __( 'About Digicars', 'digicars' ) . ' — ' . $site_name;
				$data['description'] = __( 'Digicars makes buying a car simple, transparent and safe. Every vehicle is inspected and verified, finance is arranged with trusted SA banks, and you can enquire, finance and take delivery without the dealership runaround.', 'digicars' );
				$data['keywords']    = __( 'about Digicars, trusted car dealership South Africa, buy car online safely', 'digicars' );
				return $data;
			}

			if ( 'faq' === $slug || 'help' === $slug || false !== strpos( $slug, 'faq' ) || false !== strpos( $slug, 'help' ) || false !== strpos( $template_slug, 'faq' ) || false !== strpos( $template_slug, 'help' ) ) {
				$data['title']       = __( 'Help & FAQ', 'digicars' ) . ' — ' . $site_name;
				$data['description'] = __( 'How online car buying works at Digicars: finance, trade-ins, delivery across all nine provinces, warranties and test drives. Got a car to sell? Get an instant trade-in offer and upgrade in one step.', 'digicars' );
				$data['keywords']    = __( 'Digicars help, car buying FAQ, trade in value, car delivery South Africa', 'digicars' );
				return $data;
			}

			if ( 'sell' === $slug || false !== strpos( $slug, 'sell' ) || false !== strpos( $slug, 'trade' ) || false !== strpos( $template_slug, 'sell' ) ) {
				$data['title']       = __( 'Sell or Trade In Your Car', 'digicars' ) . ' — ' . $site_name;
				$data['description'] = __( 'Sell your car or trade it in — get an instant online offer in minutes. Tell us your make, model, year and mileage, get a fair valuation, and put the value straight towards your next car.', 'digicars' );
				$data['keywords']    = __( 'sell my car instant offer, trade in value, sell my car South Africa, trade in and upgrade', 'digicars' );
				return $data;
			}

			if ( 'contact' === $slug || false !== strpos( $slug, 'contact' ) ) {
				$data['title']       = __( 'Contact Digicars', 'digicars' ) . ' — ' . $site_name;
				$data['description'] = __( 'Talk to a Digicars consultant about a vehicle, finance, a trade-in or delivery. Call 010 595 1180, email info@digicars.co.za, or send an enquiry and we will respond within one working day.', 'digicars' );
				$data['keywords']    = __( 'contact Digicars, car dealership contact South Africa', 'digicars' );
				return $data;
			}
		}

		/* ---- Search results ------------------------------------------------ */
		if ( function_exists( 'is_search' ) && is_search() ) {
			$q = function_exists( 'get_search_query' ) ? (string) get_search_query() : '';
			$data['title']       = sprintf( /* translators: %1$s query, %2$s site. */ __( 'Results for "%1$s" — %2$s', 'digicars' ), $q, $site_name );
			$data['description'] = sprintf(
				/* translators: %s: the search query. */
				__( 'Results for "%s". Browse matching cars on Digicars, see the monthly repayment on each, and get pre-qualified for finance online.', 'digicars' ),
				$q
			);
			$data['keywords'] = trim( $q . ', ' . __( 'cars for sale South Africa', 'digicars' ) );
			return $data;
		}

		/* ---- Blog single post ---------------------------------------------- */
		if ( function_exists( 'is_singular' ) && is_singular( 'post' ) ) {
			$id = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;

			$title = $id && function_exists( 'get_the_title' ) ? (string) get_the_title( $id ) : '';
			if ( '' !== $title ) {
				$data['title'] = $title . ' — ' . $site_name;
			}

			$excerpt = '';
			if ( $id && function_exists( 'get_the_excerpt' ) ) {
				$excerpt = trim( (string) get_the_excerpt( $id ) );
			}
			if ( '' === $excerpt && $id && function_exists( 'get_post_field' ) ) {
				$excerpt = trim( wp_strip_all_tags( (string) get_post_field( 'post_content', $id ) ) );
			}
			if ( '' !== $excerpt ) {
				$data['description'] = $excerpt;
			}

			$data['og_type'] = 'article';

			$post_img = digicars_seo_post_image( $id );
			if ( '' !== $post_img ) {
				$data['image'] = $post_img;
			}
			return $data;
		}

		/* ---- Blog index ---------------------------------------------------- */
		if (
			( function_exists( 'is_home' ) && is_home() && ! ( function_exists( 'is_front_page' ) && is_front_page() ) )
			|| ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'post' ) )
		) {
			$data['title']       = __( 'Car Torque', 'digicars' ) . ' — ' . $site_name;
			$data['description'] = __( 'Car Torque — car buying advice, finance tips and model news from Digicars.', 'digicars' );
			$data['keywords']    = __( 'car buying advice, car finance tips, car news South Africa', 'digicars' );
			return $data;
		}

		// Default / front page keeps the Persona 1/3 marketplace copy above.
		if ( function_exists( 'is_front_page' ) && is_front_page() ) {
			$data['title'] = $site_name . ' — ' . __( 'Buy your next car online in South Africa', 'digicars' );
		}

		return $data;
	}
}

if ( ! function_exists( 'digicars_seo_product_image' ) ) {
	/**
	 * Resolve a product's primary image URL, with a sane fallback.
	 *
	 * @param int   $id      Product ID.
	 * @param mixed $product Product object (WC_Product or stub).
	 * @return string
	 */
	function digicars_seo_product_image( int $id, $product = null ): string {
		if ( function_exists( 'get_post_thumbnail_id' ) && function_exists( 'wp_get_attachment_image_url' ) ) {
			$thumb = get_post_thumbnail_id( $id );
			if ( $thumb ) {
				$src = wp_get_attachment_image_url( $thumb, 'large' );
				if ( $src ) {
					return $src;
				}
			}
		}
		if ( $product && method_exists( $product, 'get_image_id' ) && function_exists( 'wp_get_attachment_image_url' ) ) {
			$img_id = (int) $product->get_image_id();
			if ( $img_id ) {
				$src = wp_get_attachment_image_url( $img_id, 'large' );
				if ( $src ) {
					return $src;
				}
			}
		}
		return digicars_seo_default_image();
	}
}

if ( ! function_exists( 'digicars_seo_post_image' ) ) {
	/**
	 * Resolve a blog post's featured image URL, with a sane fallback.
	 *
	 * @param int $id Post ID.
	 * @return string
	 */
	function digicars_seo_post_image( int $id ): string {
		if ( $id && function_exists( 'has_post_thumbnail' ) && function_exists( 'get_the_post_thumbnail_url' ) && has_post_thumbnail( $id ) ) {
			$src = get_the_post_thumbnail_url( $id, 'large' );
			if ( $src ) {
				return $src;
			}
		}
		return digicars_seo_default_image();
	}
}

if ( ! function_exists( 'digicars_seo_page_slug' ) ) {
	/**
	 * Lowercased slug of the current page (empty when unavailable).
	 *
	 * @return string
	 */
	function digicars_seo_page_slug(): string {
		$obj = function_exists( 'get_queried_object' ) ? get_queried_object() : null;
		if ( $obj && isset( $obj->post_name ) ) {
			return strtolower( (string) $obj->post_name );
		}
		return '';
	}
}

if ( ! function_exists( 'digicars_seo_page_template_slug' ) ) {
	/**
	 * Lowercased page-template file slug (e.g. "page-faq" or template basename).
	 *
	 * @return string
	 */
	function digicars_seo_page_template_slug(): string {
		$id = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
		if ( $id && function_exists( 'get_page_template_slug' ) ) {
			return strtolower( (string) get_page_template_slug( $id ) );
		}
		return '';
	}
}

if ( ! function_exists( 'digicars_meta_tags' ) ) {
	/**
	 * Print meta description / keywords + Open Graph + Twitter Card tags.
	 *
	 * @return void
	 */
	function digicars_meta_tags(): void {
		$m = digicars_seo_build_meta();

		$site_name = function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'name' ) : 'Digicars';
		if ( '' === trim( $site_name ) ) {
			$site_name = 'Digicars';
		}

		$desc  = digicars_seo_truncate( (string) $m['description'] );
		$title = (string) $m['title'];
		$url   = (string) $m['url'];
		$image = (string) $m['image'];
		$type  = (string) $m['og_type'];

		echo "\n<!-- Digicars SEO -->\n";

		printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $desc ) );
		if ( '' !== trim( (string) $m['keywords'] ) ) {
			printf( "<meta name=\"keywords\" content=\"%s\">\n", esc_attr( $m['keywords'] ) );
		}

		// Open Graph.
		printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
		printf( "<meta property=\"og:type\" content=\"%s\">\n", esc_attr( $type ) );
		printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( $url ) );
		printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $desc ) );
		if ( '' !== $image ) {
			printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $image ) );
		}
		printf( "<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( $site_name ) );

		// Twitter Card.
		printf( "<meta name=\"twitter:card\" content=\"%s\">\n", esc_attr( 'summary_large_image' ) );
		printf( "<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr( $title ) );
		printf( "<meta name=\"twitter:description\" content=\"%s\">\n", esc_attr( $desc ) );
		if ( '' !== $image ) {
			printf( "<meta name=\"twitter:image\" content=\"%s\">\n", esc_url( $image ) );
		}
	}
}
add_action( 'wp_head', 'digicars_meta_tags', 1 );

/* -------------------------------------------------------------------------
 * JSON-LD structured data.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'digicars_seo_print_json_ld' ) ) {
	/**
	 * Encode + print a single JSON-LD block.
	 *
	 * @param array $data Schema data.
	 * @return void
	 */
	function digicars_seo_print_json_ld( array $data ): void {
		if ( empty( $data ) ) {
			return;
		}
		if ( function_exists( 'wp_json_encode' ) ) {
			$json = wp_json_encode( $data );
		} else {
			$json = json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		}
		if ( false === $json ) {
			return;
		}
		echo "<script type=\"application/ld+json\">" . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'digicars_seo_organization' ) ) {
	/**
	 * Organization / AutoDealer schema node.
	 *
	 * @return array
	 */
	function digicars_seo_organization(): array {
		return array(
			'@context'    => 'https://schema.org',
			'@type'       => 'AutoDealer',
			'name'        => 'Digi Cars Group',
			'url'         => digicars_seo_home( '/' ),
			'logo'        => digicars_seo_logo(),
			'image'       => digicars_seo_default_image(),
			'telephone'   => '010 595 1180',
			'email'       => 'info@digicars.co.za',
			'areaServed'  => 'ZA',
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => '168 Grayston Drive, Sandown',
				'addressLocality' => 'Sandton',
				'addressRegion'   => 'Gauteng',
				'addressCountry'  => 'ZA',
			),
			'sameAs'      => array(
				'https://www.facebook.com/DigiCarsSA',
				'https://www.instagram.com/digicarssa',
				'https://twitter.com/digicarsza',
			),
		);
	}
}

if ( ! function_exists( 'digicars_seo_website' ) ) {
	/**
	 * WebSite schema node with a vehicle SearchAction.
	 *
	 * @return array
	 */
	function digicars_seo_website(): array {
		return array(
			'@context'        => 'https://schema.org',
			'@type'           => 'WebSite',
			'name'            => function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'name' ) : 'Digicars',
			'url'             => digicars_seo_home( '/' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => digicars_seo_home( '/?s={search_term_string}&post_type=product' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
	}
}

if ( ! function_exists( 'digicars_seo_car_schema' ) ) {
	/**
	 * Car / Vehicle schema for the single-vehicle PDP.
	 *
	 * @param int   $id      Product ID.
	 * @param mixed $product Product object.
	 * @return array
	 */
	function digicars_seo_car_schema( int $id, $product ): array {
		$year    = (string) digicars_meta( $id, '_vehicle_year' );
		$make    = (string) digicars_meta( $id, '_vehicle_make' );
		$model   = (string) digicars_meta( $id, '_vehicle_model' );
		$variant = (string) digicars_meta( $id, '_vehicle_variant' );

		$name_parts = array_filter(
			array( trim( $year ), trim( $make ), trim( $model ), trim( $variant ) ),
			static function ( $p ) {
				return '' !== $p;
			}
		);
		$name = trim( implode( ' ', $name_parts ) );
		if ( '' === $name && method_exists( $product, 'get_name' ) ) {
			$name = (string) $product->get_name();
		}

		$node = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Car',
			'name'     => $name,
			'url'      => function_exists( 'get_permalink' ) ? get_permalink( $id ) : digicars_seo_home( '/' ),
		);

		if ( '' !== trim( $make ) ) {
			$node['brand'] = array(
				'@type' => 'Brand',
				'name'  => $make,
			);
			$node['manufacturer'] = array(
				'@type' => 'Organization',
				'name'  => $make,
			);
		}
		if ( '' !== trim( $model ) ) {
			$node['model'] = $model;
		}
		if ( '' !== trim( $variant ) ) {
			$node['vehicleConfiguration'] = $variant;
		}
		if ( '' !== trim( $year ) ) {
			$node['modelDate']      = $year;
			$node['productionDate'] = $year;
			$node['vehicleModelDate'] = $year;
		}

		$mileage = (int) digicars_meta( $id, '_vehicle_mileage' );
		if ( $mileage > 0 ) {
			$node['mileageFromOdometer'] = array(
				'@type'    => 'QuantitativeValue',
				'value'    => $mileage,
				'unitCode' => 'KMT',
			);
		}

		$fuel = (string) digicars_meta( $id, '_vehicle_fuel' );
		if ( '' !== trim( $fuel ) ) {
			$node['fuelType'] = $fuel;
		}
		$trans = (string) digicars_meta( $id, '_vehicle_transmission' );
		if ( '' !== trim( $trans ) ) {
			$node['vehicleTransmission'] = $trans;
		}

		$doors = (int) digicars_meta( $id, '_vehicle_doors' );
		if ( $doors > 0 ) {
			$node['numberOfDoors'] = $doors;
		}
		$seats = (int) digicars_meta( $id, '_vehicle_seats' );
		if ( $seats > 0 ) {
			$node['vehicleSeatingCapacity'] = $seats;
		}
		$colour = (string) digicars_meta( $id, '_vehicle_colour' );
		if ( '' !== trim( $colour ) ) {
			$node['color'] = $colour;
		}

		$drivetrain = (string) digicars_meta( $id, '_vehicle_drivetrain' );
		if ( '' !== trim( $drivetrain ) ) {
			$node['driveWheelConfiguration'] = $drivetrain;
		}

		// Condition → schema enum.
		$condition = strtolower( (string) digicars_meta( $id, '_vehicle_condition' ) );
		if ( '' !== $condition ) {
			$node['itemCondition'] = ( false !== strpos( $condition, 'new' ) )
				? 'https://schema.org/NewCondition'
				: 'https://schema.org/UsedCondition';
		}

		// Image.
		$img = digicars_seo_product_image( $id, $product );
		if ( '' !== $img ) {
			$node['image'] = $img;
		}

		// Description (factual AI summary if present).
		if ( function_exists( 'digicars_build_ai_summary' ) ) {
			$summary = trim( (string) digicars_build_ai_summary( $id ) );
			if ( '' !== $summary ) {
				$node['description'] = $summary;
			}
		}

		// Offer.
		$price = (float) digicars_meta( $id, '_vehicle_price' );
		if ( $price <= 0 && method_exists( $product, 'get_price' ) ) {
			$price = (float) $product->get_price();
		}
		if ( $price > 0 ) {
			$node['offers'] = array(
				'@type'         => 'Offer',
				'price'         => (string) round( $price, 2 ),
				'priceCurrency' => 'ZAR',
				'availability'  => 'https://schema.org/InStock',
				'url'           => function_exists( 'get_permalink' ) ? get_permalink( $id ) : digicars_seo_home( '/' ),
				'seller'        => array(
					'@type' => 'AutoDealer',
					'name'  => 'Digi Cars Group',
				),
			);
		}

		// aggregateRating only when there are real reviews.
		$rating = 0.0;
		$count  = 0;
		if ( method_exists( $product, 'get_average_rating' ) ) {
			$rating = (float) $product->get_average_rating();
		}
		if ( method_exists( $product, 'get_rating_count' ) ) {
			$count = (int) $product->get_rating_count();
		}
		if ( $rating > 0 && $count > 0 ) {
			$node['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => (string) $rating,
				'reviewCount' => $count,
				'bestRating'  => '5',
				'worstRating' => '1',
			);
		}

		return $node;
	}
}

if ( ! function_exists( 'digicars_seo_breadcrumb' ) ) {
	/**
	 * BreadcrumbList for single vehicles and product archives.
	 *
	 * @return array Empty when no meaningful trail exists.
	 */
	function digicars_seo_breadcrumb(): array {
		$items = array();
		$pos   = 1;

		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => __( 'Home', 'digicars' ),
			'item'     => digicars_seo_home( '/' ),
		);

		// Shop / cars root.
		$shop_url = digicars_seo_home( '/shop' );
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			$maybe = wc_get_page_permalink( 'shop' );
			if ( $maybe ) {
				$shop_url = $maybe;
			}
		}
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => __( 'Cars', 'digicars' ),
			'item'     => $shop_url,
		);

		// Current node.
		if ( function_exists( 'is_product' ) && is_product() ) {
			$id   = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
			$name = $id && function_exists( 'get_the_title' ) ? (string) get_the_title( $id ) : '';
			if ( '' !== $name ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos++,
					'name'     => $name,
					'item'     => function_exists( 'get_permalink' ) ? get_permalink( $id ) : $shop_url,
				);
			}
		} elseif (
			( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() )
			|| ( function_exists( 'is_product_category' ) && is_product_category() )
		) {
			$obj = function_exists( 'get_queried_object' ) ? get_queried_object() : null;
			if ( $obj && isset( $obj->name ) ) {
				$term_url = $shop_url;
				if ( isset( $obj->term_id ) && function_exists( 'get_term_link' ) ) {
					$maybe = get_term_link( $obj );
					if ( $maybe && ! is_wp_error( $maybe ) ) {
						$term_url = $maybe;
					}
				}
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos++,
					'name'     => (string) $obj->name,
					'item'     => $term_url,
				);
			}
		}

		if ( count( $items ) < 2 ) {
			return array();
		}

		return array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}
}

if ( ! function_exists( 'digicars_seo_faq_schema' ) ) {
	/**
	 * FAQPage schema built from digicars_faq_items().
	 *
	 * @return array
	 */
	function digicars_seo_faq_schema(): array {
		$entities = array();
		foreach ( digicars_faq_items() as $item ) {
			if ( empty( $item['q'] ) || empty( $item['a'] ) ) {
				continue;
			}
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => (string) $item['q'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => (string) $item['a'],
				),
			);
		}
		if ( empty( $entities ) ) {
			return array();
		}
		return array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		);
	}
}

if ( ! function_exists( 'digicars_seo_blogposting_schema' ) ) {
	/**
	 * BlogPosting schema for a single blog post.
	 *
	 * @param int $id Post ID.
	 * @return array
	 */
	function digicars_seo_blogposting_schema( int $id ): array {
		if ( ! $id ) {
			return array();
		}

		$headline = function_exists( 'get_the_title' ) ? (string) get_the_title( $id ) : '';

		$published = function_exists( 'get_the_date' ) ? (string) get_the_date( 'c', $id ) : '';
		$modified  = function_exists( 'get_the_modified_date' ) ? (string) get_the_modified_date( 'c', $id ) : '';

		$author = '';
		if ( function_exists( 'get_post_field' ) && function_exists( 'get_the_author_meta' ) ) {
			$author_id = (int) get_post_field( 'post_author', $id );
			if ( $author_id ) {
				$author = (string) get_the_author_meta( 'display_name', $author_id );
			}
		}

		$node = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'BlogPosting',
			'headline'         => $headline,
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id'   => function_exists( 'get_permalink' ) ? get_permalink( $id ) : digicars_seo_home( '/' ),
			),
			'publisher'        => digicars_seo_organization(),
		);

		if ( '' !== $published ) {
			$node['datePublished'] = $published;
		}
		if ( '' !== $modified ) {
			$node['dateModified'] = $modified;
		}
		if ( '' !== $author ) {
			$node['author'] = array(
				'@type' => 'Person',
				'name'  => $author,
			);
		}

		$img = digicars_seo_post_image( $id );
		if ( '' !== $img ) {
			$node['image'] = $img;
		}

		return $node;
	}
}

if ( ! function_exists( 'digicars_json_ld' ) ) {
	/**
	 * Emit all applicable JSON-LD blocks for the current context.
	 *
	 * @return void
	 */
	function digicars_json_ld(): void {
		// Always present.
		digicars_seo_print_json_ld( digicars_seo_organization() );
		digicars_seo_print_json_ld( digicars_seo_website() );

		// Single vehicle → Car + BreadcrumbList.
		if (
			function_exists( 'is_product' ) && is_product()
			&& function_exists( 'wc_get_product' )
		) {
			$id      = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
			$product = $id ? wc_get_product( $id ) : null;
			if ( $product ) {
				digicars_seo_print_json_ld( digicars_seo_car_schema( $id, $product ) );
			}
			digicars_seo_print_json_ld( digicars_seo_breadcrumb() );
			return;
		}

		// Product archives → BreadcrumbList.
		if (
			( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() )
			|| ( function_exists( 'is_product_category' ) && is_product_category() )
			|| ( function_exists( 'is_shop' ) && is_shop() )
		) {
			digicars_seo_print_json_ld( digicars_seo_breadcrumb() );
			return;
		}

		// FAQ / Help page → FAQPage.
		if ( function_exists( 'is_page' ) && is_page() ) {
			$slug          = digicars_seo_page_slug();
			$template_slug = digicars_seo_page_template_slug();
			if (
				'faq' === $slug || 'help' === $slug
				|| false !== strpos( $slug, 'faq' ) || false !== strpos( $slug, 'help' )
				|| false !== strpos( $template_slug, 'faq' ) || false !== strpos( $template_slug, 'help' )
			) {
				digicars_seo_print_json_ld( digicars_seo_faq_schema() );
			}
			return;
		}

		// Blog single → BlogPosting.
		if ( function_exists( 'is_singular' ) && is_singular( 'post' ) ) {
			$id = function_exists( 'get_the_ID' ) ? (int) get_the_ID() : 0;
			digicars_seo_print_json_ld( digicars_seo_blogposting_schema( $id ) );
		}
	}
}
add_action( 'wp_head', 'digicars_json_ld', 2 );
