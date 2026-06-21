<?php
/**
 * Template Name: About
 *
 * The Digicars trust / brand story. Digital-first, phygital approach, the
 * "Fueled by passion. Driven by technology." statement, four numbered
 * principles and the branch footprint, with CTAs to browse stock and ask the
 * Concierge.
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
?>

<?php /* 1. Hero ------------------------------------------------------------ */ ?>
<section class="section section--tight">
	<div class="container container--narrow">
		<div class="stack">
			<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'About', 'digicars' ); ?></p>
			<h1 class="t-hero"><?php esc_html_e( 'We made buying a car feel like the rest of your life — online, on your terms.', 'digicars' ); ?></h1>
			<p class="t-lead">
				<?php esc_html_e( 'Digicars is a digital-first, multi-brand car marketplace built in South Africa. You research, work out what you can afford, apply for finance and enquire — all online — then collect from one of our Gauteng branches or have your car delivered. The showroom comes to you.', 'digicars' ); ?>
			</p>
			<div class="cluster">
				<a class="btn btn--signal" href="<?php echo esc_url( $digicars_shop_url ); ?>"><?php esc_html_e( 'Browse cars in stock', 'digicars' ); ?></a>
				<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/concierge' ) ); ?>"><?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php /* 2. The Digicars approach ------------------------------------------ */ ?>
<section class="section section--tight">
	<div class="container">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'The Digicars approach', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Research, afford, finance, enquire — then collect or get it delivered.', 'digicars' ); ?></h2>
				<p class="t-lead"><?php esc_html_e( 'It’s the same journey a dealership puts you through, just without the sitting around. Every step happens in your own time, and a human picks it up the moment you want one.', 'digicars' ); ?></p>
			</div>
		</div>

		<ol class="grid digicars-steps" style="--cols:4;list-style:none;">
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">01</span>
				<h3 class="t-3"><?php esc_html_e( 'Research', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Filter verified stock by make, body type, budget or monthly instalment. Compare cars side by side.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">02</span>
				<h3 class="t-3"><?php esc_html_e( 'Afford', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Use the affordability calculator to see a realistic monthly before you commit to anything.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">03</span>
				<h3 class="t-3"><?php esc_html_e( 'Finance', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'One application goes to the major banks. We come back with an indicative rate and amount.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">04</span>
				<h3 class="t-3"><?php esc_html_e( 'Enquire', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Send an enquiry and a consultant takes it from there — collect in Gauteng or have it delivered.', 'digicars' ); ?></p>
			</li>
		</ol>
	</div>
</section>

<?php /* 3. Brand statement (surface-carbon panel) ------------------------- */ ?>
<section class="section">
	<div class="container">
		<div class="surface-carbon digicars-statement" style="border-radius:var(--r-3);padding:clamp(var(--s-6),5vw,var(--s-9));">
			<div class="stack" style="max-width:64ch;">
				<p class="eyebrow eyebrow--volt"><?php esc_html_e( 'Fueled by passion. Driven by technology.', 'digicars' ); ?></p>
				<h2 class="t-1" style="color:var(--paper);"><?php esc_html_e( 'A dealer’s instinct, an engineer’s tooling.', 'digicars' ); ?></h2>
				<p class="t-lead">
					<?php esc_html_e( 'Passion is the people — consultants who actually know these cars and want you in the right one. Technology is what lets them do it at scale: a bespoke CRM that keeps your deal moving, AI-assisted discovery through the Concierge that reads our live catalogue, and verified stock so what you see online is what you collect.', 'digicars' ); ?>
				</p>
				<div class="cluster" style="margin-top:var(--s-3);">
					<a class="btn btn--signal" href="<?php echo esc_url( home_url( '/concierge' ) ); ?>"><?php esc_html_e( 'Ask the Concierge', 'digicars' ); ?></a>
					<a class="btn btn--outline" href="<?php echo esc_url( $digicars_shop_url ); ?>"><?php esc_html_e( 'See the stock', 'digicars' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php /* 4. Principles ----------------------------------------------------- */ ?>
<section class="section section--tight surface-soft">
	<div class="container">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'What we hold ourselves to', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Four principles.', 'digicars' ); ?></h2>
			</div>
		</div>

		<ol class="grid digicars-principles" style="--cols:4;list-style:none;">
			<li class="digicars-principle stack-sm">
				<span class="digicars-step__num t-mono">01</span>
				<h3 class="t-3"><?php esc_html_e( 'Verified vehicles', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Every car is inspected and its history checked before it goes live. The listing is the car.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-principle stack-sm">
				<span class="digicars-step__num t-mono">02</span>
				<h3 class="t-3"><?php esc_html_e( 'Finance made clear', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Honest instalments, the banks shown, fees disclosed. No surprises buried in the small print.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-principle stack-sm">
				<span class="digicars-step__num t-mono">03</span>
				<h3 class="t-3"><?php esc_html_e( 'Phygital convenience', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Do it all online, or walk into a branch. Start on the couch and finish in person, or the reverse.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-principle stack-sm">
				<span class="digicars-step__num t-mono">04</span>
				<h3 class="t-3"><?php esc_html_e( 'Multi-brand choice', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Chery, Omoda, Jaecoo and quality multi-brand pre-owned — we recommend the right car, not just our car.', 'digicars' ); ?></p>
			</li>
		</ol>
	</div>
</section>

<?php /* 5. Branch footprint ----------------------------------------------- */ ?>
<section class="section section--tight">
	<div class="container">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'Where to find us', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Gauteng branches, nationwide delivery.', 'digicars' ); ?></h2>
				<p class="t-lead"><?php esc_html_e( 'Our showrooms cluster around Sandton, with our group head office on Grayston Drive. Wherever you are in South Africa, we can deliver.', 'digicars' ); ?></p>
			</div>
		</div>

		<div class="grid digicars-branches" style="--cols:4;">
			<div class="digicars-branch stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Sandton', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Chery and Omoda & Jaecoo showrooms off Rivonia Road.', 'digicars' ); ?></p>
			</div>
			<div class="digicars-branch stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Northcliff', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Chery on Beyers Naudé Drive, serving the west of Joburg.', 'digicars' ); ?></p>
			</div>
			<div class="digicars-branch stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Melrose Arch', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Omoda & Jaecoo in the heart of the precinct.', 'digicars' ); ?></p>
			</div>
			<div class="digicars-branch stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Sandown head office', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( '168 Grayston Drive — group sales, finance and support.', 'digicars' ); ?></p>
			</div>
		</div>

		<div class="cluster" style="margin-top:var(--s-7);">
			<a class="btn btn--signal" href="<?php echo esc_url( home_url( '/find-a-dealer' ) ); ?>"><?php esc_html_e( 'Find a dealer', 'digicars' ); ?></a>
			<span class="muted" style="font-size:0.9rem;">
				<a href="mailto:info@digicars.co.za">info@digicars.co.za</a> · <a href="tel:0105951180">010 595 1180</a>
			</span>
		</div>
	</div>
</section>

<?php
get_footer();
