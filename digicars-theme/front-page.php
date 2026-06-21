<?php
/**
 * Front page — the Digicars signature homepage.
 *
 * The section order IS the customer journey: a Concierge-led hero, then ways
 * to browse (body type, budget, latest stock), the digital-first trust story,
 * brands, a finance teaser, social proof, the Car Torque blog and a closing
 * call to action. Enquiry + finance funnel only — there is no cart anywhere.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

get_header();

$digicars_shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );
$digicars_shop = $digicars_shop ? $digicars_shop : home_url( '/shop' );
$digicars_blog = home_url( '/car-torque' );
$digicars_fin  = home_url( '/finance' );
$digicars_hero = get_theme_file_uri( 'images/hero/hero-showroom.svg' );
?>

<main id="main" class="site-main">

	<?php /* 1 ---------------------------------------------------- Concierge hero */ ?>
	<?php /* Full-viewport centered design — the Concierge IS the hero. */ ?>
	<section class="home-hero surface-carbon">

		<?php /* Animated background: neural canvas (base) + dot grid + dual glow */ ?>
		<div class="home-hero__bg" aria-hidden="true">
			<canvas class="home-hero__canvas" id="hero-neural"></canvas>
			<div class="home-hero__dots"></div>
			<div class="home-hero__glow"></div>
		</div>

		<div class="container home-hero__inner">

			<?php /* Headline */ ?>
			<div class="home-hero__headline">
				<p class="eyebrow eyebrow--volt home-hero__eyebrow"><?php esc_html_e( 'Digital-first automotive showroom', 'digicars' ); ?></p>
				<h1 class="t-hero home-hero__title">
					<?php esc_html_e( 'Find the car', 'digicars' ); ?>
					<br class="home-hero__break">
					<?php esc_html_e( 'that fits your life.', 'digicars' ); ?>
				</h1>
				<p class="t-lead home-hero__lead"><?php esc_html_e( 'Browse verified vehicles from South Africa\'s best dealers, get a real monthly figure, and let the Concierge shortlist cars that actually match how you drive.', 'digicars' ); ?></p>
			</div>

			<?php echo do_shortcode( '[helix_search]' ); ?>

			<?php /* Secondary CTAs */ ?>
			<div class="cluster home-hero__cta">
				<a class="btn btn--outline btn--lg" href="<?php echo esc_url( $digicars_shop ); ?>"><?php esc_html_e( 'Browse all stock', 'digicars' ); ?></a>
				<span class="home-hero__or t-mono" aria-hidden="true"><?php esc_html_e( 'or', 'digicars' ); ?></span>
				<a class="link-arrow" href="<?php echo esc_url( $digicars_fin ); ?>"><?php esc_html_e( 'Check affordability', 'digicars' ); ?></a>
			</div>

		</div>

		<?php /* Scroll indicator */ ?>
		<div class="home-hero__scroll-indicator" aria-hidden="true">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<line x1="12" y1="5" x2="12" y2="19"/>
				<polyline points="19 12 12 19 5 12"/>
			</svg>
		</div>

	</section>

	<?php /* 1b --------------------------------------- Traditional search filter */ ?>
	<section class="section home-search surface-soft" data-reveal>
		<div class="container">
			<div class="home-search__inner">
				<div class="home-search__head">
					<div class="stack-sm">
						<p class="eyebrow"><?php esc_html_e( 'Find your car', 'digicars' ); ?></p>
						<h2 class="t-3" style="margin:0;"><?php esc_html_e( 'Search our stock', 'digicars' ); ?></h2>
					</div>
					<div class="home-search__conditions">
						<?php
						$digicars_conditions = array(
							''     => __( 'All', 'digicars' ),
							'new'  => __( 'New', 'digicars' ),
							'used' => __( 'Used', 'digicars' ),
							'demo' => __( 'Demo', 'digicars' ),
						);
						foreach ( $digicars_conditions as $digicars_cond_val => $digicars_cond_label ) :
							$digicars_cond_href = '' !== $digicars_cond_val
								? add_query_arg( 'condition', $digicars_cond_val, $digicars_shop )
								: $digicars_shop;
							?>
							<a class="home-search__condition-btn" href="<?php echo esc_url( $digicars_cond_href ); ?>"><?php echo esc_html( $digicars_cond_label ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>

				<form class="home-search__form" method="get" action="<?php echo esc_url( $digicars_shop ); ?>">
					<div class="home-search__fields">
						<div class="field">
							<label class="label" for="hs-make"><?php esc_html_e( 'Make', 'digicars' ); ?></label>
							<select class="select" id="hs-make" name="make">
								<option value=""><?php esc_html_e( 'Any make', 'digicars' ); ?></option>
								<?php
								if ( function_exists( 'digicars_makes' ) ) :
									foreach ( digicars_makes() as $digicars_make_slug => $digicars_make_label ) :
										?>
										<option value="<?php echo esc_attr( $digicars_make_slug ); ?>"><?php echo esc_html( $digicars_make_label ); ?></option>
										<?php
									endforeach;
								endif;
								?>
							</select>
						</div>

						<div class="field">
							<label class="label" for="hs-body"><?php esc_html_e( 'Body type', 'digicars' ); ?></label>
							<select class="select" id="hs-body" name="body">
								<option value=""><?php esc_html_e( 'Any body', 'digicars' ); ?></option>
								<?php
								if ( function_exists( 'digicars_body_types' ) ) :
									foreach ( digicars_body_types() as $digicars_bt_slug => $digicars_bt_info ) :
										?>
										<option value="<?php echo esc_attr( $digicars_bt_slug ); ?>"><?php echo esc_html( $digicars_bt_info['label'] ); ?></option>
										<?php
									endforeach;
								endif;
								?>
							</select>
						</div>

						<div class="field">
							<label class="label" for="hs-price"><?php esc_html_e( 'Max price', 'digicars' ); ?></label>
							<select class="select" id="hs-price" name="price_max">
								<option value=""><?php esc_html_e( 'Any price', 'digicars' ); ?></option>
								<option value="150000"><?php esc_html_e( 'Under R 150 000', 'digicars' ); ?></option>
								<option value="200000"><?php esc_html_e( 'Under R 200 000', 'digicars' ); ?></option>
								<option value="300000"><?php esc_html_e( 'Under R 300 000', 'digicars' ); ?></option>
								<option value="500000"><?php esc_html_e( 'Under R 500 000', 'digicars' ); ?></option>
								<option value="800000"><?php esc_html_e( 'Under R 800 000', 'digicars' ); ?></option>
								<option value="1200000"><?php esc_html_e( 'Under R 1.2M', 'digicars' ); ?></option>
							</select>
						</div>

						<div class="field">
							<label class="label" for="hs-pm"><?php esc_html_e( 'Monthly budget', 'digicars' ); ?></label>
							<select class="select" id="hs-pm" name="pm_max">
								<option value=""><?php esc_html_e( 'Any monthly', 'digicars' ); ?></option>
								<option value="3000"><?php esc_html_e( 'Under R 3 000 pm', 'digicars' ); ?></option>
								<option value="4000"><?php esc_html_e( 'Under R 4 000 pm', 'digicars' ); ?></option>
								<option value="6000"><?php esc_html_e( 'Under R 6 000 pm', 'digicars' ); ?></option>
								<option value="8000"><?php esc_html_e( 'Under R 8 000 pm', 'digicars' ); ?></option>
								<option value="12000"><?php esc_html_e( 'Under R 12 000 pm', 'digicars' ); ?></option>
							</select>
						</div>

						<button type="submit" class="btn btn--signal home-search__submit"><?php esc_html_e( 'Search', 'digicars' ); ?></button>
					</div>
				</form>
			</div>
		</div>
	</section>

	<?php /* 2 -------------------------------------------------- Browse by body type */ ?>
	<section class="section section--tight surface-soft" data-reveal>
		<div class="container">
			<div class="section-head">
				<div class="section-head__copy stack-sm">
					<p class="eyebrow"><?php esc_html_e( 'Browse by body type', 'digicars' ); ?></p>
					<h2 class="t-2"><?php esc_html_e( 'Start with the shape of your life.', 'digicars' ); ?></h2>
				</div>
				<a class="link-arrow" href="<?php echo esc_url( $digicars_shop ); ?>"><?php esc_html_e( 'All vehicles', 'digicars' ); ?></a>
			</div>

			<ul class="bodytype-grid grid" style="--cols:5;">
				<?php
				if ( function_exists( 'digicars_body_types' ) ) :
					foreach ( digicars_body_types() as $digicars_slug => $digicars_body ) :
						$digicars_url = add_query_arg( 'body', $digicars_slug, $digicars_shop );
						?>
						<li class="bodytype-grid__item">
							<a class="bodytype-tile" href="<?php echo esc_url( $digicars_url ); ?>">
								<img class="bodytype-tile__icon" src="<?php echo esc_url( $digicars_body['icon'] ); ?>" alt="" aria-hidden="true" width="48" height="32" loading="lazy">
								<span class="bodytype-tile__label"><?php echo esc_html( $digicars_body['label'] ); ?></span>
							</a>
						</li>
						<?php
					endforeach;
				endif;
				?>
			</ul>
		</div>
	</section>

	<?php
	/* 3 --------------------------------------------- Browse by budget / monthly */
	$digicars_budgets = array(
		array(
			'eyebrow' => __( 'Cash price', 'digicars' ),
			'label'   => __( 'Under R200 000', 'digicars' ),
			'note'    => __( 'Smart starter buys', 'digicars' ),
			'args'    => array(
				'search_by' => 'price',
				'price_max' => 200000,
			),
		),
		array(
			'eyebrow' => __( 'Cash price', 'digicars' ),
			'label'   => __( 'Under R300 000', 'digicars' ),
			'note'    => __( 'Family-ready value', 'digicars' ),
			'args'    => array(
				'search_by' => 'price',
				'price_max' => 300000,
			),
		),
		array(
			'eyebrow' => __( 'Per month', 'digicars' ),
			'label'   => __( 'Under R4 000 pm', 'digicars' ),
			'note'    => __( 'Easy on the budget', 'digicars' ),
			'args'    => array(
				'search_by' => 'monthly',
				'pm_max'    => 4000,
			),
		),
		array(
			'eyebrow' => __( 'Per month', 'digicars' ),
			'label'   => __( 'Under R6 000 pm', 'digicars' ),
			'note'    => __( 'Room to move up', 'digicars' ),
			'args'    => array(
				'search_by' => 'monthly',
				'pm_max'    => 6000,
			),
		),
	);
	?>
	<section class="section section--tight" data-reveal>
		<div class="container">
			<div class="section-head">
				<div class="section-head__copy stack-sm">
					<p class="eyebrow"><?php esc_html_e( 'Browse by budget', 'digicars' ); ?></p>
					<h2 class="t-2"><?php esc_html_e( 'Know your number? Start there.', 'digicars' ); ?></h2>
				</div>
				<a class="link-arrow" href="<?php echo esc_url( $digicars_fin ); ?>"><?php esc_html_e( 'Work out affordability', 'digicars' ); ?></a>
			</div>

			<div class="grid home-budgets" style="--cols:4;">
				<?php foreach ( $digicars_budgets as $digicars_budget ) : ?>
					<a class="home-budget" href="<?php echo esc_url( add_query_arg( $digicars_budget['args'], $digicars_shop ) ); ?>">
						<span class="eyebrow"><?php echo esc_html( $digicars_budget['eyebrow'] ); ?></span>
						<span class="home-budget__label t-2"><?php echo esc_html( $digicars_budget['label'] ); ?></span>
						<span class="muted"><?php echo esc_html( $digicars_budget['note'] ); ?></span>
						<span class="link-arrow home-budget__go"><?php esc_html_e( 'See cars', 'digicars' ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
	/* 4 ------------------------------------------- Latest arrivals + featured */
	$digicars_vehicles = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$digicars_featured = wc_get_products(
			array(
				'status'   => 'publish',
				'featured' => true,
				'limit'    => 8,
				'orderby'  => 'date',
				'order'    => 'DESC',
			)
		);

		$digicars_latest = wc_get_products(
			array(
				'status'  => 'publish',
				'limit'   => 8,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		// Featured first, then fill with latest; de-duplicate by ID, cap at 8.
		$digicars_seen = array();
		foreach ( array_merge( (array) $digicars_featured, (array) $digicars_latest ) as $digicars_v ) {
			if ( ! $digicars_v instanceof WC_Product ) {
				continue;
			}
			$digicars_vid = $digicars_v->get_id();
			if ( isset( $digicars_seen[ $digicars_vid ] ) ) {
				continue;
			}
			$digicars_seen[ $digicars_vid ] = true;
			$digicars_vehicles[]            = $digicars_v;
			if ( count( $digicars_vehicles ) >= 8 ) {
				break;
			}
		}
	}

	if ( ! empty( $digicars_vehicles ) ) :
		?>
		<section class="section surface-soft" data-reveal>
			<div class="container">
				<div class="section-head">
					<div class="section-head__copy stack-sm">
						<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Latest arrivals', 'digicars' ); ?></p>
						<h2 class="t-1"><?php esc_html_e( 'Fresh stock, just landed.', 'digicars' ); ?></h2>
					</div>
					<a class="btn btn--outline" href="<?php echo esc_url( $digicars_shop ); ?>"><?php esc_html_e( 'Browse all stock', 'digicars' ); ?></a>
				</div>

				<ul class="grid grid--products products" style="--cols:4;">
					<?php
					$digicars_card = get_theme_file_path( 'woocommerce/content-product.php' );
					foreach ( $digicars_vehicles as $digicars_loop_product ) :
						$GLOBALS['product'] = $digicars_loop_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						$product            = $digicars_loop_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						if ( function_exists( 'wc_get_template_part' ) ) {
							wc_get_template_part( 'content', 'product' );
						} elseif ( is_file( $digicars_card ) ) {
							require $digicars_card;
						}
					endforeach;
					?>
				</ul>
			</div>
		</section>
		<?php
	endif;
	?>

	<?php /* 5 ----------------------------------------------- Digital-first trust */ ?>
	<section class="section surface-carbon home-trust" data-reveal>
		<div class="container">
			<div class="home-trust__grid">
				<div class="home-trust__lead stack">
					<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Phygital by design', 'digicars' ); ?></p>
					<h2 class="t-1"><?php esc_html_e( 'Fueled by passion. Driven by technology.', 'digicars' ); ?></h2>
					<p class="t-lead"><?php esc_html_e( 'We pair the warmth of a proper South African dealership with the speed of doing it all from your phone — from first shortlist to signed finance.', 'digicars' ); ?></p>
				</div>

				<ul class="home-trust__points">
					<li class="home-trust__point stack-sm">
						<span class="t-mono home-trust__num">01</span>
						<h3 class="t-3"><?php esc_html_e( 'Every vehicle verified', 'digicars' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'Each car is inspected and its history checked before it goes live — mileage, ownership and service record, no surprises on collection day.', 'digicars' ); ?></p>
					</li>
					<li class="home-trust__point stack-sm">
						<span class="t-mono home-trust__num">02</span>
						<h3 class="t-3"><?php esc_html_e( 'Finance from the big banks', 'digicars' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'One application, multiple offers from WesBank, Absa, Standard Bank and Nedbank — compare real rates instead of guessing.', 'digicars' ); ?></p>
					</li>
					<li class="home-trust__point stack-sm">
						<span class="t-mono home-trust__num">03</span>
						<h3 class="t-3"><?php esc_html_e( 'Buy online, collect in person', 'digicars' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'Reserve from the couch, sign digitally, then collect at a dealer near you — or have it delivered to your door.', 'digicars' ); ?></p>
					</li>
					<li class="home-trust__point stack-sm">
						<span class="t-mono home-trust__num">04</span>
						<h3 class="t-3"><?php esc_html_e( 'One marketplace, every brand', 'digicars' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'From Toyota and VW to Chery, Omoda and GWM — compare across makes in one place instead of trawling fifteen websites.', 'digicars' ); ?></p>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<?php /* 6 ------------------------------------------------------ Brands strip */ ?>
	<section class="section section--tight" data-reveal>
		<div class="container stack">
			<p class="eyebrow"><?php esc_html_e( 'Shop by brand', 'digicars' ); ?></p>
			<ul class="home-brands cluster">
				<?php
				if ( function_exists( 'digicars_makes' ) ) :
					foreach ( digicars_makes() as $digicars_make_slug => $digicars_make_label ) :
						$digicars_make_url = add_query_arg( 'make', $digicars_make_slug, $digicars_shop );
						?>
						<li class="home-brands__item">
							<a class="home-brand" href="<?php echo esc_url( $digicars_make_url ); ?>"><?php echo esc_html( $digicars_make_label ); ?></a>
						</li>
						<?php
					endforeach;
				endif;
				?>
			</ul>
		</div>
	</section>

	<?php /* 7 -------------------------------------------- Finance / affordability */ ?>
	<section class="section section--tight surface-soft" data-reveal>
		<div class="container">
			<div class="home-finance">
				<div class="home-finance__copy stack">
					<p class="eyebrow eyebrow--volt"><?php esc_html_e( 'Finance first', 'digicars' ); ?></p>
					<h2 class="t-1"><?php esc_html_e( 'Know what you can afford first.', 'digicars' ); ?></h2>
					<p class="t-lead"><?php esc_html_e( 'Set your deposit, term and monthly comfort zone, and we\'ll show you only the cars that fit — so you shop with a real budget, not a wishlist.', 'digicars' ); ?></p>
					<div class="cluster">
						<a class="btn btn--signal btn--lg" href="<?php echo esc_url( $digicars_fin ); ?>"><?php esc_html_e( 'Check affordability', 'digicars' ); ?></a>
						<a class="link-arrow" href="<?php echo esc_url( add_query_arg( array( 'search_by' => 'monthly' ), $digicars_shop ) ); ?>"><?php esc_html_e( 'Shop by monthly', 'digicars' ); ?></a>
					</div>
				</div>
				<ul class="home-finance__facts stack">
					<li class="t-mono"><span class="home-finance__fact-k"><?php esc_html_e( 'Pre-approval', 'digicars' ); ?></span><?php esc_html_e( 'In minutes, online', 'digicars' ); ?></li>
					<li class="t-mono"><span class="home-finance__fact-k"><?php esc_html_e( 'Lenders', 'digicars' ); ?></span><?php esc_html_e( '4 major SA banks', 'digicars' ); ?></li>
					<li class="t-mono"><span class="home-finance__fact-k"><?php esc_html_e( 'Terms', 'digicars' ); ?></span><?php esc_html_e( '12–72 months', 'digicars' ); ?></li>
				</ul>
			</div>
		</div>
	</section>

	<?php /* 8 --------------------------------------------------------- Reviews */ ?>
	<section class="section" data-reveal>
		<div class="container">
			<div class="section-head">
				<div class="section-head__copy stack-sm">
					<p class="eyebrow"><?php esc_html_e( 'From our customers', 'digicars' ); ?></p>
					<h2 class="t-1"><?php esc_html_e( 'Real people. Real keys collected.', 'digicars' ); ?></h2>
				</div>
				<p class="t-mono muted home-reviews__score"><?php esc_html_e( '4.8 / 5 · 2 300+ reviews', 'digicars' ); ?></p>
			</div>

			<div class="home-reviews-layout">

				<?php /* Featured large review */ ?>
				<blockquote class="home-review home-review--hero">
					<p class="home-review__stars t-mono" aria-label="<?php esc_attr_e( '5 out of 5', 'digicars' ); ?>" aria-hidden="false">★★★★★</p>
					<div class="home-review__text">
						<p>"<?php esc_html_e( 'I found my Polo on a Tuesday, got finance sorted on my phone, and collected it in Sandton that Saturday. I never set foot in a dealership until I picked up the keys.', 'digicars' ); ?>"</p>
					</div>
					<footer class="home-review__who">
						<strong><?php esc_html_e( 'Thandeka M.', 'digicars' ); ?></strong>
						<span class="muted"> · <?php esc_html_e( 'Sandton', 'digicars' ); ?></span>
					</footer>
				</blockquote>

				<?php /* Two stacked smaller reviews */ ?>
				<div class="home-reviews-stack">
					<blockquote class="home-review">
						<p class="home-review__stars t-mono" aria-label="<?php esc_attr_e( '5 out of 5', 'digicars' ); ?>" aria-hidden="false">★★★★★</p>
						<div class="home-review__text">
							<p>"<?php esc_html_e( 'Comparing the bakkies across brands in one place saved me weeks. The monthly figure on the listing was exactly what the bank came back with.', 'digicars' ); ?>"</p>
						</div>
						<footer class="home-review__who">
							<strong><?php esc_html_e( 'Riaan van der Merwe', 'digicars' ); ?></strong>
							<span class="muted"> · <?php esc_html_e( 'Cape Town', 'digicars' ); ?></span>
						</footer>
					</blockquote>

					<blockquote class="home-review">
						<p class="home-review__stars t-mono" aria-label="<?php esc_attr_e( '5 out of 5', 'digicars' ); ?>" aria-hidden="false">★★★★★</p>
						<div class="home-review__text">
							<p>"<?php esc_html_e( 'As a first-time buyer I was nervous, but the Concierge shortlisted three cars in my budget and a consultant phoned me the next morning. Brilliant.', 'digicars' ); ?>"</p>
						</div>
						<footer class="home-review__who">
							<strong><?php esc_html_e( 'Lerato K.', 'digicars' ); ?></strong>
							<span class="muted"> · <?php esc_html_e( 'Pretoria', 'digicars' ); ?></span>
						</footer>
					</blockquote>
				</div>

			</div>
		</div>
	</section>

	<?php
	/* 9 --------------------------------------------------- Car Torque (dynamic) */
	$digicars_posts = null;
	if ( class_exists( 'WP_Query' ) ) {
		$digicars_posts = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
			)
		);
	}

	if ( $digicars_posts && $digicars_posts->have_posts() ) :
		?>
		<section class="section surface-soft" data-reveal>
			<div class="container">
				<div class="section-head">
					<div class="section-head__copy stack-sm">
						<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Car Torque', 'digicars' ); ?></p>
						<h2 class="t-1"><?php esc_html_e( 'Buying advice, road tests & EV talk.', 'digicars' ); ?></h2>
					</div>
					<a class="btn btn--outline" href="<?php echo esc_url( $digicars_blog ); ?>"><?php esc_html_e( 'Read Car Torque', 'digicars' ); ?></a>
				</div>

				<ul class="post-list grid" style="--cols:3;">
					<?php
					while ( $digicars_posts->have_posts() ) :
						$digicars_posts->the_post();
						if ( function_exists( 'digicars_post_card' ) ) {
							digicars_post_card( get_post() );
						}
					endwhile;
					?>
				</ul>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endif;
	?>

	<?php /* 10 ----------------------------------------------------- Closing CTA */ ?>
	<section class="section surface-carbon home-closing" data-reveal>
		<div class="container stack">
			<p class="eyebrow eyebrow--volt"><?php esc_html_e( 'Ready when you are', 'digicars' ); ?></p>
			<h2 class="t-hero"><?php esc_html_e( 'Let\'s find the one.', 'digicars' ); ?></h2>
			<p class="t-lead"><?php esc_html_e( 'Tell the Concierge how you drive, or dive straight into the stock. Either way, you\'re minutes from your next car.', 'digicars' ); ?></p>
			<div class="cluster">
				<a class="btn btn--signal btn--lg" href="<?php echo esc_url( home_url( '/concierge' ) ); ?>"><?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?></a>
				<a class="btn btn--outline btn--lg" href="<?php echo esc_url( $digicars_shop ); ?>"><?php esc_html_e( 'Browse all stock', 'digicars' ); ?></a>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
