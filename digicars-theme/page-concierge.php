<?php
/**
 * Template Name: Concierge
 *
 * Dedicated Concierge discovery page. Hosts the Helix AI search shortcode
 * in a full-width, distraction-free layout. All "Ask the Concierge" CTAs
 * site-wide link here.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php /* Hero --------------------------------------------------------------- */ ?>
<section class="section section--tight surface-carbon">
	<div class="container">
		<div class="stack" style="text-align:center; align-items:center; max-width:640px; margin-inline:auto;">
			<p class="eyebrow eyebrow--volt"><?php esc_html_e( 'AI-powered search', 'digicars' ); ?></p>
			<h1 class="t-hero" style="color:var(--paper);"><?php esc_html_e( 'Tell us what you need.', 'digicars' ); ?></h1>
			<p class="t-lead" style="color:rgba(246,246,244,0.68);">
				<?php esc_html_e( 'Describe your ideal car — lifestyle, budget, size, brand — and the Concierge will shortlist verified vehicles from our live catalogue that actually match how you drive.', 'digicars' ); ?>
			</p>
		</div>
	</div>
</section>

<?php /* Helix search -------------------------------------------------------- */ ?>
<section class="section section--tight">
	<div class="container">
		<div class="concierge-page">
			<div class="concierge-page__inner">
				<?php echo do_shortcode( '[helix_search]' ); ?>
			</div>
		</div>
	</div>
</section>

<?php /* How it works -------------------------------------------------------- */ ?>
<section class="section section--tight surface-soft" data-reveal>
	<div class="container">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'How the Concierge works', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Three steps to your next car.', 'digicars' ); ?></h2>
			</div>
		</div>

		<ol class="grid digicars-steps" style="--cols:3; list-style:none;">
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">01</span>
				<h3 class="t-3"><?php esc_html_e( 'Describe your life', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Tell the Concierge how you drive, your budget, family size, or preferred brand. Anything helps — even "something stylish under R300k".', 'digicars' ); ?></p>
			</li>
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">02</span>
				<h3 class="t-3"><?php esc_html_e( 'Get a shortlist', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'The Concierge reads our live catalogue and returns vehicles that match — with prices, monthly estimates and specs.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">03</span>
				<h3 class="t-3"><?php esc_html_e( 'Enquire or finance', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Enquire on any result or jump straight to finance. A consultant takes it from there — collect in Gauteng or have it delivered.', 'digicars' ); ?></p>
			</li>
		</ol>
	</div>
</section>

<?php
get_footer();
