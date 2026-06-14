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

	// Product thumbnail — sideload the matching step image if not already set.
	if ( ! has_post_thumbnail( $product_id ) && $data['step'] >= 1 && $data['step'] <= 7 ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$img_filename = sprintf( 'product-step-%02d.jpg', (int) $data['step'] );
		$img_url      = get_template_directory_uri() . '/images/products/' . $img_filename;
		$attachment_id = media_sideload_image( $img_url, $product_id, $data['name'], 'id' );

		if ( ! is_wp_error( $attachment_id ) ) {
			set_post_thumbnail( $product_id, $attachment_id );
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
}

// Best-seller plausibility: give featured products a sales head start.
foreach ( $glow_products as $index => $data ) {
	$pid = wc_get_product_id_by_sku( $data['sku'] );
	if ( $pid ) {
		update_post_meta( $pid, 'total_sales', $data['featured'] ? 120 + $index * 7 : 12 + $index * 3 );
	}
}

if ( function_exists( 'wc_update_product_lookup_tables' ) ) {
	wc_update_product_lookup_tables();
}

echo "\nDone. {$glow_created} products created, {$glow_updated} updated, across " . count( $glow_categories ) . " categories.\n";
echo "Categories, skin types, skin concerns and product reviews are in place.\n";
echo "Product cards use bundled theme SVGs automatically until you attach media.\n";
