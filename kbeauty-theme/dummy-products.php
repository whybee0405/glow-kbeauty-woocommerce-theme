<?php
/**
 * Glow K-Beauty — dummy data importer.
 *
 * Run with WP-CLI:
 *   wp eval-file wp-content/themes/kbeauty-theme/dummy-products.php
 *
 * Creates: product categories (7 routine steps + Sheet Masks + Lips),
 * skin_type and skin_concern terms, 20 products with full meta and SKUs,
 * and 2–3 genuine review comments per product (ratings are computed from
 * these, so stars, counts and Product schema always agree).
 *
 * Idempotent: re-running updates existing products (matched by SKU)
 * instead of duplicating them.
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

// Give the importer enough room — shared hosts often cap at 128 MB / 30 s.
@ini_set( 'memory_limit', '512M' );
@set_time_limit( 600 );

if ( ! class_exists( 'WooCommerce' ) ) {
	echo "WooCommerce is not active — activate it first, then re-run this file.\n";
	return;
}

/* --------------------------------------------------------------------------
 * 1. Categories
 * ------------------------------------------------------------------------ */

$glow_categories = array(
	'cleansers'       => array( 'Cleansers', 'Step 01 — where every routine starts. Gel, foam and balm cleansers that take the day off without stripping your barrier.' ),
	'exfoliators'     => array( 'Exfoliators', 'Step 02 — chemical exfoliants (AHA, BHA, PHA) that clear congestion and smooth texture. Use 2–3 times a week, not daily.' ),
	'toners-essences' => array( 'Toners & Essences', 'Step 03 — the K-beauty difference-maker. Hydrating toners and treatment essences that prep skin to absorb everything after.' ),
	'serums-ampoules' => array( 'Serums & Ampoules', 'Step 04 — concentrated actives targeted at your specific concern: brightening, repair, calming, hydration.' ),
	'moisturisers'    => array( 'Moisturisers', 'Step 05 — seal the work in. Gel creams for oily skin, barrier creams for dry and sensitive, and everything between.' ),
	'eye-care'        => array( 'Eye Care', 'Step 06 — the thinnest skin on your face gets its own step. Creams and gels for fine lines, puffiness and dark circles.' ),
	'sun-care'        => array( 'Sun Care', 'Step 07 — the step that makes the other six worth doing. Korean sunscreens that feel like skincare, not paste.' ),
	'sheet-masks'     => array( 'Sheet Masks', 'The Sunday-evening step. Single-use essence-soaked masks for when your skin needs a reset, not a new product.' ),
	'lips'            => array( 'Lips', 'Lips are skin too. Overnight masks and treatments for the part of your face that gets no SPF and all the weather.' ),
);

$glow_cat_ids = array();

foreach ( $glow_categories as $slug => $data ) {
	$existing = term_exists( $slug, 'product_cat' );

	if ( $existing ) {
		$glow_cat_ids[ $slug ] = (int) $existing['term_id'];
		wp_update_term( (int) $existing['term_id'], 'product_cat', array( 'description' => $data[1] ) );
	} else {
		$term = wp_insert_term( $data[0], 'product_cat', array( 'slug' => $slug, 'description' => $data[1] ) );
		if ( ! is_wp_error( $term ) ) {
			$glow_cat_ids[ $slug ] = (int) $term['term_id'];
		}
	}
}

/* --------------------------------------------------------------------------
 * 2. Skin type & concern terms
 * ------------------------------------------------------------------------ */

$glow_skin_types = array(
	'dry'         => array( 'Dry', 'Tight after cleansing, flaky in winter. Needs humectants layered under occlusives.' ),
	'oily'        => array( 'Oily', 'Shiny by noon, congestion-prone. Needs lightweight hydration — not stripping.' ),
	'combination' => array( 'Combination', 'Oily T-zone, normal-to-dry cheeks. Most people, most of the time.' ),
	'sensitive'   => array( 'Sensitive', 'Reacts first, asks questions later. Everything tagged here is fragrance-checked.' ),
	'normal'      => array( 'Normal', 'Lucky you. Maintain the barrier and wear the sunscreen.' ),
);

$glow_concerns = array(
	'dehydrated-dull'     => array( 'Dehydrated & dull', 'Skin that drinks moisturiser and still looks tired. Humectants, essences and rice extract.' ),
	'breakouts-texture'   => array( 'Breakouts & texture', 'Congestion, bumps and post-blemish marks. BHA, tea tree and consistency.' ),
	'fine-lines-firmness' => array( 'Fine lines & firmness', 'The long game: peptides, snail mucin, retinal alternatives and daily SPF.' ),
	'sensitive-reactive'  => array( 'Sensitive & reactive', 'Calming actives, minimal ingredient lists, zero mystery fragrance.' ),
);

foreach ( $glow_skin_types as $slug => $data ) {
	if ( ! term_exists( $slug, 'skin_type' ) ) {
		wp_insert_term( $data[0], 'skin_type', array( 'slug' => $slug, 'description' => $data[1] ) );
	}
}

foreach ( $glow_concerns as $slug => $data ) {
	if ( ! term_exists( $slug, 'skin_concern' ) ) {
		wp_insert_term( $data[0], 'skin_concern', array( 'slug' => $slug, 'description' => $data[1] ) );
	}
}

/* --------------------------------------------------------------------------
 * 3. Products
 * ------------------------------------------------------------------------ */

$glow_products = array(
	array(
		'sku'         => 'GLW-CLN-001',
		'name'        => 'Low pH Good Morning Gel Cleanser',
		'brand'       => 'COSRX',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 295,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Sensitive',
		'types'       => array( 'oily', 'combination', 'sensitive' ),
		'concerns'    => array( 'breakouts-texture', 'sensitive-reactive' ),
		'actives'     => 'Tea tree leaf oil, Betaine salicylate',
		'svg'         => 'cosrx-low-ph-cleanser.svg',
		'excerpt'     => 'A slightly acidic gel cleanser that matches your skin\'s natural pH (around 5), so it cleans without that tight, squeaky feeling. The morning workhorse of a million routines.',
		'description' => "Most foaming cleansers sit at pH 9–10 — great for dishes, hard on your acid mantle. This one sits at pH 5, which is roughly where healthy skin lives, so it lifts oil and sunscreen without stripping the barrier that keeps irritants out.\n\nTea tree oil keeps congestion-prone skin calm, and a low dose of betaine salicylate gives a very gentle daily exfoliating nudge. Lather is modest by design; if you want squeaky, this isn\'t it — squeaky is the problem.\n\nHow to use: morning and evening, massage a small amount onto damp skin for 30–60 seconds, rinse lukewarm. Follow with the rest of your routine within a minute or two while skin is still damp.",
	),
	array(
		'sku'         => 'GLW-CLN-002',
		'name'        => 'Clean It Zero Cleansing Balm Original',
		'brand'       => 'Banila Co',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 425,
		'sale'        => 365,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Papaya extract, Vitamin E, Hot springs water',
		'svg'         => 'banila-clean-it-zero.svg',
		'excerpt'     => 'The sherbet-textured cleansing balm that dissolves sunscreen, makeup and the day in about 40 seconds. The first half of a proper double cleanse.',
		'description' => "If you wear SPF daily (you should — see step 07), you need an oil-based first cleanse. This balm starts as a sorbet, melts into an oil on contact, and binds to everything water can\'t: sunscreen filters, foundation, sebum.\n\nPapaya extract gives mild enzymatic exfoliation; vitamin E keeps the formula from feeling stripping. It emulsifies milky with water and rinses clean — no film, no residue to break you out.\n\nHow to use: evenings, scoop with dry hands onto a dry face. Massage one minute, add a little water to emulsify, rinse, then follow with your water-based cleanser.",
	),
	array(
		'sku'         => 'GLW-CLN-003',
		'name'        => 'Green Tea Foam Cleanser',
		'brand'       => 'Innisfree',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 240,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Combination, Oily, Normal',
		'types'       => array( 'combination', 'oily', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Green tea extract, Green tea seed oil',
		'svg'         => 'innisfree-green-tea-cleanser.svg',
		'excerpt'     => 'A creamy foam built on Jeju green tea — antioxidant-rich, properly hydrating, and gentle enough for twice-daily use.',
		'description' => "Innisfree grows its own green tea on Jeju Island and built this cleanser around a double dose of it: the extract for antioxidants and amino acids, the seed oil so the foam doesn\'t leave you tight.\n\nIt\'s the easy recommendation for combination skin — enough cleansing power for an oily T-zone, enough cushion for the cheeks.\n\nHow to use: morning and evening as your water-based cleanse. A 2cm dot foams up generously with a little water.",
	),
	array(
		'sku'         => 'GLW-EXF-001',
		'name'        => 'BHA Blackhead Power Liquid',
		'brand'       => 'COSRX',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 385,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture' ),
		'actives'     => 'Betaine salicylate 4%, Willow bark water, Niacinamide',
		'svg'         => 'cosrx-bha-power-liquid.svg',
		'excerpt'     => 'The cult blackhead treatment: 4% betaine salicylate that gets into pores and dissolves what\'s clogging them — gentler than classic salicylic acid, just slower and steadier.',
		'description' => "BHA is oil-soluble, which means it\'s the only exfoliant class that works inside the pore rather than just on the surface. This formula uses betaine salicylate — a gentler salicylic derivative — at 4%, on a base of willow bark water instead of plain water.\n\nExpect visible change around week three or four, not day two. That\'s the honest timeline for any pore work.\n\nHow to use: 2–3 evenings a week after cleansing, on dry skin. Thin layer over the T-zone or full face, wait 10 minutes, continue your routine. Always wear SPF the next morning — that\'s non-negotiable with acids.",
	),
	array(
		'sku'         => 'GLW-EXF-002',
		'name'        => 'AHA·BHA·PHA 30 Days Miracle Toner',
		'brand'       => 'Some By Mi',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 350,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture' ),
		'actives'     => 'AHA, BHA, PHA, Tea tree water 10%',
		'svg'         => 'somebymi-miracle-toner.svg',
		'excerpt'     => 'Three exfoliating acids at gentle daily-use strength on a tea-tree base. The “30 days” is marketing; the slow, steady texture change is real.',
		'description' => "This toner stacks all three acid families at low concentrations: AHA for surface cell turnover, BHA for inside the pore, PHA for hydration with the gentlest touch of the three. The base is 10% tea tree water, which is why blemish-prone skin tends to get along with it.\n\nBecause each acid is gentle, this can be used more often than a concentrated treatment like a power liquid — but start slow anyway.\n\nHow to use: start 3 evenings a week on a cotton pad or pressed in with palms, after cleansing. Build up as your skin allows. Don\'t stack it with other acids on the same night, and wear SPF daily.",
	),
	array(
		'sku'         => 'GLW-TON-001',
		'name'        => 'Supple Preparation Unscented Toner',
		'brand'       => 'Klairs',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 420,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Hyaluronic acid, Beta-glucan, Centella asiatica',
		'svg'         => 'klairs-unscented-toner.svg',
		'excerpt'     => 'The sensitive-skin benchmark: zero fragrance — not even essential oils — with hyaluronic acid and centella in a toner that layers beautifully.',
		'description' => "The original Supple Preparation toner is beloved; this is the same formula with every trace of fragrance removed, including the essential oils. If your skin has ever flared at a product that claimed to be “gentle”, start here.\n\nHyaluronic acid and beta-glucan pull water in; centella asiatica calms whatever today did to you. The texture is a slightly viscous water that layers without pilling — the “7-skin method” crowd uses exactly this.\n\nHow to use: after cleansing, pat a few drops in with palms. Repeat up to three layers on dehydrated days. Everything you apply afterwards absorbs better.",
	),
	array(
		'sku'         => 'GLW-TON-002',
		'name'        => 'Advanced Snail 96 Mucin Power Essence',
		'brand'       => 'COSRX',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 450,
		'sale'        => 0,
		'featured'    => true,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'sensitive', 'normal' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Snail secretion filtrate 96%, Sodium hyaluronate, Allantoin',
		'svg'         => 'cosrx-snail-96-essence.svg',
		'excerpt'     => '96% snail secretion filtrate. Sounds odd, feels odd for about ten seconds, works so well it\'s our most reordered product. Repair, bounce, glow.',
		'description' => "Let\'s address it: yes, snail mucin, and no, no snails are harmed — the filtrate is collected from mesh surfaces the snails wander over at night. What you get is a cocktail of glycoproteins, glycolic acid and zinc that skin treats like a repair signal.\n\nPeople reach for it after breakouts, after too much sun, after over-exfoliating, or just for the specific bouncy glow it gives. The texture is a stretchy gel that absorbs faster than it has any right to.\n\nHow to use: after toner, one pump pressed into the whole face. Morning and evening. It plays well with everything, acids and retinoids included.",
	),
	array(
		'sku'         => 'GLW-TON-003',
		'name'        => 'Time Revolution First Treatment Essence',
		'brand'       => 'Missha',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 690,
		'sale'        => 590,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Normal, Combination',
		'types'       => array( 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Fermented yeast extract 90%, Niacinamide, Adenosine',
		'svg'         => 'missha-first-treatment-essence.svg',
		'excerpt'     => 'The famous “first essence”: 90% fermented yeast extract applied straight after cleansing. Dullness\'s natural enemy, and the step that makes skin drink everything after it.',
		'description' => "First treatment essences are a category K-beauty invented: fermented, watery, applied before everything else, on the logic that fermentation breaks nutrients into pieces small enough for skin to actually use.\n\nThis is the best-known of them all — 90% fermented yeast extract with niacinamide for brightening and adenosine for elasticity. The difference shows up as glow and evenness over weeks, not overnight. We discount it occasionally because the bottle lasts months and we\'d rather you try it.\n\nHow to use: the very first thing on skin after cleansing (before toner). Pour a coin-sized amount into palms and press in.",
	),
	array(
		'sku'         => 'GLW-SRM-001',
		'name'        => 'Yuja Niacin 30 Days Brightening Serum',
		'brand'       => 'Some By Mi',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 395,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'combination', 'normal', 'oily' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Yuja extract 82%, Niacinamide 5%, Arbutin',
		'svg'         => 'somebymi-yuja-niacin-serum.svg',
		'excerpt'     => 'Yuja citron — the ingredient our accent colour is named after — at 82%, with 5% niacinamide. A vitamin-C-adjacent brightener that doesn\'t throw tantrums in sunlight.',
		'description' => "Yuja is a Korean citron with one of the highest natural vitamin C contents of any citrus, and unlike pure L-ascorbic acid it doesn\'t oxidise into uselessness in a month. Stacked with 5% niacinamide and arbutin, this serum works on post-blemish marks, sun spots and general dullness from three directions at once.\n\nIt\'s the brightening serum we suggest first because it rarely irritates and pairs with almost anything.\n\nHow to use: after toner/essence, morning or evening, 2–3 drops. If using in the morning, SPF after — always, but especially with brightening actives.",
	),
	array(
		'sku'         => 'GLW-SRM-002',
		'name'        => 'Freshly Juiced Vitamin Drop',
		'brand'       => 'Klairs',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 430,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Normal, Dry',
		'types'       => array( 'sensitive', 'normal', 'dry' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'Ascorbic acid 5%, Centella asiatica, Yuja extract',
		'svg'         => 'klairs-vitamin-drop.svg',
		'excerpt'     => 'Vitamin C for people vitamin C usually hates: 5% ascorbic acid buffered with centella, designed as a first C serum for reactive skin.',
		'description' => "Most vitamin C serums run 15–20% and sting accordingly. This one runs 5% on purpose: strong enough to brighten and support collagen over time, mild enough that sensitive skin can build tolerance instead of building resentment.\n\nCentella and yuja round it out. If you\'ve tried a strong C and given up, this is the on-ramp.\n\nHow to use: evenings to start, 2–3 drops after toner. Once your skin shrugs at it, you can move it to mornings under SPF. Slight warmth on application is normal; burning is not.",
	),
	array(
		'sku'         => 'GLW-SRM-003',
		'name'        => 'Cicapair Tiger Grass Serum',
		'brand'       => 'Dr.Jart+',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 640,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Sensitive, Dry',
		'types'       => array( 'sensitive', 'dry' ),
		'concerns'    => array( 'sensitive-reactive' ),
		'actives'     => 'Centella asiatica complex, Panthenol, Madecassoside',
		'svg'         => 'drjart-cicapair-serum.svg',
		'excerpt'     => 'The redness specialist. A concentrated centella complex that talks reactive skin down from whatever ledge today put it on.',
		'description' => "“Cica” is centella asiatica — tiger grass, so named because injured tigers reportedly rolled in it. Dr.Jart+ built its entire Cicapair line on a concentrated complex of it, and this serum is the line\'s workhorse: madecassoside and panthenol calming visible redness while the barrier repairs underneath.\n\nIt\'s what we recommend after retinoid overdoes, windburn, or any stretch where your skin is just angry.\n\nHow to use: after toner, morning and evening, alone or under moisturiser. Plays well under makeup once absorbed.",
	),
	// ── Moisturisers ──────────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-MST-004',
		'name'        => 'Oat-in Calming Gel Cream',
		'brand'       => 'Purito',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 355,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Oily, Combination',
		'types'       => array( 'sensitive', 'oily', 'combination' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Oat kernel extract, Centella, Beta-glucan',
		'svg'         => '_default.svg',
		'excerpt'     => 'A lightweight, oat-forward gel cream for reactive skin that wants moisture without weight — calming in texture and in feel.',
		'description' => "Oat extract has one of the strongest evidence bases for soothing sensitive and atopic skin. Purito wraps it in a gel-cream texture that works through Joburg summer without a trace of greasiness.",
	),
	array(
		'sku'         => 'GLW-MST-005',
		'name'        => 'Goodbye Redness Centella Cream',
		'brand'       => 'Benton',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 380,
		'sale'        => 325,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry',
		'types'       => array( 'sensitive', 'dry' ),
		'concerns'    => array( 'sensitive-reactive' ),
		'actives'     => 'Centella asiatica 67%, Madecassic acid, Panthenol',
		'svg'         => '_default.svg',
		'excerpt'     => '67% centella in a rich cream that visibly calms redness over a week of use — the name is not a boast, it is a timeline.',
		'description' => "Benton put almost nothing else in this cream besides centella asiatica extract and the key compounds that make centella work: madecassic acid, asiaticoside, panthenol. For skin post-procedure, post-flare, or post-whatever-the-weather-did.",
	),
	array(
		'sku'         => 'GLW-MST-006',
		'name'        => 'Midnight Blue Rich Cream',
		'brand'       => 'Klairs',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 490,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry',
		'types'       => array( 'sensitive', 'dry' ),
		'concerns'    => array( 'sensitive-reactive', 'fine-lines-firmness' ),
		'actives'     => 'Guaiazulene, Squalane, Evening primrose oil',
		'svg'         => '_default.svg',
		'excerpt'     => 'Klairs\' most calming moisturiser — guaiazulene gives it the midnight blue colour and does the redness-reducing work while squalane seals the barrier.',
		'description' => "Guaiazulene is an azulene compound from chamomile that turns the cream distinctively blue and calms inflamed skin. Rich enough for dry skin in winter, absorbed cleanly enough for sensitive skin that reacts to heavy occlusives.",
	),
	array(
		'sku'         => 'GLW-MST-007',
		'name'        => 'Hyaluronic Acid Moist Cream',
		'brand'       => 'Isntree',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 375,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Hyaluronic acid 7 types, Ceramide, Squalane',
		'svg'         => '_default.svg',
		'excerpt'     => 'Seven forms of hyaluronic acid in a cream that hydrates at every layer, sealed with ceramide and squalane — the full moisture system in one jar.',
		'description' => "Isntree went deeper than most by using seven molecular weights of HA. Combined with ceramide and squalane, this cream seals in whatever the serums below have built. Fragrance-free, suitable for all skin types, no silicones.",
	),
	array(
		'sku'         => 'GLW-MST-008',
		'name'        => 'Ceramide Ato Concentrate Cream',
		'brand'       => 'Illiyoon',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 315,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Sensitive',
		'types'       => array( 'dry', 'sensitive' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Ceramide NP, EGL ceramide, Shea butter',
		'svg'         => '_default.svg',
		'excerpt'     => 'The atopic-skin standard: a thick, ceramide-heavy cream that rebuilds the barrier for dry and eczema-prone skin without fragrance or unnecessary additives.',
		'description' => "Korean dermatologists reach for Illiyoon almost reflexively for atopic and very dry skin. The EGL ceramide complex mimics the skin\'s own lipid bilayer composition. Apply to damp skin for maximum occlusion.",
	),
	array(
		'sku'         => 'GLW-MST-009',
		'name'        => 'Oil-Free Ultra Moisturising Lotion',
		'brand'       => 'COSRX',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 340,
		'sale'        => 290,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'dehydrated-dull', 'breakouts-texture' ),
		'actives'     => 'Birch sap, Hyaluronic acid, Sodium PCA',
		'svg'         => '_default.svg',
		'excerpt'     => 'COSRX\'s answer for oily skin that still needs moisturiser: a completely oil-free lotion that hydrates without contributing to shine or congestion.',
		'description' => "The barrier needs water-based hydration even when it makes its own oil — skipping moisturiser causes more sebum production, not less. This lotion is the education and the solution: genuinely oil-free, genuinely moisturising.",
	),
	array(
		'sku'         => 'GLW-MST-010',
		'name'        => 'Dynasty Cream',
		'brand'       => 'Beauty of Joseon',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 460,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, Combination',
		'types'       => array( 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'fine-lines-firmness', 'dehydrated-dull' ),
		'actives'     => 'Rice extract 30%, Ginseng root extract, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => 'A richly textured court-inspired cream — rice, ginseng and niacinamide — that reads as a luxury moisturiser at an accessible price.',
		'description' => "Beauty of Joseon draws on Joseon dynasty beauty records for its formulas. This cream centres rice and ginseng — both documented in court skincare manuscripts — alongside niacinamide for brightness. The texture is substantial without being heavy.",
	),
	array(
		'sku'         => 'GLW-MST-011',
		'name'        => 'Mugwort Cream',
		'brand'       => "I'm From",
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 530,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry, Combination',
		'types'       => array( 'sensitive', 'dry', 'combination' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Mugwort extract 42%, Ceramide, Beta-glucan',
		'svg'         => '_default.svg',
		'excerpt'     => '42% mugwort — artemisia used in traditional Korean medicine for inflammation — in a cream that calms sensitive skin and restores barrier integrity overnight.',
		'description' => "I'm From sources its mugwort from Korean farms and cold-extracts it to preserve the flavonoids. The cream smells faintly herbal and absorbs fully by morning. For skin that runs hot, reacts to wind, or just had a bad week.",
	),
	array(
		'sku'         => 'GLW-MST-012',
		'name'        => 'Dive-In Hyaluronic Acid Cream',
		'brand'       => 'Torriden',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 390,
		'sale'        => 330,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'normal', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Low molecular hyaluronic acid, Ceramide, Panthenol',
		'svg'         => '_default.svg',
		'excerpt'     => 'The cream version of Torriden\'s bestselling serum — low molecular HA sealed with ceramide in a texture suitable for every skin type in every season.',
		'description' => "Designed as the natural companion to the Dive-In serum. Use both and the HA penetration you built with the serum is sealed in by the cream. Works as a standalone if you prefer a single hydration step.",
	),
	array(
		'sku'         => 'GLW-MST-013',
		'name'        => 'Ceramidin Cream',
		'brand'       => 'Dr.Jart+',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 720,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Sensitive',
		'types'       => array( 'dry', 'sensitive' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => '5-Cera Complex, Ceramide, Cholesterol',
		'svg'         => '_default.svg',
		'excerpt'     => 'Dr.Jart+\'s flagship barrier cream — a dermatologist-developed ceramide complex that rebuilds the skin\'s lipid layer with clinical precision.',
		'description' => "The 5-Cera Complex mimics the ratio of ceramides, cholesterol and fatty acids found in healthy skin. It is the premium answer for a compromised or chronically dry barrier. Results are measurable in transepidermal water loss reduction within a week.",
	),
	array(
		'sku'         => 'GLW-MST-014',
		'name'        => 'Vitamin A-Mazing Bakuchiol Night Cream',
		'brand'       => 'By Wishtrend',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 520,
		'sale'        => 445,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Normal, Dry, Combination',
		'types'       => array( 'normal', 'dry', 'combination' ),
		'concerns'    => array( 'fine-lines-firmness' ),
		'actives'     => 'Bakuchiol 1%, Retinyl propionate, Squalane',
		'svg'         => '_default.svg',
		'excerpt'     => 'A retinoid night cream for users who want anti-ageing results but find pure retinol too irritating — bakuchiol and retinyl propionate working in tandem.',
		'description' => "Retinyl propionate is the mildest retinoid ester — it converts to retinoic acid in skin but at a slow rate that limits irritation. Bakuchiol adds plant-based retinol pathway stimulation. Use as your final step on evenings you skip actives.",
	),
	array(
		'sku'         => 'GLW-MST-015',
		'name'        => '1025 Dokdo Cream',
		'brand'       => 'Round Lab',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 350,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Combination, Normal',
		'types'       => array( 'dry', 'combination', 'normal' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'Dokdo sea water minerals, Hyaluronic acid, Adenosine',
		'svg'         => '_default.svg',
		'excerpt'     => 'Mineral-dense deep sea water from Dokdo Island in a lightweight cream that replaces lost electrolytes and seals moisture — for skin that feels depleted.',
		'description' => "Round Lab harvests and purifies deep sea water from 1025m below the surface — the number in the product name. The mineral profile supports barrier function where freshwater-based moisturisers don\'t. A unique formula without a direct comparison.",
	),
	// ── Eye Care ──────────────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-EYE-003',
		'name'        => 'Advanced Snail Peptide Eye Cream',
		'brand'       => 'COSRX',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 380,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'sensitive' ),
		'concerns'    => array( 'fine-lines-firmness' ),
		'actives'     => 'Snail secretion filtrate 70%, Argireline, Syn-Ake peptide',
		'svg'         => '_default.svg',
		'excerpt'     => 'COSRX applies their snail expertise to the eye zone — 70% filtrate with two peptides that target expression lines without requiring a needle.',
		'description' => "Argireline and Syn-Ake both work on neuromuscular signalling to relax expression wrinkles. At 70% snail filtrate they have a well-hydrated medium to work from. Apply morning and night; results on crow\'s feet appear around six weeks.",
	),
	array(
		'sku'         => 'GLW-EYE-004',
		'name'        => 'Cica Peptide Anti-Wrinkle Eye Cream',
		'brand'       => 'Some By Mi',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 310,
		'sale'        => 265,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'sensitive' ),
		'concerns'    => array( 'fine-lines-firmness', 'sensitive-reactive' ),
		'actives'     => 'Centella asiatica, Matrixyl peptide, Adenosine',
		'svg'         => '_default.svg',
		'excerpt'     => 'Centella for calm, Matrixyl for firmness, adenosine for elasticity — the three-target eye cream at a price you can actually use enough of.',
		'description' => "Most eye creams are rationed to the point of uselessness. At this price, you can apply properly — a rice grain per eye, morning and night. Centella keeps the delicate periorbital skin from reacting to the peptides.",
	),
	array(
		'sku'         => 'GLW-EYE-005',
		'name'        => 'Centella Hyalu-Cica Eye Cream',
		'brand'       => 'Skin1004',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 295,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal' ),
		'concerns'    => array( 'fine-lines-firmness', 'sensitive-reactive' ),
		'actives'     => 'Centella asiatica, Hyaluronic acid 5 types, Madecassoside',
		'svg'         => '_default.svg',
		'excerpt'     => 'Skin1004\'s single-ingredient centella philosophy applied to the eye zone — pure, unfussy, and surprisingly effective on fine lines.',
		'description' => "If you react to most eye creams, start here. Centella does the calming and the repair work; five molecular weights of HA do the plumping. Vegan, fragrance-free, and light enough to go under concealer without balling up.",
	),
	array(
		'sku'         => 'GLW-EYE-006',
		'name'        => 'Fermentation Eye Cream',
		'brand'       => 'Benton',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 420,
		'sale'        => 360,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Normal, Dry, Combination',
		'types'       => array( 'normal', 'dry', 'combination' ),
		'concerns'    => array( 'fine-lines-firmness', 'dehydrated-dull' ),
		'actives'     => 'Galactomyces ferment filtrate, Bifida ferment lysate, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => 'Dual-fermented eye cream — galactomyces and bifida lysate — for fine lines and dullness around the eyes with the penetrating benefit of fermented actives.',
		'description' => "Fermented ingredients are broken into smaller molecules that reach deeper skin layers. Around the eye, where skin is thinnest, this matters. Benton\'s dual-ferment formula is the anti-ageing eye cream for people who take ingredient science seriously.",
	),
	array(
		'sku'         => 'GLW-EYE-007',
		'name'        => 'Water Bank Blue Hyaluronic Eye Cream',
		'brand'       => 'Laneige',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 540,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Normal, All skin types',
		'types'       => array( 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Blue hyaluronic acid (micro-sized), Squalane, Ceramide',
		'svg'         => '_default.svg',
		'excerpt'     => 'Laneige brings their micro-sized blue HA technology to the eye zone — the same plumping depth as the face cream, targeted around the orbital bone.',
		'description' => "Laneige\'s fermentation-reduced HA reaches into the dermis rather than sitting on the surface. Around the eye, where water loss is highest, this targeted hydration reduces the look of fine lines within two weeks.",
	),
	array(
		'sku'         => 'GLW-EYE-008',
		'name'        => 'Ceramidin Eye Cream',
		'brand'       => 'Dr.Jart+',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 610,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Sensitive',
		'types'       => array( 'dry', 'sensitive' ),
		'concerns'    => array( 'fine-lines-firmness', 'sensitive-reactive' ),
		'actives'     => '5-Cera Complex, Peptides, Adenosine',
		'svg'         => '_default.svg',
		'excerpt'     => 'Dr.Jart+\'s ceramide science around the eye — the barrier rebuild of the Ceramidin Cream applied to the zone that loses water fastest.',
		'description' => "The 5-Cera Complex that makes the Ceramidin Cream a dermatologist favourite is here concentrated into an eye cream. Barrier support plus peptides and adenosine for firmness. The clinical eye cream for seriously dry or compromised skin.",
	),
	array(
		'sku'         => 'GLW-EYE-009',
		'name'        => 'Hyaluronic Acid Eye Serum',
		'brand'       => 'Isntree',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 330,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'sensitive', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Hyaluronic acid (5 molecular weights), Niacinamide, Adenosine',
		'svg'         => '_default.svg',
		'excerpt'     => 'A lightweight eye serum for those who find eye creams too heavy — full-depth HA hydration in a texture that sits cleanly under makeup.',
		'description' => "Eye creams are not always the right format. This serum applies in seconds, dries without residue, and layers under anything. All five molecular weights of HA are present; niacinamide works on dark circles. The lightweight path to the same results.",
	),
	array(
		'sku'         => 'GLW-EYE-010',
		'name'        => 'Intensive Vital Eye Serum',
		'brand'       => 'Missha',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 370,
		'sale'        => 315,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Normal, Dry, Combination',
		'types'       => array( 'normal', 'dry', 'combination' ),
		'concerns'    => array( 'fine-lines-firmness' ),
		'actives'     => 'Fermented yeast extract, Collagen, Elastin',
		'svg'         => '_default.svg',
		'excerpt'     => 'Fermented yeast at the same strength as Missha\'s famous first treatment essence, applied specifically to the eye zone for firmness and luminosity.',
		'description' => "Missha extends their fermented yeast expertise to the eye zone: the same complex that makes the First Treatment Essence a glow staple is here concentrated around the orbital bone, with collagen and elastin for structural support.",
	),
	array(
		'sku'         => 'GLW-MST-001',
		'name'        => 'Water Bank Blue Hyaluronic Cream',
		'brand'       => 'Laneige',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 750,
		'sale'        => 0,
		'featured'    => true,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Normal, Combination',
		'types'       => array( 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Blue hyaluronic acid, Squalane, Ceramides',
		'svg'         => 'laneige-water-bank-cream.svg',
		'excerpt'     => 'Laneige\'s flagship moisturiser: micro-sized “blue” hyaluronic acid that gets past the surface, sealed with squalane and ceramides. Eight weeks of water, one jar.',
		'description' => "Standard hyaluronic acid molecules are too large to go anywhere — they sit on top, grab moisture, and leave with it when the air is dry (hello, Joburg winter). Laneige ferments theirs down to fragments small enough to settle into the upper skin layers and stay.\n\nThe cream itself is a quiet achiever: rich enough for dry skin, light enough that combination skin won\'t slide off the pillow. Squalane and ceramides do the sealing.\n\nHow to use: final skincare step at night, second-to-last in the morning (SPF after). A blueberry-sized amount does the whole face and neck.",
	),
	array(
		'sku'         => 'GLW-MST-002',
		'name'        => 'SoonJung 2x Barrier Intensive Cream',
		'brand'       => 'Etude',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 360,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry',
		'types'       => array( 'sensitive', 'dry' ),
		'concerns'    => array( 'sensitive-reactive' ),
		'actives'     => 'Panthenol 5%, Madecassoside, Camellia oil',
		'svg'         => 'etude-soonjung-cream.svg',
		'excerpt'     => 'A 93% naturally-derived barrier cream with 5% panthenol and almost nothing else — no fragrance, no essential oils, no drama. Sensitive skin\'s security blanket.',
		'description' => "SoonJung (“pure”) is Etude\'s sensitive-skin line, and this cream is its centrepiece: a short, considered ingredient list led by 5% panthenol for repair and madecassoside for calm.\n\nThere is nothing exciting about it, which is exactly the point. When your skin is reactive, exciting is the enemy. It\'s also the cream we suggest buffering strong actives with.\n\nHow to use: as your moisturiser, morning and night. During a flare-up, use it generously and pause your actives until things settle.",
	),
	array(
		'sku'         => 'GLW-MST-003',
		'name'        => 'Hydra B5 Soothing Cream',
		'brand'       => 'AHC',
		'cat'         => 'moisturisers',
		'step'        => 5,
		'price'       => 445,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Combination, Oily, Normal',
		'types'       => array( 'combination', 'oily', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Panthenol (B5), Hyaluronic acid, Trehalose',
		'svg'         => 'ahc-hydra-b5-cream.svg',
		'excerpt'     => 'The gel-cream for skin that wants moisture without weight. Vitamin B5 and hyaluronic acid in a texture that disappears in seconds.',
		'description' => "AHC made its name in Korean aesthetic clinics before it ever hit shelves, and this B5 gel-cream is the formula that crossed over. Panthenol and hyaluronic acid hydrate and soothe; trehalose — the sugar that lets desert plants survive drought — helps skin hold what it\'s given.\n\nThe texture is the selling point: a bouncy gel that sinks in fully, no film, no shine. Oily and combination skin, this is your moisturiser argument settled.\n\nHow to use: morning and evening as the final hydrating step. Layer a richer cream over it in deep winter if needed.",
	),
	array(
		'sku'         => 'GLW-EYE-001',
		'name'        => 'Ageless Real Eye Cream For Face',
		'brand'       => 'AHC',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 340,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'combination', 'normal', 'sensitive' ),
		'concerns'    => array( 'fine-lines-firmness' ),
		'actives'     => 'Peptide complex, Niacinamide, Adenosine',
		'svg'         => 'ahc-ageless-eye-cream.svg',
		'excerpt'     => 'The eye cream so gentle Korean aestheticians use it on the whole face — hence the name. Peptides and adenosine for fine lines, at a price that lets you actually use enough.',
		'description' => "“For Face” isn\'t a typo. AHC formulated this gently enough to use anywhere, then watched half of Korea do exactly that. The peptide complex and adenosine target elasticity and fine lines; niacinamide brightens the under-eye over time.\n\nEye creams fail because people ration them. At this price, don\'t. Use it twice a day, use enough, and give it the eight weeks the actives need.\n\nHow to use: after serum, pat — never rub — a rice-grain amount around each orbital bone. Morning and night. Whole face if you\'re feeling Korean about it.",
	),
	array(
		'sku'         => 'GLW-EYE-002',
		'name'        => 'Fundamental Eye Awakening Gel',
		'brand'       => 'Klairs',
		'cat'         => 'eye-care',
		'step'        => 6,
		'price'       => 495,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types, Sensitive',
		'types'       => array( 'sensitive', 'normal', 'combination', 'oily' ),
		'concerns'    => array( 'fine-lines-firmness', 'sensitive-reactive' ),
		'actives'     => 'Caffeine, Green tea EGCG, Peptides',
		'svg'         => 'klairs-eye-awakening-gel.svg',
		'excerpt'     => 'Caffeine and green tea EGCG in a cooling gel with a metal applicator tip. For puffy mornings and screen-tired eyes — the 6am meeting in a tube.',
		'description' => "Where most eye products chase lines, this one chases mornings: caffeine constricts the puffiness, EGCG from green tea handles the antioxidant work, and the rounded metal tip does cold-spoon duty on the way in.\n\nIt\'s vegan, fragrance-free, and light enough to wear under concealer without pilling.\n\nHow to use: mornings (and after long screen days), draw a small line under each eye with the tip, then pat in with your ring finger. Keep it in the fridge for the full effect — we\'re serious, it\'s better cold.",
	),
	array(
		'sku'         => 'GLW-SUN-001',
		'name'        => 'Daily UV Defense Sunscreen SPF36+',
		'brand'       => 'Innisfree',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 330,
		'sale'        => 0,
		'featured'    => true,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'sensitive', 'normal' ),
		'concerns'    => array( 'fine-lines-firmness' ),
		'actives'     => 'UV filters SPF36 PA++, Green tea extract, Sunflower oil',
		'svg'         => 'innisfree-daily-uv-spf36.svg',
		'excerpt'     => 'The sunscreen that converts sunscreen-haters: a weightless daily lotion that disappears with zero white cast and zero greasy film. SPF you\'ll actually reapply.',
		'description' => "Here is the truth about steps one through six: they\'re wasted money if you skip step seven. UV does more visible ageing than everything else combined, and the Highveld sun does not take winter off.\n\nKorean sunscreens won the world over by feeling like moisturisers instead of paste, and this Innisfree is the gateway: light lotion texture, no white cast on any skin tone, no sting around the eyes, sits beautifully under makeup.\n\nHow to use: every morning, last skincare step, two finger-lengths for face and neck. Reapply at lunch if you\'re outdoors. Yes, also when it\'s cloudy. Yes, also in winter.",
	),
	array(
		'sku'         => 'GLW-SUN-002',
		'name'        => 'All Around Safe Block Essence Sun Milk SPF50+',
		'brand'       => 'Missha',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 310,
		'sale'        => 265,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Oily, Combination, Normal',
		'types'       => array( 'oily', 'combination', 'normal' ),
		'concerns'    => array( 'breakouts-texture' ),
		'actives'     => 'UV filters SPF50+ PA+++, Rice bran extract, Centella asiatica',
		'svg'         => 'missha-safe-block-sun-milk.svg',
		'excerpt'     => 'Maximum protection in a sweat-resistant milk that sets matte. The pick for oily skin, sport, and proper South African summer.',
		'description' => "When SPF36 daily-wear isn\'t enough — beach days, hiking, cricket on the weekend — this is the step up: SPF50+ PA+++ in a fluid milk that sets to a soft matte finish and holds on through sweat.\n\nRice bran extract and centella keep it from feeling like a chemical exercise, and the matte set makes it the default recommendation for oily skin even on office days.\n\nHow to use: shake first. Apply generously as your final morning step, 15 minutes before sun. Reapply every two hours of direct exposure — no sunscreen on earth is once-a-day at the beach, whatever the bottle implies.",
	),
	// ── Sun Care ──────────────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-SUN-003',
		'name'        => 'Relief Sun Rice + Probiotics SPF50+ PA++++',
		'brand'       => 'Beauty of Joseon',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 380,
		'sale'        => 0,
		'featured'    => true,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'sensitive', 'normal' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'UV filters SPF50+ PA++++, Rice extract, Niacinamide, Lactobacillus',
		'svg'         => '_default.svg',
		'excerpt'     => 'The sunscreen that broke the internet — rice extract and probiotics in an SPF50+ PA++++ formula that feels like a serum and photographs without flash-back.',
		'description' => "Beauty of Joseon\'s Relief Sun became the most-discussed sunscreen in K-beauty because it is genuinely good: maximum protection in a formula with zero white cast, zero pill under makeup, and rice niacinamide to brighten as it protects. The benchmark we measure other sunscreens against.",
	),
	array(
		'sku'         => 'GLW-SUN-004',
		'name'        => 'Birch Juice Moisturising Sunscreen SPF50+ PA++++',
		'brand'       => 'Round Lab',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 355,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, Sensitive',
		'types'       => array( 'dry', 'normal', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'UV filters SPF50+ PA++++, Birch juice 61.7%, Panthenol',
		'svg'         => '_default.svg',
		'excerpt'     => '61% birch juice as the sunscreen base — this protects at the maximum rating while hydrating dry and sensitive skin rather than sitting on top of it.',
		'description' => "Dry and sensitive skin often skips sunscreen because most formulas feel drying or irritating. Round Lab fixed that by making birch juice the primary ingredient. Same SPF50+ PA++++ rating as any medical-grade screen, but the texture of a moisturiser.",
	),
	array(
		'sku'         => 'GLW-SUN-005',
		'name'        => 'Centella Hyalu-Cica Water-Fit Sun Serum SPF50+ PA++++',
		'brand'       => 'Skin1004',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 340,
		'sale'        => 290,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Sensitive',
		'types'       => array( 'oily', 'combination', 'sensitive' ),
		'concerns'    => array( 'sensitive-reactive' ),
		'actives'     => 'UV filters SPF50+ PA++++, Centella asiatica, Hyaluronic acid',
		'svg'         => '_default.svg',
		'excerpt'     => 'The serum-textured SPF for oily, acne-prone and reactive skin — centella calms, HA hydrates, and zero white cast means no adjustments to your makeup routine.',
		'description' => "Skin1004 applies their no-fragrance centella-forward philosophy to sun care. The water-fit texture disappears on contact — genuinely invisible on all skin tones. A significant upgrade for anyone who has been applying less SPF than needed because of how it feels.",
	),
	array(
		'sku'         => 'GLW-SUN-006',
		'name'        => 'Daily Go-To Sunscreen SPF50+ PA++++',
		'brand'       => 'Purito',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 310,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'sensitive', 'normal' ),
		'concerns'    => array( 'sensitive-reactive' ),
		'actives'     => 'UV filters SPF50+ PA++++, Centella, Adenosine',
		'svg'         => '_default.svg',
		'excerpt'     => 'Purito\'s revised flagship sunscreen — fragrance-free, vegan, genuinely comfortable to wear — the daily SPF that removed the last excuse for not applying.',
		'description' => "Purito reformulated this sunscreen following community feedback and it is now one of the cleanest SPF50+ PA++++ formulas available: no fragrance, no alcohol, no white cast. Centella ensures it is compatible with even the most reactive routines.",
	),
	array(
		'sku'         => 'GLW-SUN-007',
		'name'        => 'Aloe Soothing Sun Cream SPF50+ PA+++',
		'brand'       => 'COSRX',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 295,
		'sale'        => 250,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'UV filters SPF50+, Aloe vera 65%, Centella',
		'svg'         => '_default.svg',
		'excerpt'     => '65% aloe vera as the sunscreen base — for skin that burns in more ways than one: redness-prone types that need sun protection without further irritation.',
		'description' => "COSRX built this around aloe for the same reason they put aloe in everything: it is one of the most evidence-backed soothing ingredients in cosmetics. At 65%, this is as much a calming treatment as it is a sunscreen. Suitable year-round for sensitive skin.",
	),
	array(
		'sku'         => 'GLW-SUN-008',
		'name'        => 'Hyaluronic Acid Watery Sun Gel SPF50+ PA++++',
		'brand'       => 'Isntree',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 345,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Normal',
		'types'       => array( 'oily', 'combination', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'UV filters SPF50+ PA++++, Hyaluronic acid 5 types, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => 'A gel-textured SPF50+ PA++++ that hydrates as it protects — five molecular weights of HA in the lightest possible sunscreen format.',
		'description' => "Isntree\'s signature multi-weight HA appears in the sunscreen step too. The gel texture sets matte, layers under makeup without interference, and makes this one of the few SPFs that actively contributes to the hydration goals of the rest of your routine.",
	),
	array(
		'sku'         => 'GLW-SUN-009',
		'name'        => 'Every Sun Day Sun Fluid SPF50+ PA++++',
		'brand'       => 'Dr.Jart+',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 520,
		'sale'        => 445,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'normal' ),
		'concerns'    => array( 'fine-lines-firmness' ),
		'actives'     => 'UV filters SPF50+ PA++++, Ceramide, Hyaluronic acid',
		'svg'         => '_default.svg',
		'excerpt'     => 'Dr.Jart+\'s premium fluid sunscreen — ceramide and hyaluronic acid in the lightest SPF50+ PA++++ formula in the line, with a silky finish that photographs beautifully.',
		'description' => "Dr.Jart+ brings its barrier science to the final skincare step: ceramide in a sunscreen formula means protection and barrier repair happen simultaneously. The fluid consistency is the most elegantly wearable SPF we stock at this protection level.",
	),
	array(
		'sku'         => 'GLW-SUN-010',
		'name'        => 'Must Waterproof Sun Milk SPF50+ PA++++',
		'brand'       => 'Etude',
		'cat'         => 'sun-care',
		'step'        => 7,
		'price'       => 280,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Oily, Combination',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture' ),
		'actives'     => 'UV filters SPF50+ PA++++, Centella, Hyaluronic acid',
		'svg'         => '_default.svg',
		'excerpt'     => 'Waterproof SPF50+ PA++++ in a milk texture built for sweat, sport and South African summer — protection that stays where you put it.',
		'description' => "For beach days, runs, and anything outdoors: Etude formulated this milk to resist sweat and water without becoming uncomfortable. Matte finish, no white cast, and the waterproofing means a two-hour outdoor reapplication instead of an hourly one.",
	),
	// ── Sheet Masks ───────────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-MSK-002',
		'name'        => 'N.M.F Aquaring Hydrating Mask — 5 Pack',
		'brand'       => 'Mediheal',
		'cat'         => 'sheet-masks',
		'step'        => 0,
		'price'       => 210,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Normal, All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Natural Moisturising Factor complex, Sodium hyaluronate, Amino acids',
		'svg'         => '_default.svg',
		'excerpt'     => 'Mediheal\'s hydration specialist: NMF complex and hyaluronic acid in a 20-minute mask that leaves skin the texture of a well-watered houseplant.',
		'description' => "NMF (natural moisturising factor) is the mix of amino acids, PCA and minerals that healthy skin produces to keep itself hydrated. This mask tops it up from the outside in 20 minutes. The dehydration mask the brand built its name on.",
	),
	array(
		'sku'         => 'GLW-MSK-003',
		'name'        => 'My Real Squeeze Mask Aloe — 5 Pack',
		'brand'       => 'Innisfree',
		'cat'         => 'sheet-masks',
		'step'        => 0,
		'price'       => 195,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Sensitive, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Jeju aloe vera, Sodium hyaluronate, Allantoin',
		'svg'         => '_default.svg',
		'excerpt'     => 'Innisfree\'s single-ingredient philosophy in mask form — pure Jeju aloe pressed into a sheet that calms and hydrates in equal measure.',
		'description' => 'The "squeeze" in the name refers to how Innisfree cold-presses fresh aloe to preserve the polysaccharides. The result is a calming, cooling sheet mask that works year-round — cooling in summer, barrier-support in winter.',
	),
	array(
		'sku'         => 'GLW-MSK-004',
		'name'        => 'AHA·BHA·PHA 30 Days Miracle Brightening Mask — 5 Pack',
		'brand'       => 'Some By Mi',
		'cat'         => 'sheet-masks',
		'step'        => 0,
		'price'       => 240,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Normal',
		'types'       => array( 'oily', 'combination', 'normal' ),
		'concerns'    => array( 'breakouts-texture', 'dehydrated-dull' ),
		'actives'     => 'AHA, BHA, PHA triple acid complex, Tea tree water, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => 'Triple-acid brightening sheet masks at a lower concentration than the leave-on — exfoliation and radiance in 20 minutes without the 10-minute wait of a liquid.',
		'description' => "Some By Mi applies the AHA·BHA·PHA logic from their toner to a sheet mask format: the occlusion of the sheet drives the actives into the skin more efficiently than a toner step alone. Use on evenings you want results faster.",
	),
	array(
		'sku'         => 'GLW-MSK-005',
		'name'        => 'Cicapair Calming Mask',
		'brand'       => 'Dr.Jart+',
		'cat'         => 'sheet-masks',
		'step'        => 0,
		'price'       => 285,
		'sale'        => 245,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Sensitive, Dry, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal' ),
		'concerns'    => array( 'sensitive-reactive' ),
		'actives'     => 'Centella asiatica complex, Panthenol, Madecassoside',
		'svg'         => '_default.svg',
		'excerpt'     => 'Dr.Jart+\'s Cicapair formula in a single-use sheet — the barrier-repair mask for post-procedure, post-beach and any day that was too much for your skin.',
		'description' => "The same cica complex that makes the Cicapair serum so effective in an intensified sheet format. 20 minutes under the mask drives the centella compounds deeper than topical application alone. Keep a pack for skin emergencies.",
	),
	array(
		'sku'         => 'GLW-MSK-006',
		'name'        => 'Glow Serum Mask Propolis + Niacinamide — 5 Pack',
		'brand'       => 'Beauty of Joseon',
		'cat'         => 'sheet-masks',
		'step'        => 0,
		'price'       => 265,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'normal', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Propolis extract 60%, Niacinamide 2%, Honey extract',
		'svg'         => '_default.svg',
		'excerpt'     => 'The bestselling Glow Serum as a sheet mask — the same propolis and niacinamide in a 20-minute format that delivers visible glow before an event.',
		'description' => "Beauty of Joseon soaks the sheet in the same 60% propolis and niacinamide formula as their famous serum. The sheet format creates occlusion that boosts absorption. Pre-event mask of choice for the glow-before-camera-crowd.",
	),
	array(
		'sku'         => 'GLW-MSK-007',
		'name'        => 'Centella Ampoule Mask — 5 Pack',
		'brand'       => 'Skin1004',
		'cat'         => 'sheet-masks',
		'step'        => 0,
		'price'       => 220,
		'sale'        => 185,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Centella asiatica leaf water 100%, Madecassoside, Asiaticoside',
		'svg'         => '_default.svg',
		'excerpt'     => 'Pure centella leaf water — the same single-ingredient formula as Skin1004\'s ampoule — pressed into a sheet mask for a 20-minute repair session.',
		'description' => "Skin1004 is consistent: everything they make is built on their 100% centella leaf water. In mask format, the sheet keeps the formula pressed against skin long enough for genuine repair, not just surface calming. Five packs last a month of weekly use.",
	),
	array(
		'sku'         => 'GLW-MSK-008',
		'name'        => 'Rich Moist Soothing Tencel Sheet Mask — 5 Pack',
		'brand'       => 'Klairs',
		'cat'         => 'sheet-masks',
		'step'        => 0,
		'price'       => 250,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'Hyaluronic acid, Centella, Willow herb extract',
		'svg'         => '_default.svg',
		'excerpt'     => 'Klairs uses Tencel (lyocell) sheets instead of cotton — softer against reactive skin and better at holding essence next to the face throughout the 20 minutes.',
		'description' => "The sheet material matters: Tencel is smoother, thinner and stays wet longer than standard cotton, meaning more essence stays on skin and less dries before you take it off. Fragrance-free formula suits the most reactive skin types.",
	),
	// ── Lips ──────────────────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-LIP-002',
		'name'        => 'Lip Sleeping Mask Vanilla',
		'brand'       => 'Laneige',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 385,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'oily', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Vanilla berry complex, Vitamin C, Murumuru butter',
		'svg'         => '_default.svg',
		'excerpt'     => 'The same beloved Lip Sleeping Mask formula in vanilla — warm, sweet and effective at waking up with soft lips every single morning.',
		'description' => "Identical mechanism to the Berry original: overnight seal with vitamin C, berry antioxidants and murumuru butter. Vanilla is the favourite for those who find Berry too sweet. The jar that lives on the bedside table.",
	),
	array(
		'sku'         => 'GLW-LIP-003',
		'name'        => 'Lip Glowy Balm Berry',
		'brand'       => 'Laneige',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 295,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'oily', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Shea butter, Berry fruit complex, Hyaluronic acid',
		'svg'         => '_default.svg',
		'excerpt'     => 'The daytime version of the Sleeping Mask — a glossy, bouncy balm that sits comfortably over lipstick or alone for hydrated-looking lips all day.',
		'description' => "Where the sleeping mask is a treatment, this balm is daily maintenance: shea, HA and the berry complex in a texture with a natural gloss finish. Stacks beautifully under or over any lip colour.",
	),
	array(
		'sku'         => 'GLW-LIP-004',
		'name'        => 'Yuja Niacin Brightening Lip Balm',
		'brand'       => 'Some By Mi',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 165,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'oily', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Yuja extract, Niacinamide, Hyaluronic acid',
		'svg'         => '_default.svg',
		'excerpt'     => 'Yuja and niacinamide brought to lip care — for lips that are not only dry but darkened at the corners, a common concern nobody talks about.',
		'description' => "Perioral hyperpigmentation responds to the same ingredients as facial dark spots. Some By Mi applies their yuja-niacinamide logic to a balm format that can be used throughout the day. One of the few lip products that addresses colour, not just moisture.",
	),
	array(
		'sku'         => 'GLW-LIP-005',
		'name'        => 'Birch Juice Moisturising Lip Balm',
		'brand'       => 'Round Lab',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 175,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'sensitive', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Birch juice, Panthenol, Jojoba seed oil',
		'svg'         => '_default.svg',
		'excerpt'     => 'Round Lab\'s birch juice in a slim balm stick — mineral-rich hydration for lips in the most portable format, no jar required.',
		'description' => "The same birch juice that goes into Round Lab\'s toner and moisturiser, here in a stick lip balm with panthenol and jojoba. Fragrance-free, lightweight and genuinely moisturising without the thick coat some lip products leave.",
	),
	array(
		'sku'         => 'GLW-LIP-006',
		'name'        => 'Shea Butter & Coconut Lip Balm',
		'brand'       => 'Benton',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 155,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, All skin types',
		'types'       => array( 'dry', 'sensitive', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Shea butter, Coconut oil, Vitamin E',
		'svg'         => '_default.svg',
		'excerpt'     => 'No frills, no fragrance — shea, coconut and vitamin E in a balm that seals seriously dry lips overnight or on the go.',
		'description' => "Benton keeps this simple because simple is often right for lip care: shea butter and coconut oil are among the most occlusive natural ingredients available, and they coat without leaving lips greasy. A reliable daily companion.",
	),
	array(
		'sku'         => 'GLW-LIP-007',
		'name'        => 'Beta-Glucan Lip Serum',
		'brand'       => 'iUNIK',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 210,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types, Sensitive',
		'types'       => array( 'sensitive', 'dry', 'normal' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Beta-glucan 90%, Hyaluronic acid, Ceramide',
		'svg'         => '_default.svg',
		'excerpt'     => 'A lip serum — not a balm — with 90% beta-glucan for genuine hydration, not just surface coating. The step between your routine and your lip product.',
		'description' => "Most lip products sit on top; this serum absorbs. 90% beta-glucan and hyaluronic acid hydrate the lip tissue itself before a balm or colour seals the work in. The lip step that rewards patience with visibly plumper, smoother lips.",
	),
	array(
		'sku'         => 'GLW-LIP-008',
		'name'        => 'Hyaluronic Lip Glow Balm',
		'brand'       => 'Isntree',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 195,
		'sale'        => 165,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'sensitive', 'normal', 'combination' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Hyaluronic acid (5 types), Jojoba oil, Calendula extract',
		'svg'         => '_default.svg',
		'excerpt'     => 'Isntree\'s five-weight HA strategy applied to lips — real hydration in a clear balm with a natural glow finish and calendula for added calm.',
		'description' => "Isntree applies the same multi-molecular-weight HA from their skincare range to lip care. Five HA sizes reach different depths of the lip tissue; jojoba oil seals them in; calendula soothes lips prone to chapping and perioral irritation.",
	),
	array(
		'sku'         => 'GLW-LIP-009',
		'name'        => 'Lip Sleeping Mask Apple Lime',
		'brand'       => 'Laneige',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 385,
		'sale'        => 325,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'oily', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Apple lime complex, Vitamin C, Murumuru butter',
		'svg'         => '_default.svg',
		'excerpt'     => 'The Laneige Sleeping Mask in the fresh, citrus variant — identical overnight treatment in a scent that suits those who find Berry too sweet and Vanilla too warm.',
		'description' => "The third scent in Laneige\'s sleeping mask collection, with the same murumuru butter, vitamin C and fruit complex actives as the original. Apple lime suits warm-weather routines and makes a particularly appreciated gift pairing with the Berry or Vanilla.",
	),
	array(
		'sku'         => 'GLW-MSK-001',
		'name'        => 'Tea Tree Essential Mask — 5 Pack',
		'brand'       => 'Mediheal',
		'cat'         => 'sheet-masks',
		'step'        => 0,
		'price'       => 225,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Oily, Combination, Sensitive',
		'types'       => array( 'oily', 'combination', 'sensitive' ),
		'concerns'    => array( 'breakouts-texture', 'sensitive-reactive' ),
		'actives'     => 'Tea tree oil, Centella asiatica, Chamomile',
		'svg'         => 'mediheal-tea-tree-mask.svg',
		'excerpt'     => 'Korea\'s best-selling sheet mask, in the tea tree version for stressed and blemish-prone skin. Twenty minutes of enforced stillness, included free.',
		'description' => "Mediheal sells over a billion sheet masks a year and this is the one pharmacies can\'t keep stocked: a cellulose sheet soaked in tea tree, centella and chamomile essence. It calms angry skin, takes the heat out of active breakouts, and forces you to sit still for twenty minutes — arguably the most underrated active ingredient in skincare.\n\nFive masks per pack, because one is never enough once you start.\n\nHow to use: after cleansing and toner, smooth onto the face for 15–20 minutes. Pat the leftover essence in (don\'t rinse), then moisturise. One to two per week, or after any day your skin would describe as “a lot”.",
	),
	// ── Cleansers ────────────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-CLN-004',
		'name'        => 'Centella Unscented Cleansing Foam',
		'brand'       => 'Purito',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 280,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry, Combination',
		'types'       => array( 'sensitive', 'dry', 'combination' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Centella asiatica 10000ppm, Panthenol, Glycerin',
		'svg'         => '_default.svg',
		'excerpt'     => 'Zero fragrance, zero essential oils — just centella at 10 000ppm in a gentle foam that cleans without registering on reactive skin.',
		'description' => "Purito pulled every potential irritant from this formula and left centella doing the calming work. The foam is modest and the pH is skin-friendly. If a cleanser has ever made your cheeks sting, this is the reset button.",
	),
	array(
		'sku'         => 'GLW-CLN-005',
		'name'        => 'Balanceful Cica Cleansing Foam',
		'brand'       => 'Torriden',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 265,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Sensitive',
		'types'       => array( 'oily', 'combination', 'sensitive' ),
		'concerns'    => array( 'breakouts-texture', 'sensitive-reactive' ),
		'actives'     => 'Cica complex, Hyaluronic acid, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => 'A cica-forward foam that controls oil without disturbing the barrier — for skin that is simultaneously congested and reactive.',
		'description' => "Torriden stacked centella with hyaluronic acid and niacinamide to address the oily-but-sensitive contradiction. Cleans thoroughly, settles immediately, and the tube lasts longer than you expect.",
	),
	array(
		'sku'         => 'GLW-CLN-006',
		'name'        => 'Rice Pure Cleansing Foam',
		'brand'       => 'Thank You Farmer',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 255,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, Combination',
		'types'       => array( 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Rice water, Rice bran extract, Ceramides',
		'svg'         => '_default.svg',
		'excerpt'     => 'Rice water and ceramides in a foam cleanser that doubles as a micro-brightening step — skin is noticeably more even over two weeks.',
		'description' => "Rice has been in Korean beauty since before K-beauty was a category. This foam uses both rice water and bran extract for gentle brightening and ceramides to keep the barrier whole after every wash.",
	),
	array(
		'sku'         => 'GLW-CLN-007',
		'name'        => 'Rich Moist Foaming Cleanser',
		'brand'       => 'Klairs',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 310,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, Sensitive',
		'types'       => array( 'dry', 'normal', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'Hyaluronic acid, Amino acids, Willow herb extract',
		'svg'         => '_default.svg',
		'excerpt'     => 'A creamy, pH-balanced foam for dry and sensitive skin that cleans without the tight feeling that sends people back to micellar water.',
		'description' => "Klairs built this for skin that finds most cleansers a net negative. Amino acids maintain the surface film; hyaluronic acid means you finish the wash with more moisture than you started. Fragrance-free as standard.",
	),
	array(
		'sku'         => 'GLW-CLN-008',
		'name'        => 'Birch Juice Moisturising Cleanser',
		'brand'       => 'Round Lab',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 290,
		'sale'        => 245,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'sensitive', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Birch juice 67%, Hyaluronic acid, Panthenol',
		'svg'         => '_default.svg',
		'excerpt'     => '67% birch juice replaces water in this gel cleanser — the result is a wash that hydrates on contact and rinses clean.',
		'description' => "Round Lab sources Korean birch juice for its amino acid and mineral content. It does what water does as a cleanser base but with measurable benefits for barrier hydration. One of the better all-skin-type cleansers we stock.",
	),
	array(
		'sku'         => 'GLW-CLN-009',
		'name'        => 'Ceramide Ato Cleanser',
		'brand'       => 'Illiyoon',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 235,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Sensitive',
		'types'       => array( 'dry', 'sensitive' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Ceramide NP, Ceramide AP, Glycerin',
		'svg'         => '_default.svg',
		'excerpt'     => 'A ceramide-loaded body-and-face cleanser for skin that loses moisture every time it touches water — the cleanser that finally ends that cycle.',
		'description' => "Illiyoon is the sensitive-skin brand of Amorepacific, designed for eczema-prone and atopic skin. Three ceramide types reinforce the barrier while the surfactants clean. Works on face and body and is gentle enough for daily use on reactive skin.",
	),
	array(
		'sku'         => 'GLW-CLN-010',
		'name'        => 'Rose Galactomyces Silky Cleansing Foam',
		'brand'       => 'iUNIK',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 245,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Normal, Combination, Dry',
		'types'       => array( 'normal', 'combination', 'dry' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Galactomyces ferment filtrate 56%, Rose extract, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => 'Galactomyces at 56% — the fermented ingredient behind some of K-beauty\'s most famous essences — in a daily cleanser for glow-focused routines.',
		'description' => "iUNIK uses galactomyces as its base rather than water. It is the same fermented yeast filtrate in luxury first essences, here in a foam cleanser at an honest price. Over weeks, skin looks brighter and more even without adding another step.",
	),
	array(
		'sku'         => 'GLW-CLN-011',
		'name'        => 'Zero Pore All Kill Cleansing Foam',
		'brand'       => 'Medicube',
		'cat'         => 'cleansers',
		'step'        => 1,
		'price'       => 320,
		'sale'        => 275,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture' ),
		'actives'     => 'Salicylic acid 0.5%, Niacinamide, Zinc PCA',
		'svg'         => '_default.svg',
		'excerpt'     => 'A BHA-spiked daily cleanser from the brand dermatologists in Korea actually recommend — targets pore congestion from the very first step.',
		'description' => "Medicube positions itself at the clinical end of K-beauty. This foam has 0.5% salicylic acid — enough for a meaningful daily anti-congestion effect without the irritation of a leave-on treatment. Good starting point before adding a separate BHA step.",
	),
	// ── Toners & Essences ─────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-TON-004',
		'name'        => 'Madagascar Centella Asiatica 100 Ampoule',
		'brand'       => 'Skin1004',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 380,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Centella asiatica leaf water 100%, Madecassoside, Asiaticoside',
		'svg'         => '_default.svg',
		'excerpt'     => 'The ingredient list is one item: 100% centella asiatica leaf water. No filler, no water, no fragrance. Pure redness-calming, barrier-building cica.',
		'description' => "Skin1004 sources Madagascan centella and presses it to a single-ingredient formula. There is nothing to react to and everything to benefit from. The go-to step after any skin procedure, sun overexposure, or reactive flare-up.",
	),
	array(
		'sku'         => 'GLW-TON-005',
		'name'        => 'Green Tea Hyaluronic Acid Toner',
		'brand'       => 'Innisfree',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 295,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'normal', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Jeju green tea 56.67%, Hyaluronic acid 5 types, Amino acids',
		'svg'         => '_default.svg',
		'excerpt'     => 'Five molecular weights of hyaluronic acid on a Jeju green tea base — hydration at every skin layer, in one toner step.',
		'description' => "Different molecular sizes of HA reach different skin depths. Innisfree stacked five to cover the full range, then built the formula on green tea extract for antioxidants. The toner equivalent of a full hydration routine in one pass.",
	),
	array(
		'sku'         => 'GLW-TON-006',
		'name'        => 'Birch Juice Moisturising Toner',
		'brand'       => 'Round Lab',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 310,
		'sale'        => 265,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, Sensitive',
		'types'       => array( 'dry', 'normal', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'Birch juice 79.92%, Panthenol, Beta-glucan',
		'svg'         => '_default.svg',
		'excerpt'     => '79% birch juice in a toner that prepares dehydrated skin for the rest of the routine — lightweight enough to layer three times without heaviness.',
		'description' => "Birch juice is richer in minerals and amino acids than water and absorbs without residue. Panthenol and beta-glucan amplify the barrier-support work. Dry and sensitive skin types reach for this before anything else.",
	),
	array(
		'sku'         => 'GLW-TON-007',
		'name'        => 'Premium Whitening Hyaluronic Acid Lotion',
		'brand'       => 'Hada Labo',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 255,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Normal, All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Super hyaluronic acid, Nano hyaluronic acid, Collagen',
		'svg'         => '_default.svg',
		'excerpt'     => 'Hada Labo\'s best-selling hydration toner — super and nano hyaluronic acid in a watery lotion with a famously addictive plumping texture.',
		'description' => "Japan's number-one skincare brand has a cult following for a reason. This toner is almost nothing but hyaluronic acid at two molecular weights, which is why it works so reliably. No fragrance, no colour — just water pulled into skin.",
	),
	array(
		'sku'         => 'GLW-TON-008',
		'name'        => 'Rice Toner',
		'brand'       => "I'm From",
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 460,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Dull, Normal, Combination',
		'types'       => array( 'normal', 'combination', 'dry' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Rice extract 77.78%, Niacinamide, Allantoin',
		'svg'         => '_default.svg',
		'excerpt'     => '77% rice extract in a toner that brightens, hydrates and preps in a single step — the simplest route to the famed "glass skin" base.',
		'description' => "I'm From sources its rice from Chungcheong Province and cold-extracts it to preserve the amino acids, vitamins and minerals that make rice a brightening staple. Niacinamide accelerates the evenness work. Apply with palms and press.",
	),
	array(
		'sku'         => 'GLW-TON-009',
		'name'        => 'Centella Green Level Calming Toner',
		'brand'       => 'Purito',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 340,
		'sale'        => 290,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Centella asiatica 49%, Hyaluronic acid, Beta-glucan',
		'svg'         => '_default.svg',
		'excerpt'     => '49% centella in a hydrating toner that de-stresses reactive skin while building the moisture foundation the rest of the routine rests on.',
		'description' => "Purito made its name on the back of this formula. Fragrance-free, almost entirely naturally-derived, and calming enough to use on broken-out or wind-burned skin. The stable middle step in any sensitive routine.",
	),
	array(
		'sku'         => 'GLW-TON-010',
		'name'        => 'Snail Bee High Content Skin',
		'brand'       => 'Benton',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 395,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Acne-prone',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture', 'fine-lines-firmness' ),
		'actives'     => 'Snail secretion filtrate 90.3%, Bee venom 0.08%, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => 'Snail filtrate at 90% with a trace of bee venom — the combination for skin dealing with active breakouts and the marks they leave behind.',
		'description' => "Bee venom in skincare acts like a mild mannequin of a sting — stimulating blood flow and collagen without actual venom effects at this concentration. Combined with 90% snail filtrate, this toner is the cult pick for blemish-prone skin that also wants anti-ageing benefits.",
	),
	array(
		'sku'         => 'GLW-TON-011',
		'name'        => 'Glow Serum Propolis + Niacinamide',
		'brand'       => 'Beauty of Joseon',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 420,
		'sale'        => 0,
		'featured'    => true,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'normal', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Propolis extract 60%, Niacinamide 2%, Honey extract',
		'svg'         => '_default.svg',
		'excerpt'     => 'The essence that launched a thousand repurchases: 60% propolis with niacinamide in a serum-textured toner for translucent, even, genuinely glowing skin.',
		'description' => "Beauty of Joseon makes traditional Korean ingredients contemporary, and propolis — the antimicrobial resin honeybees produce — is their signature. This essence feeds skin nutrients while niacinamide pulls the tone even. The bottle looks small; the results compound over months.",
	),
	array(
		'sku'         => 'GLW-TON-012',
		'name'        => 'Ceramide Ato Concentrate Toner',
		'brand'       => 'Illiyoon',
		'cat'         => 'toners-essences',
		'step'        => 3,
		'price'       => 270,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Dry, Sensitive',
		'types'       => array( 'dry', 'sensitive' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Ceramide NP, EGL ceramide complex, Hyaluronic acid',
		'svg'         => '_default.svg',
		'excerpt'     => 'A ceramide-first toner for atopic and eczema-prone skin that rebuilds the barrier at the prep step — before serums, before moisturiser.',
		'description' => "Illiyoon's EGL ceramide complex is the same barrier-supporting technology in their cream, here in a thin toner that absorbs before you layer anything. Recommended by Korean dermatologists for post-procedure recovery and chronic dry skin.",
	),
	// ── Serums & Ampoules ─────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-SRM-004',
		'name'        => 'Snail Bee High Content Ampoule',
		'brand'       => 'Benton',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 490,
		'sale'        => 420,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Acne-prone',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture', 'fine-lines-firmness' ),
		'actives'     => 'Snail secretion filtrate 70%, Bee venom 0.1%, Peptides',
		'svg'         => '_default.svg',
		'excerpt'     => 'A concentrated ampoule version of Benton\'s cult toner — snail and bee venom at higher dose for targeted post-blemish and fine-line work.',
		'description' => "Same formula logic as the Snail Bee toner but condensed into an ampoule for concentrated treatment. Use it after the toner step, targeted or all-over, when skin needs more than maintenance.",
	),
	array(
		'sku'         => 'GLW-SRM-005',
		'name'        => 'Hyaluronic Acid Serum',
		'brand'       => 'Isntree',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 360,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'sensitive', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Hyaluronic acid 50% (5 types), Centella, Ceramide',
		'svg'         => '_default.svg',
		'excerpt'     => 'Fifty percent hyaluronic acid — across five molecular weights — in a serum that does nothing but hydrate, flawlessly.',
		'description' => "No actives competing for attention, no fragrance, no colour. Just the most complete hyaluronic acid delivery system on our shelves. For skin that is well-managed but chronically thirsty, this is the answer.",
	),
	array(
		'sku'         => 'GLW-SRM-006',
		'name'        => 'Niacinamide 10% + Zinc 1% Serum',
		'brand'       => 'Cos De BAHA',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 285,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Acne-prone',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture', 'dehydrated-dull' ),
		'actives'     => 'Niacinamide 10%, Zinc PCA 1%',
		'svg'         => '_default.svg',
		'excerpt'     => 'The clinical benchmark for pore minimising and oil control: 10% niacinamide with 1% zinc PCA, nothing more.',
		'description' => "Cos De BAHA makes single-hero serums without the luxury markup. This one mirrors the combination that made The Ordinary's version famous but at comparable price with a cleaner feel. Oil control is visible within a fortnight.",
	),
	array(
		'sku'         => 'GLW-SRM-007',
		'name'        => 'Revive Serum Ginseng + Snail Mucin',
		'brand'       => 'Beauty of Joseon',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 440,
		'sale'        => 0,
		'featured'    => true,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, Combination',
		'types'       => array( 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'fine-lines-firmness', 'dehydrated-dull' ),
		'actives'     => 'Ginseng root water 25%, Snail secretion filtrate 25%, Peptides',
		'svg'         => '_default.svg',
		'excerpt'     => 'Ginseng and snail mucin at equal measure — K-beauty\'s two most-studied traditional and modern ingredients, working together on elasticity and glow.',
		'description' => "Beauty of Joseon pairs Korean ginseng (a joseon-era court beauty staple) with contemporary snail filtrate for a serum that addresses firmness and radiance simultaneously. Light texture, serious results.",
	),
	array(
		'sku'         => 'GLW-SRM-008',
		'name'        => 'Dive-In Low Molecular Hyaluronic Acid Serum',
		'brand'       => 'Torriden',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 370,
		'sale'        => 315,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'oily', 'combination', 'sensitive', 'normal' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Hyaluronic acid 5 types (low molecular), Panthenol, Centella',
		'svg'         => '_default.svg',
		'excerpt'     => 'Torriden\'s bestseller: low-molecular HA that reaches deeper skin layers faster — for the kind of dewy plump that lasts past midday.',
		'description' => 'The "low molecular" distinction matters. Smaller HA fragments penetrate further than standard HA, delivering hydration at the dermal layer rather than sitting on the surface. Five different sizes ensure full-depth coverage.',
	),
	array(
		'sku'         => 'GLW-SRM-009',
		'name'        => 'Pure Vitamin C 21.5% Advanced Serum',
		'brand'       => 'By Wishtrend',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 580,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Normal, Combination',
		'types'       => array( 'normal', 'combination' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'L-Ascorbic acid 21.5%, Vitamin E, Ferulic acid',
		'svg'         => '_default.svg',
		'excerpt'     => '21.5% pure L-ascorbic acid — clinical-strength vitamin C for experienced users who want maximum brightening and collagen results.',
		'description' => "This is not a starter vitamin C. At 21.5% pure ascorbic acid, it is the most potent formula on our shelves — used mornings under SPF by people serious about long-term anti-ageing. Vitamin E and ferulic acid extend its stability and effectiveness. Patch test, build slowly.",
	),
	array(
		'sku'         => 'GLW-SRM-010',
		'name'        => 'Centella Enriched Calming Serum',
		'brand'       => 'Purito',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 410,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry, All skin types',
		'types'       => array( 'sensitive', 'dry', 'normal' ),
		'concerns'    => array( 'sensitive-reactive', 'dehydrated-dull' ),
		'actives'     => 'Centella asiatica 49%, Hyaluronic acid, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => 'Purito\'s centella toner logic, concentrated into serum strength — for reactive skin that also wants plump, even results.',
		'description' => "The step between Purito\'s calming toner and a heavier moisturiser for sensitive skin. 49% centella plus three forms of hyaluronic acid, fragrance-free, and reassuringly boring in the best possible way.",
	),
	array(
		'sku'         => 'GLW-SRM-011',
		'name'        => 'Galactomyces Pure Vitamin C Glow Serum',
		'brand'       => 'Some By Mi',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 430,
		'sale'        => 365,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Normal, Combination, Oily',
		'types'       => array( 'normal', 'combination', 'oily' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Galactomyces ferment 76.92%, Niacinamide 10%, Vitamin C derivative',
		'svg'         => '_default.svg',
		'excerpt'     => '76% galactomyces with high-dose niacinamide and a stabilised vitamin C — the brightening serum for all three concerns at once.',
		'description' => "Some By Mi brought the same galactomyces used in luxury Korean essences into a working brightening serum. 10% niacinamide handles pores and tone; the vitamin C derivative handles surface luminosity without the instability of pure L-ascorbic acid.",
	),
	array(
		'sku'         => 'GLW-SRM-012',
		'name'        => 'Beta-Glucan Serum',
		'brand'       => 'iUNIK',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 295,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'All skin types, Sensitive',
		'types'       => array( 'dry', 'sensitive', 'normal', 'combination' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'Beta-glucan 90%, Centella asiatica, Hyaluronic acid',
		'svg'         => '_default.svg',
		'excerpt'     => '90% beta-glucan — the skin-identical polysaccharide that hydrates, heals and calms simultaneously — in a serum built for sensitive, barrier-compromised skin.',
		'description' => "Beta-glucan is derived from oats and is often called the hydrating ingredient more effective than hyaluronic acid in head-to-head studies. At 90%, this iUNIK serum has little else to do except hydrate and repair. Post-procedure favourite.",
	),
	array(
		'sku'         => 'GLW-SRM-013',
		'name'        => 'Red Lacto Collagen Ampoule',
		'brand'       => 'Some By Mi',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 465,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, Combination',
		'types'       => array( 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'fine-lines-firmness', 'dehydrated-dull' ),
		'actives'     => 'Collagen 10000ppm, Lactobacillus ferment, Red algae',
		'svg'         => '_default.svg',
		'excerpt'     => 'High-dose collagen with fermented lactobacillus and red algae — for skin focused on firmness and bounce in the serum step.',
		'description' => "Topical collagen molecules are too large to penetrate skin directly, but they form a hydrating film and signal collagen synthesis. Lactobacillus ferment prepares the skin surface to receive the formula; red algae provides mineral support.",
	),
	array(
		'sku'         => 'GLW-SRM-014',
		'name'        => 'Retinol 0.1% Bakuchiol 1% Serum',
		'brand'       => 'Cos De BAHA',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 320,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Normal, Combination, Oily',
		'types'       => array( 'normal', 'combination', 'oily' ),
		'concerns'    => array( 'fine-lines-firmness' ),
		'actives'     => 'Retinol 0.1%, Bakuchiol 1%, Squalane',
		'svg'         => '_default.svg',
		'excerpt'     => 'Retinol at an effective starter dose, buffered with bakuchiol to reduce irritation — the firmness serum for people who find retinol alone too aggressive.',
		'description' => "0.1% retinol is a real working concentration for fine lines and cell turnover. Bakuchiol — the plant-derived retinol alternative — shares the mechanism and adds synergistic calm. Squalane keeps the formula from drying. Use evenings only, always with morning SPF.",
	),
	array(
		'sku'         => 'GLW-SRM-015',
		'name'        => 'Peptide + Copper Serum',
		'brand'       => 'TIAM',
		'cat'         => 'serums-ampoules',
		'step'        => 4,
		'price'       => 498,
		'sale'        => 425,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Dry, Normal, All skin types',
		'types'       => array( 'dry', 'normal', 'combination' ),
		'concerns'    => array( 'fine-lines-firmness' ),
		'actives'     => 'Copper tripeptide-1 GHK-Cu, Matrixyl 3000, Argireline',
		'svg'         => '_default.svg',
		'excerpt'     => 'The anti-ageing specialist: copper peptide GHK-Cu with Matrixyl and Argireline — the three most evidence-backed peptides in one serum.',
		'description' => "Copper tripeptide-1 is the most studied regenerative peptide in cosmetic science; Matrixyl and Argireline each target different aspects of the skin ageing cascade. TIAM put all three in one formula. Results develop over 8–12 weeks of consistent use.",
	),
	// ── Exfoliators ───────────────────────────────────────────────────────
	array(
		'sku'         => 'GLW-EXF-003',
		'name'        => 'One Step Original Clear Pad',
		'brand'       => 'COSRX',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 340,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination',
		'types'       => array( 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture' ),
		'actives'     => 'Betaine salicylate 1.5%, Willow bark water, Allantoin',
		'svg'         => '_default.svg',
		'excerpt'     => 'Toner-soaked cotton pads pre-loaded with BHA — exfoliation without measuring, decanting or second-guessing the amount.',
		'description' => "The same BHA logic as the Power Liquid but lower concentration and in pad form — swipe on after cleansing, done. Ideal for travelling, for routine beginners, or for skin not yet ready for 4% betaine salicylate.",
	),
	array(
		'sku'         => 'GLW-EXF-004',
		'name'        => 'AHA 7 Whitehead Power Liquid',
		'brand'       => 'Some By Mi',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 330,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'yes',
		'skin_types'  => 'Normal, Dry, Combination',
		'types'       => array( 'normal', 'dry', 'combination' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Glycolic acid 7%, Lemon extract, Niacinamide',
		'svg'         => '_default.svg',
		'excerpt'     => '7% glycolic acid for surface cell turnover — the AHA counterpart to the BHA Power Liquid, aimed at dullness and textural unevenness rather than pores.',
		'description' => "Where BHA works inside pores, AHA works on the surface — speeding the cell turnover that slows with age and sun exposure. This liquid sits at a weekly-treatment concentration. Pair it with the BHA on alternating nights for a complete exfoliation rotation.",
	),
	array(
		'sku'         => 'GLW-EXF-005',
		'name'        => 'Bio-Peel Gauze Peeling Wine',
		'brand'       => 'Neogen',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 480,
		'sale'        => 410,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Normal, Combination, Dry',
		'types'       => array( 'normal', 'combination', 'dry' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Tartaric acid, Resveratrol, Real wine 38%',
		'svg'         => '_default.svg',
		'excerpt'     => 'Double-sided gauze pads soaked in 38% fermented wine — physical exfoliation on one side, AHA treatment on the other, and resveratrol throughout.',
		'description' => "This is a more textural experience than a liquid: the rough gauze side lifts debris while tartaric acid (naturally occurring in wine) does the chemical exfoliation below. Resveratrol adds antioxidant work. Popular for dull, uneven skin tone. Use twice a week maximum.",
	),
	array(
		'sku'         => 'GLW-EXF-006',
		'name'        => 'Aloe BHA Skin Toner',
		'brand'       => 'Benton',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 360,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Oily, Combination',
		'types'       => array( 'sensitive', 'oily', 'combination' ),
		'concerns'    => array( 'breakouts-texture', 'sensitive-reactive' ),
		'actives'     => 'Aloe vera 82%, Salicylic acid 0.3%, Snail secretion filtrate',
		'svg'         => '_default.svg',
		'excerpt'     => 'The gentlest daily BHA option we stock: 0.3% salicylic acid in an 82% aloe base that calms while it clears.',
		'description' => "For skin where congestion and sensitivity overlap, Benton's formula keeps the exfoliating dose low enough for every-other-day use. Aloe and snail filtrate mean this calms as much as it clears. The toner you reach for on a skin-stressed week.",
	),
	array(
		'sku'         => 'GLW-EXF-007',
		'name'        => 'Brightening Peeling Gel',
		'brand'       => 'Dr.G',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 295,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'Normal, Combination, Dry',
		'types'       => array( 'normal', 'combination', 'dry' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Papain enzyme, Lactic acid, Vitamin C derivative',
		'svg'         => '_default.svg',
		'excerpt'     => 'A wash-off enzyme gel that physically beets up (not "peels" in the aggressive sense) — dead skin rolls away in satisfying little balls without a trace of irritation.',
		'description' => "Papain enzyme from papaya dissolves dead cell bonds; a low dose of lactic acid brightens below the surface. The texture is the novelty but the results are real. One to two uses a week instead of a dedicated acid night.",
	),
	array(
		'sku'         => 'GLW-EXF-008',
		'name'        => 'Chestnut AHA 8% Toner',
		'brand'       => 'Isntree',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 390,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Normal',
		'types'       => array( 'oily', 'combination', 'normal' ),
		'concerns'    => array( 'dehydrated-dull', 'fine-lines-firmness' ),
		'actives'     => 'Glycolic acid 5%, Mandelic acid 3%, Chestnut extract',
		'svg'         => '_default.svg',
		'excerpt'     => '8% combined AHA — glycolic and mandelic — in a toner that works on both texture and tone with less post-exfoliation dryness than single-acid formulas.',
		'description' => "Mandelic acid is larger molecule than glycolic, so it penetrates more slowly and irritates less — stacking them at different rates means the exfoliation spreads across more skin depths. Chestnut extract adds natural antioxidants. A once-to-twice-weekly step.",
	),
	array(
		'sku'         => 'GLW-EXF-009',
		'name'        => 'Mandelic Acid 5% Skin Prep Water',
		'brand'       => 'By Wishtrend',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 420,
		'sale'        => 360,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Sensitive, Dry, Normal',
		'types'       => array( 'sensitive', 'dry', 'normal' ),
		'concerns'    => array( 'dehydrated-dull', 'sensitive-reactive' ),
		'actives'     => 'Mandelic acid 5%, Lactobacillus ferment, Sodium PCA',
		'svg'         => '_default.svg',
		'excerpt'     => 'The AHA for sensitive skin: mandelic acid at 5% is the gentlest option in the acid class, with fermented lactobacillus for added calm.',
		'description' => "Mandelic acid is derived from bitter almonds and has the largest molecule of any AHA — which makes it the on-ramp for anyone who has tried acids and given up. 5% is a real working concentration. No tingling, no redness, steady texture improvement.",
	),
	array(
		'sku'         => 'GLW-EXF-010',
		'name'        => 'Vita B3 Source',
		'brand'       => 'TIAM',
		'cat'         => 'exfoliators',
		'step'        => 2,
		'price'       => 345,
		'sale'        => 0,
		'featured'    => false,
		'vegan'       => 'yes',
		'cf'          => 'yes',
		'skin_types'  => 'Oily, Combination, Normal',
		'types'       => array( 'oily', 'combination', 'normal' ),
		'concerns'    => array( 'breakouts-texture', 'dehydrated-dull' ),
		'actives'     => 'Niacinamide 10%, PHA, Zinc PCA',
		'svg'         => '_default.svg',
		'excerpt'     => '10% niacinamide with PHA — the AHA family member that hydrates as it exfoliates — for brightening and pore work in a single step.',
		'description' => "PHA (polyhydroxy acid) is the gentlest exfoliant class and doubles as a humectant, making this TIAM toner suitable for skin that wants exfoliation and hydration from the same step. Niacinamide at 10% handles the brightening.",
	),
	array(
		'sku'         => 'GLW-LIP-001',
		'name'        => 'Lip Sleeping Mask — Berry',
		'brand'       => 'Laneige',
		'cat'         => 'lips',
		'step'        => 0,
		'price'       => 385,
		'sale'        => 0,
		'featured'    => true,
		'vegan'       => 'no',
		'cf'          => 'no',
		'skin_types'  => 'All skin types',
		'types'       => array( 'dry', 'normal', 'combination', 'oily', 'sensitive' ),
		'concerns'    => array( 'dehydrated-dull' ),
		'actives'     => 'Berry fruit complex, Vitamin C, Murumuru and shea butter',
		'svg'         => 'laneige-lip-sleeping-mask.svg',
		'excerpt'     => 'The overnight lip mask with a genuine cult — vitamin-C berry complex in a balm that means waking up with soft lips becomes the default state.',
		'description' => "There\'s a reason this little jar shows up in every “empties” video on the internet. The berry complex delivers antioxidants and gentle fruit-acid exfoliation while murumuru and shea butter form an overnight seal — so flakes are gone by morning instead of being chewed off by 10am.\n\nLips have no oil glands and get no SPF; they need more help than they get. This is the help. Also our most-given gift, by a margin.\n\nHow to use: a thin layer on lips as the actual last step of your evening — after the routine, after the water glass, right before the pillow.",
	),
);

/* --------------------------------------------------------------------------
 * 4. Review pool — assigned deterministically so re-runs are stable.
 * ------------------------------------------------------------------------ */

$glow_reviewers = array(
	array( 'Naledi M.', 'naledi.m@example.com' ),
	array( 'Sarah V.', 'sarah.v@example.com' ),
	array( 'Thabo K.', 'thabo.k@example.com' ),
	array( 'Aisha P.', 'aisha.p@example.com' ),
	array( 'Lerato D.', 'lerato.d@example.com' ),
	array( 'Megan W.', 'megan.w@example.com' ),
	array( 'Sipho N.', 'sipho.n@example.com' ),
	array( 'Chloe B.', 'chloe.b@example.com' ),
	array( 'Zanele T.', 'zanele.t@example.com' ),
	array( 'Daniel R.', 'daniel.r@example.com' ),
	array( 'Priya G.', 'priya.g@example.com' ),
	array( 'Johan S.', 'johan.s@example.com' ),
);

$glow_review_texts = array(
	array( 5, 'Third repurchase. My skin has genuinely never been this consistent — and I\'m someone who used to switch products every month looking for a miracle.' ),
	array( 5, 'Took about three weeks to show, exactly like the description said it would. I appreciate a store that doesn\'t promise overnight anything.' ),
	array( 4, 'Really good. Took one star off because I wish the bottle were bigger — I go through it fast because I actually enjoy using it.' ),
	array( 5, 'My dermatologist asked what I\'d changed. That\'s the review.' ),
	array( 4, 'Solid, does what it says. Texture took a few days to get used to but now I don\'t notice it at all.' ),
	array( 5, 'Bought after the team talked me OUT of a more expensive option and into this one. It was the right call, and that honesty is why I keep coming back.' ),
	array( 5, 'Sensitive skin here — patch tested for three days like the FAQ says, no reaction, been using daily for two months. Zero issues, visible improvement.' ),
	array( 4, 'Arrived in Durban in two days, properly packed. Product itself is very good; my partner keeps borrowing it, which is its own kind of five stars.' ),
	array( 5, 'This replaced two other products in my routine. Less stuff, better skin. The routine-order thing on this site actually works.' ),
	array( 5, 'Was sceptical about ordering skincare online after a bad fake from another site. The batch documentation came with the parcel without me even asking.' ),
	array( 4, 'Does exactly what the actives list says it should. No fragrance drama, no purging surprise. Reliable.' ),
	array( 5, 'Gifted it first, then bought my own because I kept stealing hers. Winter in Joburg has met its match.' ),
);

/* --------------------------------------------------------------------------
 * 5. Create / update products
 * ------------------------------------------------------------------------ */

$glow_created = 0;
$glow_updated = 0;

foreach ( $glow_products as $index => $data ) {
	$existing_id = wc_get_product_id_by_sku( $data['sku'] );
	$product     = $existing_id ? wc_get_product( $existing_id ) : new WC_Product_Simple();

	$product->set_name( $data['name'] );
	$product->set_status( 'publish' );
	$product->set_sku( $data['sku'] );
	$product->set_regular_price( (string) $data['price'] );
	$product->set_sale_price( $data['sale'] > 0 ? (string) $data['sale'] : '' );
	$product->set_short_description( $data['excerpt'] );
	$product->set_description( $data['description'] );
	$product->set_featured( $data['featured'] );
	$product->set_manage_stock( true );
	$product->set_stock_quantity( 25 + ( $index * 3 ) % 40 );
	$product->set_stock_status( 'instock' );

	if ( isset( $glow_cat_ids[ $data['cat'] ] ) ) {
		$product->set_category_ids( array( $glow_cat_ids[ $data['cat'] ] ) );
	}

	$product_id = $product->save();

	if ( $existing_id ) {
		$glow_updated++;
	} else {
		$glow_created++;
	}

	// Theme meta — keys must match the theme templates.
	update_post_meta( $product_id, '_product_brand', $data['brand'] );
	update_post_meta( $product_id, '_skin_types', $data['skin_types'] );
	update_post_meta( $product_id, '_key_ingredients', $data['actives'] );
	update_post_meta( $product_id, '_product_routine_step', $data['step'] > 0 ? (string) $data['step'] : '' );
	update_post_meta( $product_id, '_is_vegan', $data['vegan'] );
	update_post_meta( $product_id, '_is_cruelty_free', $data['cf'] );
	update_post_meta( $product_id, '_glow_svg', $data['svg'] );

	// Product thumbnail — reuse an already-imported step image if present,
	// otherwise sideload once per unique filename (avoids repeated HTTP calls).
	if ( ! has_post_thumbnail( $product_id ) && $data['step'] >= 1 && $data['step'] <= 7 ) {
		$img_filename = sprintf( 'product-step-%02d.jpg', (int) $data['step'] );

		// Look for an existing attachment with this filename before sideloading.
		$existing_att = get_posts( array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( array(
				'key'     => '_wp_attached_file',
				'value'   => $img_filename,
				'compare' => 'LIKE',
			) ),
		) );

		if ( $existing_att ) {
			set_post_thumbnail( $product_id, $existing_att[0] );
		} else {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$img_url       = get_template_directory_uri() . '/images/products/' . $img_filename;
			$attachment_id = media_sideload_image( $img_url, $product_id, $data['name'], 'id' );

			if ( ! is_wp_error( $attachment_id ) ) {
				set_post_thumbnail( $product_id, $attachment_id );
			}
		}
	}

	// Custom taxonomies.
	wp_set_object_terms( $product_id, $data['types'], 'skin_type' );
	wp_set_object_terms( $product_id, $data['concerns'], 'skin_concern' );

	// Reviews: 2–3 per product from the pool, deterministic, no duplicates on re-run.
	$existing_reviews = get_comments(
		array(
			'post_id' => $product_id,
			'type'    => 'review',
			'count'   => true,
		)
	);

	if ( ! $existing_reviews ) {
		$review_count = 2 + ( $index % 2 );

		for ( $r = 0; $r < $review_count; $r++ ) {
			$reviewer = $glow_reviewers[ ( $index * 2 + $r * 5 ) % count( $glow_reviewers ) ];
			$review   = $glow_review_texts[ ( $index * 3 + $r * 7 ) % count( $glow_review_texts ) ];

			$comment_id = wp_insert_comment(
				array(
					'comment_post_ID'      => $product_id,
					'comment_author'       => $reviewer[0],
					'comment_author_email' => $reviewer[1],
					'comment_content'      => $review[1],
					'comment_type'         => 'review',
					'comment_approved'     => 1,
					'comment_date'         => gmdate( 'Y-m-d H:i:s', time() - ( ( $index + $r + 3 ) * 5 * DAY_IN_SECONDS ) ),
				)
			);

			if ( $comment_id ) {
				update_comment_meta( $comment_id, 'rating', $review[0] );
				update_comment_meta( $comment_id, 'verified', 1 );
			}
		}
	}

	// Recompute cached rating fields from the actual reviews.
	if ( class_exists( 'WC_Comments' ) ) {
		WC_Comments::clear_transients( $product_id );
	}

	$fresh = wc_get_product( $product_id );
	if ( $fresh ) {
		$counts = array();
		$reviews = get_comments(
			array(
				'post_id' => $product_id,
				'type'    => 'review',
				'status'  => 'approve',
			)
		);

		$sum = 0;
		$num = 0;
		foreach ( $reviews as $review_comment ) {
			$rating = (int) get_comment_meta( $review_comment->comment_ID, 'rating', true );
			if ( $rating > 0 ) {
				$counts[ $rating ] = isset( $counts[ $rating ] ) ? $counts[ $rating ] + 1 : 1;
				$sum += $rating;
				$num++;
			}
		}

		$fresh->set_rating_counts( $counts );
		$fresh->set_average_rating( $num ? round( $sum / $num, 2 ) : 0 );
		$fresh->set_review_count( $num );
		$fresh->save();
	}

	echo ( $existing_id ? 'Updated: ' : 'Created: ' ) . $data['name'] . "\n";
	unset( $product, $fresh, $reviews );
}

// Best-seller plausibility: give featured products a sales head start.
foreach ( $glow_products as $index => $data ) {
	$pid = wc_get_product_id_by_sku( $data['sku'] );
	if ( $pid ) {
		update_post_meta( $pid, 'total_sales', $data['featured'] ? 120 + $index * 7 : 12 + $index * 3 );
	}
}

// Schedule lookup table rebuild as a background task rather than blocking inline.
if ( function_exists( 'wc_update_product_lookup_tables' ) ) {
	wp_schedule_single_event( time() + 5, 'wc_update_product_lookup_tables' );
}

/* --------------------------------------------------------------------------
 * 6. Global WooCommerce attributes for Helix sync
 *
 * Registers pa_skin-types, pa_concerns-targeted, pa_key-ingredients and
 * pa_step as WooCommerce global attributes with pre-populated terms, then
 * assigns realistic values to every demo product using WC_Product_Attribute
 * objects so WooCommerce recognises them as proper taxonomy attributes.
 *
 * Idempotent: skips attributes / terms / products that already have values.
 * ------------------------------------------------------------------------ */

$glow_helix_attrs = array(
	'skin-types' => array(
		'label' => 'Skin Types',
		'terms' => array(
			'oily'        => 'Oily',
			'dry'         => 'Dry',
			'combination' => 'Combination',
			'sensitive'   => 'Sensitive',
			'normal'      => 'Normal',
			'all'         => 'All',
		),
	),
	'concerns-targeted' => array(
		'label' => 'Concerns Targeted',
		'terms' => array(
			'acne'        => 'Acne',
			'aging'       => 'Aging',
			'brightening' => 'Brightening',
			'hydration'   => 'Hydration',
			'pores'       => 'Pores',
			'redness'     => 'Redness',
			'texture'     => 'Texture',
		),
	),
	'key-ingredients' => array(
		'label' => 'Key Ingredients',
		'terms' => array(
			'niacinamide'       => 'Niacinamide',
			'hyaluronic-acid'   => 'Hyaluronic Acid',
			'retinol'           => 'Retinol',
			'vitamin-c'         => 'Vitamin C',
			'centella-asiatica' => 'Centella Asiatica',
			'snail-mucin'       => 'Snail Mucin',
			'ceramide'          => 'Ceramide',
			'aha'               => 'AHA',
			'bha'               => 'BHA',
			'peptides'          => 'Peptides',
		),
	),
	'step' => array(
		'label' => 'Routine Step',
		'terms' => array(
			'cleanse'    => 'Cleanse',
			'tone'       => 'Tone',
			'treat'      => 'Treat',
			'moisturize' => 'Moisturize',
			'protect'    => 'Protect',
			'mask'       => 'Mask',
		),
	),
);

// ── Step A: register attributes and pre-populate terms ────────────────────

$glow_helix_attr_ids = array();

foreach ( $glow_helix_attrs as $attr_name => $attr_cfg ) {
	$taxonomy = 'pa_' . $attr_name;

	// Find existing attribute ID.
	$attr_id    = 0;
	$registered = wc_get_attribute_taxonomies();
	foreach ( $registered as $tax ) {
		if ( $tax->attribute_name === $attr_name ) {
			$attr_id = (int) $tax->attribute_id;
			break;
		}
	}

	if ( ! $attr_id ) {
		$result = wc_create_attribute( array(
			'name'         => $attr_cfg['label'],
			'slug'         => $attr_name,
			'type'         => 'select',
			'order_by'     => 'menu_order',
			'has_archives' => false,
		) );
		$attr_id = is_wp_error( $result ) ? 0 : (int) $result;
		echo "Created attribute: {$taxonomy}\n";
	}

	$glow_helix_attr_ids[ $attr_name ] = $attr_id;

	// Register the taxonomy in the current request so term functions work.
	if ( ! taxonomy_exists( $taxonomy ) ) {
		register_taxonomy( $taxonomy, array( 'product' ), array(
			'hierarchical' => false,
			'label'        => $attr_cfg['label'],
			'show_ui'      => false,
			'query_var'    => true,
			'rewrite'      => array( 'slug' => $taxonomy ),
		) );
	}

	// Insert any missing terms.
	foreach ( $attr_cfg['terms'] as $slug => $label ) {
		if ( ! term_exists( $slug, $taxonomy ) ) {
			wp_insert_term( $label, $taxonomy, array( 'slug' => $slug ) );
		}
	}
}

// ── Step B: helper to build a WC_Product_Attribute and link terms ─────────

/**
 * Builds a WC_Product_Attribute for a global taxonomy attribute and links
 * terms to the product post. Returns the attribute object or null if no
 * matching terms were found.
 */
if ( ! function_exists( 'glow_build_wc_attr' ) ) {
	function glow_build_wc_attr( $product_id, $taxonomy, $attr_id, $term_slugs, $position ) {
		$term_ids = array();
		foreach ( $term_slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, $taxonomy );
			if ( $term && ! is_wp_error( $term ) ) {
				$term_ids[] = $term->term_id;
			}
		}
		if ( empty( $term_ids ) ) {
			return null;
		}

		wp_set_object_terms( $product_id, $term_ids, $taxonomy );

		$wc_attr = new WC_Product_Attribute();
		$wc_attr->set_id( $attr_id );
		$wc_attr->set_name( $taxonomy );
		$wc_attr->set_options( $term_ids );
		$wc_attr->set_position( $position );
		$wc_attr->set_visible( true );
		$wc_attr->set_variation( false );

		return $wc_attr;
	}
}

// ── Step C: derive attribute term slugs from our existing product data ────

// Concern taxonomy slugs → Helix concern terms.
$glow_concern_map = array(
	'dehydrated-dull'     => array( 'hydration', 'brightening' ),
	'breakouts-texture'   => array( 'acne', 'pores', 'texture' ),
	'fine-lines-firmness' => array( 'aging' ),
	'sensitive-reactive'  => array( 'redness' ),
);

// Routine step number → Helix step term.
$glow_step_map = array(
	1 => 'cleanse',
	2 => 'treat',   // exfoliators
	3 => 'tone',
	4 => 'treat',   // serums / ampoules
	5 => 'moisturize',
	6 => 'treat',   // eye care
	7 => 'protect',
	0 => 'mask',    // sheet masks and lips
);

// Keyword patterns → Helix ingredient term slug.
$glow_ingredient_patterns = array(
	'niacinamide'       => '/niacinamide/i',
	'hyaluronic-acid'   => '/hyaluronic|sodium hyaluronate/i',
	'retinol'           => '/retinol|retinyl|retinoid|retinal/i',
	'vitamin-c'         => '/vitamin c|ascorbic|ascorbyl/i',
	'centella-asiatica' => '/centella|madecassoside|asiaticoside/i',
	'snail-mucin'       => '/snail/i',
	'ceramide'          => '/ceramide/i',
	'aha'               => '/\baha\b|glycolic|lactic|mandelic|tartaric|malic/i',
	'bha'               => '/\bbha\b|salicylic|betaine salicylate/i',
	'peptides'          => '/peptide/i',
);

// ── Step D: assign attributes to each demo product ────────────────────────

$glow_helix_assigned = 0;
$glow_helix_skipped  = 0;

foreach ( $glow_products as $data ) {
	$product_id = wc_get_product_id_by_sku( $data['sku'] );
	if ( ! $product_id ) {
		continue;
	}

	$product        = wc_get_product( $product_id );
	$existing_attrs = $product->get_attributes();

	// Idempotent check: skip if both required Helix fields are already set.
	if ( isset( $existing_attrs['pa_skin-types'] ) && isset( $existing_attrs['pa_concerns-targeted'] ) ) {
		$glow_helix_skipped++;
		unset( $product, $existing_attrs );
		continue;
	}

	// Skin types — map directly from our $data['types'] array; use 'all'
	// when a product targets every skin type (5 values in our set).
	$all_skin_types   = array( 'dry', 'oily', 'combination', 'sensitive', 'normal' );
	$product_types    = $data['types'];
	$missing          = array_diff( $all_skin_types, $product_types );
	$skin_type_slugs  = empty( $missing ) ? array( 'all' ) : $product_types;

	// Concerns.
	$concern_slugs = array();
	foreach ( $data['concerns'] as $c ) {
		if ( isset( $glow_concern_map[ $c ] ) ) {
			foreach ( $glow_concern_map[ $c ] as $mapped ) {
				$concern_slugs[] = $mapped;
			}
		}
	}
	$concern_slugs = array_values( array_unique( $concern_slugs ) );
	if ( empty( $concern_slugs ) ) {
		$concern_slugs = array( 'hydration' ); // safe fallback
	}

	// Routine step.
	$step_slug = isset( $glow_step_map[ $data['step'] ] ) ? $glow_step_map[ $data['step'] ] : 'treat';

	// Key ingredients — keyword-match against the actives string.
	$actives_str     = $data['actives'];
	$ingredient_slugs = array();
	foreach ( $glow_ingredient_patterns as $ing_slug => $pattern ) {
		if ( preg_match( $pattern, $actives_str ) ) {
			$ingredient_slugs[] = $ing_slug;
		}
	}
	if ( empty( $ingredient_slugs ) ) {
		$ingredient_slugs = array( 'niacinamide' ); // safe fallback
	}

	// Build and attach all four attributes.
	$new_attrs = $existing_attrs;

	$a = glow_build_wc_attr( $product_id, 'pa_skin-types',        $glow_helix_attr_ids['skin-types'],        $skin_type_slugs,  0 );
	$b = glow_build_wc_attr( $product_id, 'pa_concerns-targeted', $glow_helix_attr_ids['concerns-targeted'], $concern_slugs,    1 );
	$c = glow_build_wc_attr( $product_id, 'pa_key-ingredients',   $glow_helix_attr_ids['key-ingredients'],   $ingredient_slugs, 2 );
	$d = glow_build_wc_attr( $product_id, 'pa_step',              $glow_helix_attr_ids['step'],              array( $step_slug ), 3 );

	if ( $a ) { $new_attrs['pa_skin-types']        = $a; }
	if ( $b ) { $new_attrs['pa_concerns-targeted'] = $b; }
	if ( $c ) { $new_attrs['pa_key-ingredients']   = $c; }
	if ( $d ) { $new_attrs['pa_step']              = $d; }

	$product->set_attributes( $new_attrs );
	$product->save();
	unset( $product, $new_attrs, $a, $b, $c, $d );

	$glow_helix_assigned++;
}

echo "\nHelix attributes: {$glow_helix_assigned} products assigned, {$glow_helix_skipped} already had values.\n";

echo "\nDone. {$glow_created} products created, {$glow_updated} updated, across " . count( $glow_categories ) . " categories.\n";
echo "Categories, skin types, skin concerns and product reviews are in place.\n";
echo "Product cards use bundled theme SVGs automatically until you attach media.\n";
