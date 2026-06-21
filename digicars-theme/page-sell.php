<?php
/**
 * Template Name: Sell / Trade-in
 *
 * Trade-in / instant-offer funnel. Hero, an instant-offer-style lead form
 * (data-topic="trade-in") and a "why trade in with Digicars" value list.
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
<section class="section section--flush-bottom">
	<div class="container">
		<div class="hero-split">
			<div class="hero-split__copy stack">
				<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Sell or trade in', 'digicars' ); ?></p>
				<h1 class="t-1"><?php esc_html_e( 'Sell or trade in. Get a real number, fast.', 'digicars' ); ?></h1>
				<p class="t-lead">
					<?php esc_html_e( 'Tell us about your car and a consultant comes back within one working day with an indicative offer — no haggling theatre, no lowball. Settle your finance, offset the value against your next Digicars car, or take a clean cash deal. Your call.', 'digicars' ); ?>
				</p>
				<div class="cluster">
					<a class="btn btn--signal" href="#trade-in-form"><?php esc_html_e( 'Get my offer', 'digicars' ); ?></a>
					<a class="btn btn--outline" href="<?php echo esc_url( $digicars_shop_url ); ?>"><?php esc_html_e( 'See what you could upgrade to', 'digicars' ); ?></a>
				</div>
				<p class="muted" style="font-size:0.85rem;">
					<?php esc_html_e( 'Indicative offer subject to a physical inspection at Sandton, Northcliff or Melrose Arch.', 'digicars' ); ?>
				</p>
			</div>

			<div class="hero-split__stage" style="background:var(--carbon);">
				<div class="surface-carbon" style="padding:clamp(var(--s-5),4vw,var(--s-7));height:100%;display:flex;flex-direction:column;gap:var(--s-4);justify-content:center;">
					<p class="eyebrow"><?php esc_html_e( 'How it usually goes', 'digicars' ); ?></p>
					<ol class="stack-sm" style="list-style:none;font-size:0.95rem;color:rgba(246,246,244,0.82);">
						<li><span class="t-mono" style="color:var(--signal);">1 — </span><?php esc_html_e( 'You send the details below.', 'digicars' ); ?></li>
						<li><span class="t-mono" style="color:var(--signal);">2 — </span><?php esc_html_e( 'We come back with an indicative figure within one working day.', 'digicars' ); ?></li>
						<li><span class="t-mono" style="color:var(--signal);">3 — </span><?php esc_html_e( 'Quick inspection, we confirm, and we handle the settlement and paperwork.', 'digicars' ); ?></li>
					</ol>
				</div>
			</div>
		</div>
	</div>
</section>

<?php /* 2. Instant-offer lead form ---------------------------------------- */ ?>
<section class="section" id="trade-in-form">
	<div class="container container--narrow">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Your car', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Tell us what you’re driving.', 'digicars' ); ?></h2>
				<p class="t-lead"><?php esc_html_e( 'The more accurate the detail, the closer our indicative offer will be to the final number. A consultant responds within one working day.', 'digicars' ); ?></p>
			</div>
		</div>

		<form class="digicars-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-enquiry-form data-topic="trade-in">
			<div class="digicars-form__grid">
				<div class="field">
					<label class="label" for="trade-reg"><?php esc_html_e( 'Registration number', 'digicars' ); ?></label>
					<input class="input" type="text" id="trade-reg" name="reg" placeholder="<?php esc_attr_e( 'e.g. CA 123-456', 'digicars' ); ?>" />
				</div>
				<div class="field">
					<label class="label" for="trade-year"><?php esc_html_e( 'Year', 'digicars' ); ?></label>
					<input class="input" type="number" inputmode="numeric" min="1980" max="2026" id="trade-year" name="year" placeholder="<?php esc_attr_e( 'e.g. 2020', 'digicars' ); ?>" />
				</div>
				<div class="field">
					<label class="label" for="trade-make"><?php esc_html_e( 'Make', 'digicars' ); ?></label>
					<input class="input" type="text" id="trade-make" name="make" placeholder="<?php esc_attr_e( 'e.g. Volkswagen', 'digicars' ); ?>" required />
				</div>
				<div class="field">
					<label class="label" for="trade-model"><?php esc_html_e( 'Model', 'digicars' ); ?></label>
					<input class="input" type="text" id="trade-model" name="model" placeholder="<?php esc_attr_e( 'e.g. Polo', 'digicars' ); ?>" required />
				</div>
				<div class="field">
					<label class="label" for="trade-variant"><?php esc_html_e( 'Variant / derivative', 'digicars' ); ?></label>
					<input class="input" type="text" id="trade-variant" name="variant" placeholder="<?php esc_attr_e( 'e.g. 1.0 TSI Comfortline', 'digicars' ); ?>" />
				</div>
				<div class="field">
					<label class="label" for="trade-mileage"><?php esc_html_e( 'Mileage (km)', 'digicars' ); ?></label>
					<input class="input" type="number" inputmode="numeric" min="0" step="1000" id="trade-mileage" name="mileage" placeholder="<?php esc_attr_e( 'e.g. 86 000', 'digicars' ); ?>" />
				</div>
				<div class="field">
					<label class="label" for="trade-condition"><?php esc_html_e( 'Overall condition', 'digicars' ); ?></label>
					<select class="select" id="trade-condition" name="condition">
						<option value=""><?php esc_html_e( 'Select…', 'digicars' ); ?></option>
						<option value="excellent"><?php esc_html_e( 'Excellent — like new, full service history', 'digicars' ); ?></option>
						<option value="good"><?php esc_html_e( 'Good — minor wear, well maintained', 'digicars' ); ?></option>
						<option value="fair"><?php esc_html_e( 'Fair — visible wear or small repairs needed', 'digicars' ); ?></option>
						<option value="poor"><?php esc_html_e( 'Poor — mechanical or body work required', 'digicars' ); ?></option>
					</select>
				</div>
				<div class="field">
					<label class="label" for="trade-upgrade"><?php esc_html_e( 'Upgrading to (optional)', 'digicars' ); ?></label>
					<input class="input" type="text" id="trade-upgrade" name="vehicle" placeholder="<?php esc_attr_e( 'e.g. Chery Tiggo 7 Pro', 'digicars' ); ?>" />
				</div>
			</div>

			<div class="digicars-form__grid" style="margin-top:var(--s-4);">
				<div class="field">
					<label class="label" for="trade-name"><?php esc_html_e( 'Your name', 'digicars' ); ?></label>
					<input class="input" type="text" id="trade-name" name="name" autocomplete="name" required />
				</div>
				<div class="field">
					<label class="label" for="trade-email"><?php esc_html_e( 'Email', 'digicars' ); ?></label>
					<input class="input" type="email" id="trade-email" name="email" autocomplete="email" required />
				</div>
				<div class="field">
					<label class="label" for="trade-phone"><?php esc_html_e( 'Mobile number', 'digicars' ); ?></label>
					<input class="input" type="tel" id="trade-phone" name="phone" autocomplete="tel" required />
				</div>
			</div>

			<div class="field" style="margin-top:var(--s-4);">
				<label class="label" for="trade-message"><?php esc_html_e( 'Anything else?', 'digicars' ); ?></label>
				<textarea class="textarea" id="trade-message" name="message" placeholder="<?php esc_attr_e( 'Outstanding finance to settle, extras fitted, accident history…', 'digicars' ); ?>"></textarea>
			</div>

			<p class="field-hint" style="margin-top:var(--s-3);">
				<?php esc_html_e( 'The offer we send is indicative and confirmed after a quick inspection. We do not sell your details.', 'digicars' ); ?>
			</p>

			<div class="digicars-form__actions cluster">
				<button type="submit" class="btn btn--signal"><?php esc_html_e( 'Get my indicative offer', 'digicars' ); ?></button>
				<span class="muted" style="font-size:0.85rem;">
					<?php esc_html_e( 'Or call', 'digicars' ); ?>
					<a href="tel:0105951180">010 595 1180</a>
				</span>
			</div>

			<p class="digicars-form__error field-error" data-form-error hidden></p>
		</form>
	</div>
</section>

<?php /* 3. Why trade in with Digicars ------------------------------------- */ ?>
<section class="section section--tight surface-soft">
	<div class="container">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'Why trade in with Digicars', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'We take the admin off your hands.', 'digicars' ); ?></h2>
			</div>
		</div>

		<div class="grid" style="--cols:3;">
			<div class="digicars-value stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Settlement handled', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Still owe money on the car? We settle the outstanding balance directly with your bank and net it off the deal, so you never carry two repayments at once.', 'digicars' ); ?></p>
			</div>
			<div class="digicars-value stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Paperwork done', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Change of ownership, NaTIS, roadworthy and licence transfer — our team files it all. You sign once and we handle the rest.', 'digicars' ); ?></p>
			</div>
			<div class="digicars-value stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Offset against your next car', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Put your trade-in value straight towards the deposit on your next Digicars car and shrink the amount you finance — or take a clean cash payout instead.', 'digicars' ); ?></p>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
