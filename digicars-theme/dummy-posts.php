<?php
/**
 * Digicars "Car Torque" blog seeder.
 *
 * Idempotent WP-CLI seeder for the native Car Torque blog. Run with:
 *
 *   wp eval-file wp-content/themes/digicars-theme/dummy-posts.php
 *
 * Each post is keyed on its slug: on re-run an existing post with that slug is
 * left untouched rather than duplicated, so the importer is safe to run
 * repeatedly. The "Car Torque" category is created on demand and every post is
 * assigned to it. Tags are created on demand. A featured image is sideloaded
 * from images/ best-effort and never aborts the import.
 *
 * @package Digicars
 */

/* -------------------------------------------------------------------------
 * Environment guard — only run under WP-CLI / a real WP bootstrap. A stray
 * web hit (no wp_insert_post) does nothing.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'wp_insert_post' ) ) {
	$digicars_notice = "Digicars blog seeder: WordPress not loaded. Run via: wp eval-file wp-content/themes/digicars-theme/dummy-posts.php\n";
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

if ( ! function_exists( 'digicars_blog_log' ) ) {
	/**
	 * Print a progress line to the CLI (or stdout as a fallback).
	 *
	 * @param string $message Line to print.
	 * @return void
	 */
	function digicars_blog_log( string $message ): void {
		if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( '\\WP_CLI' ) ) {
			\WP_CLI::log( $message );
		} else {
			echo $message . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/* -------------------------------------------------------------------------
 * Ensure the "Car Torque" category exists; return its term_id.
 * ---------------------------------------------------------------------- */

$digicars_cat_id = 0;
$digicars_cat    = get_term_by( 'slug', 'car-torque', 'category' );
if ( $digicars_cat instanceof WP_Term ) {
	$digicars_cat_id = (int) $digicars_cat->term_id;
} else {
	$digicars_cat_result = wp_insert_term(
		'Car Torque',
		'category',
		array(
			'slug'        => 'car-torque',
			'description' => 'Car buying advice, finance tips and model news from Digicars.',
		)
	);
	if ( ! is_wp_error( $digicars_cat_result ) ) {
		$digicars_cat_id = (int) $digicars_cat_result['term_id'];
		digicars_blog_log( 'Created category: Car Torque (#' . $digicars_cat_id . ')' );
	} else {
		// Race / already-exists: try once more by slug.
		$retry = get_term_by( 'slug', 'car-torque', 'category' );
		if ( $retry instanceof WP_Term ) {
			$digicars_cat_id = (int) $retry->term_id;
		}
	}
}

if ( $digicars_cat_id <= 0 ) {
	digicars_blog_log( '! Could not resolve the "Car Torque" category — aborting.' );
	return;
}

/* -------------------------------------------------------------------------
 * The posts. Real, SA-specific, human titles + a few paragraphs each.
 * ---------------------------------------------------------------------- */

$digicars_posts = array(

	/* 1. First-car buying guide. */
	array(
		'slug'    => 'first-car-buying-guide-south-africa',
		'title'   => 'Buying your first car in South Africa: a no-nonsense guide',
		'excerpt' => 'From budgeting beyond the sticker price to getting pre-approved finance, here is how to buy your first car without the dealership runaround.',
		'tags'    => array( 'First Car', 'Finance', 'Buying Guide' ),
		'content' => '<p>Your first car is a milestone, but it is also the moment most South Africans first run into the real cost of motoring. The sticker price is only the beginning. Before you fall in love with anything on a forecourt, work out what you can genuinely afford every month — not just the instalment, but insurance, fuel, tyres and the inevitable service.</p>
<p>A good rule of thumb is to keep your total transport cost under about 25% of your take-home pay. If a R4 500 instalment leaves you eating two-minute noodles by the 20th, it is the wrong car. Use the affordability calculator to model a few scenarios, then get pre-qualified for finance before you shop. Walking in pre-approved changes the whole conversation: you know your ceiling and you can negotiate on the car, not on your nerves.</p>
<h2>New, demo or used?</h2>
<p>A demo unit with a few thousand kilometres on the clock often hits the sweet spot — most of the new-car warranty intact, a chunk knocked off the price. A well-cared-for used car from a reputable dealer stretches your budget furthest, provided it comes with a full service history and a proper roadworthy. Whatever you choose, insist on the paperwork: service book, finance settlement status and a clear odometer.</p>
<p>Finally, do not skip the insurance quote before you sign. A R200 000 hatchback can carry wildly different premiums depending on where you park it at night and how old you are. Knowing that number up front keeps your first car a joy rather than a monthly headache.</p>',
	),

	/* 2. EV ownership in SA. */
	array(
		'slug'    => 'ev-ownership-south-africa-loadshedding',
		'title'   => 'Living with an electric car in South Africa, loadshedding and all',
		'excerpt' => 'Range anxiety, charging at home during stage 4, and whether the maths actually works. An honest look at EV ownership in SA.',
		'tags'    => array( 'EV', 'Ownership', 'Loadshedding' ),
		'content' => '<p>Electric cars have quietly become a real option in South Africa, and the question we hear most is blunt: does it actually work here, with our electricity? The honest answer is yes, with a bit of planning — and for many commuters the running-cost saving is hard to argue with.</p>
<p>Most EV owners charge at home overnight, when tariffs are lowest and the car is parked anyway. A modern EV with 400km of real-world range easily covers a week of city driving on a couple of charges. Loadshedding is less of a crisis than the headlines suggest: you simply charge in the windows when the power is on, and many owners pair the car with home solar or a battery so the schedule barely registers.</p>
<h2>Road trips take planning</h2>
<p>The longer haul is where you think ahead. The public fast-charging network along the N1, N2 and N3 has grown a lot, but it is not yet petrol-station dense. Map your stops, build in a 20-to-30-minute coffee break at a DC charger, and the Cape run is entirely doable — just not something you do on a whim with a flat battery.</p>
<p>On the numbers, a home charge costs a fraction of a tank of petrol per kilometre, and an EV has far fewer moving parts to service. The catch is the higher purchase price. If you drive a lot and can charge at home, the total cost of ownership increasingly stacks up. If you do mostly low mileage and street-park, the case is weaker. As always, run your own kilometres through the maths before the showroom romance takes over.</p>',
	),

	/* 3. Finance tips. */
	array(
		'slug'    => 'car-finance-tips-balloon-payments',
		'title'   => 'Car finance decoded: deposits, balloons and the deal behind the deal',
		'excerpt' => 'Balloon payments, residuals and that tempting low monthly instalment — what the finance fine print really means for your pocket.',
		'tags'    => array( 'Finance', 'Buying Guide', 'Money' ),
		'content' => '<p>The advertised monthly instalment is the most quoted and least understood number in car buying. Dealers can make almost any car look affordable by stretching the term and parking a balloon at the end. Understanding the levers behind that figure is the single best way to avoid overpaying.</p>
<h2>The deposit</h2>
<p>A bigger deposit lowers the amount you finance and therefore the interest you pay over the life of the deal. Even 10% makes a meaningful dent. If you can put down more without draining your emergency fund, do it.</p>
<h2>The balloon (or residual)</h2>
<p>A balloon payment is a lump sum — often 20% to 35% of the price — pushed to the end of the contract to shrink your monthly figure. It feels great until the final month, when you owe a large amount on a car that is now worth less. You either pay it cash, refinance it, or trade in and hope the value covers it. Treat a balloon as borrowing you still have to settle, not free money.</p>
<p>Watch the interest rate and the term, too. A linked rate moves with the repo rate, so your instalment can climb. A 72-month term lowers the monthly but means more interest overall and longer in negative equity. Wherever you can, submit your application to several banks at once — through Digicars we do exactly that — so you are comparing real offers rather than accepting the first one. The cheapest-looking deal on the screen is rarely the cheapest deal in your bank account.</p>',
	),

	/* 4. Industry / model news. */
	array(
		'slug'    => 'chinese-brands-changing-sa-new-car-market',
		'title'   => 'How Chery, Omoda and GWM rewrote the SA new-car price list',
		'excerpt' => 'A wave of Chinese brands has reset what buyers expect for their money. Here is what it means if you are shopping for a new car right now.',
		'tags'    => array( 'Industry', 'New Models', 'Buying Guide' ),
		'content' => '<p>Walk into any new-car conversation in South Africa today and a name like Chery, Omoda, Haval or GWM comes up within minutes. A few years ago these brands were a curiosity. Now they are reshaping the price list — and forcing the established players to sharpen their pencils.</p>
<p>The pitch is simple and effective: a lot of car for the money. Panoramic sunroofs, big touchscreens, driver-assistance kit and lengthy warranties that used to live in premium territory now arrive on sensibly priced SUVs. For buyers who were being squeezed out of the new-car market by rising prices, that value has been a genuine lifeline.</p>
<h2>What about resale and parts?</h2>
<p>The fair questions are longevity and resale. The brands have answered with long warranties — frequently five years and beyond — and a fast-growing dealer and parts footprint across the country. Resale values are still settling as these cars build a track record on the used market, so factor that into your sums if you plan to sell after three or four years.</p>
<p>For the established marques, the response has been more competitive pricing and richer specification, which is good news whatever badge you ultimately choose. The practical takeaway for shoppers is to compare on total value: the price, yes, but also the warranty, the service plan, the safety rating and the kit you actually get. The market has shifted in the buyer\'s favour — make the most of it.</p>',
	),
);

/* -------------------------------------------------------------------------
 * Optional shared featured image — sideloaded best-effort from images/.
 * Resolved once, reused across posts. Missing files are skipped silently.
 * ---------------------------------------------------------------------- */

$digicars_blog_thumb_id = 0;
if ( function_exists( 'get_theme_file_path' ) ) {
	$digicars_thumb_candidates = array(
		get_theme_file_path( 'images/blog/car-torque.jpg' ),
		get_theme_file_path( 'images/hero/hero-showroom.svg' ),
		get_theme_file_path( 'images/vehicles/_default.svg' ),
	);
	foreach ( $digicars_thumb_candidates as $digicars_thumb_path ) {
		if ( ! $digicars_thumb_path || ! file_exists( $digicars_thumb_path ) ) {
			continue;
		}
		if ( ! function_exists( 'wp_generate_attachment_metadata' ) && file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		if ( ! function_exists( 'wp_insert_attachment' ) || ! function_exists( 'wp_upload_bits' ) ) {
			break; // Helpers unavailable; skip thumbnails silently.
		}
		$digicars_upload = wp_upload_bits( basename( $digicars_thumb_path ), null, file_get_contents( $digicars_thumb_path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( empty( $digicars_upload['error'] ) && ! empty( $digicars_upload['file'] ) ) {
			$digicars_filetype = wp_check_filetype( $digicars_upload['file'], null );
			$digicars_attach   = wp_insert_attachment(
				array(
					'post_mime_type' => $digicars_filetype['type'] ? $digicars_filetype['type'] : 'image/jpeg',
					'post_title'     => 'Car Torque',
					'post_content'   => '',
					'post_status'    => 'inherit',
				),
				$digicars_upload['file']
			);
			if ( $digicars_attach && ! is_wp_error( $digicars_attach ) ) {
				if ( function_exists( 'wp_generate_attachment_metadata' ) ) {
					wp_update_attachment_metadata( $digicars_attach, wp_generate_attachment_metadata( $digicars_attach, $digicars_upload['file'] ) );
				}
				$digicars_blog_thumb_id = (int) $digicars_attach;
			}
		}
		break; // Only attempt one candidate.
	}
}

/* -------------------------------------------------------------------------
 * Import loop.
 * ---------------------------------------------------------------------- */

$digicars_created = 0;
$digicars_skipped = 0;
$digicars_total   = count( $digicars_posts );

digicars_blog_log( sprintf( 'Digicars blog seeder: processing %d posts...', $digicars_total ) );

foreach ( $digicars_posts as $p ) {

	$slug = sanitize_title( $p['slug'] );

	/* ---- Idempotency: skip if a post with this slug already exists. ---- */
	$existing = get_page_by_path( $slug, 'OBJECT', 'post' );
	if ( $existing instanceof WP_Post ) {
		++$digicars_skipped;
		digicars_blog_log( sprintf( '  [skipped] #%d %s (already exists)', (int) $existing->ID, $slug ) );
		continue;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'post_title'     => $p['title'],
			'post_name'      => $slug,
			'post_content'   => $p['content'],
			'post_excerpt'   => $p['excerpt'],
			'post_category'  => array( $digicars_cat_id ),
			'comment_status' => 'closed',
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		digicars_blog_log( sprintf( '  ! FAILED %s - %s', $slug, $post_id->get_error_message() ) );
		continue;
	}

	$post_id = (int) $post_id;

	// Ensure the category sticks even if defaults interfere.
	wp_set_post_categories( $post_id, array( $digicars_cat_id ), false );

	// Tags (created on demand).
	if ( ! empty( $p['tags'] ) ) {
		wp_set_post_terms( $post_id, $p['tags'], 'post_tag', false );
	}

	// Featured image (best-effort, shared).
	if ( $digicars_blog_thumb_id > 0 ) {
		set_post_thumbnail( $post_id, $digicars_blog_thumb_id );
	}

	++$digicars_created;
	digicars_blog_log( sprintf( '  [created] #%d %s - %s', $post_id, $slug, $p['title'] ) );
}

/* -------------------------------------------------------------------------
 * Summary.
 * ---------------------------------------------------------------------- */

digicars_blog_log(
	sprintf(
		'Digicars blog seeder done: %d created, %d skipped, %d total processed.',
		$digicars_created,
		$digicars_skipped,
		$digicars_total
	)
);
