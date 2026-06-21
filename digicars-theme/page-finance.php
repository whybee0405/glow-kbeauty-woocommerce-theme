<?php
/**
 * Template Name: Finance
 *
 * Affordability / finance funnel landing page. Pre-qualification story, an
 * affordability calculator that reuses the same hooks as the vehicle page
 * (affordability.js), a finance application lead form and finance partners.
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

// Sensible default vehicle price for the calculator prefill.
$digicars_aff_price   = 350000;
$digicars_aff_monthly = function_exists( 'digicars_monthly_from' ) ? digicars_monthly_from( $digicars_aff_price ) : 0;
?>

<?php /* 1. Hero ------------------------------------------------------------ */ ?>
<section class="section section--flush-bottom">
	<div class="container">
		<div class="hero-split">
			<div class="hero-split__copy stack">
				<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Finance', 'digicars' ); ?></p>
				<h1 class="t-1"><?php esc_html_e( 'Know what you can afford before you fall in love with the car.', 'digicars' ); ?></h1>
				<p class="t-lead">
					<?php esc_html_e( 'Pre-qualify online in minutes. We submit to WesBank, MFC, Absa, Standard Bank and Nedbank on your behalf, then come back with a real instalment — not a guess. One application, multiple banks, whether you walk in or buy from the couch.', 'digicars' ); ?>
				</p>
				<div class="cluster">
					<a class="btn btn--signal" href="#finance-apply"><?php esc_html_e( 'Pre-qualify now', 'digicars' ); ?></a>
					<a class="btn btn--outline" href="<?php echo esc_url( $digicars_shop_url ); ?>"><?php esc_html_e( 'Browse cars in stock', 'digicars' ); ?></a>
				</div>
				<p class="muted" style="font-size:0.85rem;">
					<?php esc_html_e( 'NCR-compliant affordability assessment. Bank approval and T&Cs apply.', 'digicars' ); ?>
				</p>
			</div>

			<div class="hero-split__stage" style="background:var(--carbon);">
				<div class="surface-carbon" style="padding:clamp(var(--s-5),4vw,var(--s-7));height:100%;display:flex;flex-direction:column;gap:var(--s-4);justify-content:center;">
					<p class="eyebrow"><?php esc_html_e( 'Typical pre-qualification', 'digicars' ); ?></p>
					<div class="stack-sm">
						<p class="t-mono" style="font-size:0.85rem;color:rgba(246,246,244,0.6);"><?php esc_html_e( 'R350 000 over 72 months', 'digicars' ); ?></p>
						<p class="t-2" style="color:var(--paper);">
							<?php echo $digicars_aff_monthly > 0 ? 'R ' . esc_html( number_format( $digicars_aff_monthly, 0, '.', ' ' ) ) . ' <span style="font-size:1rem;font-weight:500;">' . esc_html__( 'p/m', 'digicars' ) . '</span>' : esc_html__( 'On request', 'digicars' ); ?>
						</p>
					</div>
					<p class="muted" style="font-size:0.82rem;">
						<?php esc_html_e( 'Estimate at 11.5% linked, no deposit, no balloon. Your rate is set by the bank on approval.', 'digicars' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

<?php /* 2. Affordability calculator (reuses affordability.js hooks) --------- */ ?>
<section class="section">
	<div class="container">
		<div
			class="pdp-afford surface-carbon"
			data-affordability
			data-price="<?php echo esc_attr( $digicars_aff_price ); ?>"
		>
			<div class="pdp-afford__intro">
				<p class="eyebrow"><?php esc_html_e( 'Affordability calculator', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Work backwards from your budget.', 'digicars' ); ?></h2>
				<p class="muted"><?php esc_html_e( 'Set a deposit, term and balloon to see an indicative instalment on a R350 000 car. Move the numbers until the monthly fits — then find a car around that price.', 'digicars' ); ?></p>
			</div>

			<form class="pdp-afford__form" novalidate>
				<label class="pdp-afford__field">
					<span class="pdp-afford__field-label"><?php esc_html_e( 'Deposit (R)', 'digicars' ); ?></span>
					<input type="number" inputmode="numeric" min="0" step="5000" value="0" data-aff-deposit />
				</label>

				<label class="pdp-afford__field">
					<span class="pdp-afford__field-label"><?php esc_html_e( 'Term (months)', 'digicars' ); ?></span>
					<select data-aff-term>
						<?php
						foreach ( array( 12, 24, 36, 48, 60, 72 ) as $digicars_term_option ) :
							$digicars_term_selected = ( 72 === $digicars_term_option ) ? ' selected' : '';
							?>
							<option value="<?php echo esc_attr( $digicars_term_option ); ?>"<?php echo esc_attr( $digicars_term_selected ); ?>>
								<?php echo esc_html( $digicars_term_option ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>

				<label class="pdp-afford__field">
					<span class="pdp-afford__field-label"><?php esc_html_e( 'Balloon (%)', 'digicars' ); ?></span>
					<input type="number" inputmode="numeric" min="0" max="40" step="5" value="0" data-aff-balloon />
				</label>
			</form>

			<div class="pdp-afford__output">
				<span class="pdp-afford__output-label t-mono"><?php esc_html_e( 'Estimated monthly', 'digicars' ); ?></span>
				<span class="pdp-afford__output-value" data-aff-output>
					<?php echo $digicars_aff_monthly > 0 ? 'R ' . esc_html( number_format( $digicars_aff_monthly, 0, '.', ' ' ) ) : esc_html__( 'On request', 'digicars' ); ?>
				</span>
				<span class="pdp-afford__output-note muted"><?php esc_html_e( 'Estimate only, at 11.5% per annum. Excludes initiation and monthly service fees. Final figures confirmed by the bank on approval.', 'digicars' ); ?></span>
			</div>
		</div>
	</div>
</section>

<?php /* 3. How it works --------------------------------------------------- */ ?>
<section class="section section--tight surface-soft">
	<div class="container">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'How it works', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Three steps, mostly from your phone.', 'digicars' ); ?></h2>
			</div>
		</div>

		<ol class="grid digicars-steps" style="--cols:3;list-style:none;">
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">01</span>
				<h3 class="t-3"><?php esc_html_e( 'Pre-qualify', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Complete one finance application below. We run an affordability check and submit to the major banks. You get an indicative amount and rate, usually the same working day.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">02</span>
				<h3 class="t-3"><?php esc_html_e( 'Choose your car', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Shop in stock knowing your number. Filter by monthly instalment, not just price, so every car you view is one you can actually drive away in.', 'digicars' ); ?></p>
			</li>
			<li class="digicars-step stack-sm">
				<span class="digicars-step__num t-mono">03</span>
				<h3 class="t-3"><?php esc_html_e( 'Drive away', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'We finalise the deal, FICA and delivery. Collect from Sandton, Northcliff or Melrose Arch, or have it delivered to your door in Gauteng.', 'digicars' ); ?></p>
			</li>
		</ol>
	</div>
</section>

<?php /* 4. Application form ------------------------------------------------ */ ?>
<section class="section" id="finance-apply">
	<div class="container container--narrow">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Pre-qualify', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Start your finance application.', 'digicars' ); ?></h2>
				<p class="t-lead"><?php esc_html_e( 'This is a no-obligation enquiry, not a binding contract. A Digicars finance consultant reviews it and comes back within one working day. We never run a credit bureau check without your consent.', 'digicars' ); ?></p>
			</div>
		</div>

		<form class="digicars-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-enquiry-form data-topic="finance">
			<div class="digicars-form__grid">
				<div class="field">
					<label class="label" for="finance-name"><?php esc_html_e( 'Full name', 'digicars' ); ?></label>
					<input class="input" type="text" id="finance-name" name="name" autocomplete="name" required />
				</div>
				<div class="field">
					<label class="label" for="finance-email"><?php esc_html_e( 'Email', 'digicars' ); ?></label>
					<input class="input" type="email" id="finance-email" name="email" autocomplete="email" required />
				</div>
				<div class="field">
					<label class="label" for="finance-phone"><?php esc_html_e( 'Mobile number', 'digicars' ); ?></label>
					<input class="input" type="tel" id="finance-phone" name="phone" autocomplete="tel" required />
				</div>
				<div class="field">
					<label class="label" for="finance-employment"><?php esc_html_e( 'Employment status', 'digicars' ); ?></label>
					<select class="select" id="finance-employment" name="employment">
						<option value=""><?php esc_html_e( 'Select…', 'digicars' ); ?></option>
						<option value="permanent"><?php esc_html_e( 'Permanently employed', 'digicars' ); ?></option>
						<option value="contract"><?php esc_html_e( 'Contract / fixed-term', 'digicars' ); ?></option>
						<option value="self-employed"><?php esc_html_e( 'Self-employed / business owner', 'digicars' ); ?></option>
						<option value="commission"><?php esc_html_e( 'Commission earner', 'digicars' ); ?></option>
						<option value="pensioner"><?php esc_html_e( 'Pensioner', 'digicars' ); ?></option>
					</select>
				</div>
				<div class="field">
					<label class="label" for="finance-income"><?php esc_html_e( 'Gross monthly income (R)', 'digicars' ); ?></label>
					<input class="input" type="number" inputmode="numeric" min="0" step="500" id="finance-income" name="income" placeholder="<?php esc_attr_e( 'e.g. 28 000', 'digicars' ); ?>" />
				</div>
				<div class="field">
					<label class="label" for="finance-vehicle"><?php esc_html_e( 'Vehicle of interest', 'digicars' ); ?></label>
					<input class="input" type="text" id="finance-vehicle" name="vehicle" placeholder="<?php esc_attr_e( 'e.g. Chery Tiggo 7 Pro, or "open to suggestions"', 'digicars' ); ?>" />
				</div>
			</div>

			<div class="field" style="margin-top:var(--s-4);">
				<label class="label" for="finance-message"><?php esc_html_e( 'Anything we should know?', 'digicars' ); ?></label>
				<textarea class="textarea" id="finance-message" name="message" placeholder="<?php esc_attr_e( 'Deposit you have available, trade-in to settle, preferred instalment…', 'digicars' ); ?>"></textarea>
			</div>

			<p class="field-hint" style="margin-top:var(--s-3);">
				<?php esc_html_e( 'By submitting you agree we may contact you about your enquiry. We do not sell your details. Honest answers get you an accurate number — over-stating income just wastes everyone’s time.', 'digicars' ); ?>
			</p>

			<div class="digicars-form__actions cluster">
				<button type="submit" class="btn btn--signal"><?php esc_html_e( 'Submit application', 'digicars' ); ?></button>
				<span class="muted" style="font-size:0.85rem;">
					<?php esc_html_e( 'Prefer to talk?', 'digicars' ); ?>
					<a href="tel:0105951180">010 595 1180</a>
				</span>
			</div>

			<p class="digicars-form__error field-error" data-form-error hidden></p>
		</form>
	</div>
</section>

<?php /* 5. Finance partners ----------------------------------------------- */ ?>
<section class="section section--tight section--flush-top">
	<div class="container">
		<p class="eyebrow" style="margin-bottom:var(--s-4);"><?php esc_html_e( 'We submit to', 'digicars' ); ?></p>
		<div class="cluster digicars-partners">
			<span class="badge badge--used">WesBank</span>
			<span class="badge badge--used">MFC</span>
			<span class="badge badge--used">Absa</span>
			<span class="badge badge--used">Standard Bank</span>
			<span class="badge badge--used">Nedbank</span>
		</div>
		<p class="muted" style="margin-top:var(--s-4);max-width:60ch;font-size:0.9rem;">
			<?php esc_html_e( 'Applying through several banks at once means you take the best offer on the table — different banks price the same applicant differently.', 'digicars' ); ?>
		</p>
	</div>
</section>

<?php
get_footer();
