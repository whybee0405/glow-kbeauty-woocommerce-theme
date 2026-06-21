<?php
/**
 * Template Name: Book a Service
 *
 * Service booking funnel. Hero and a booking lead form (data-topic="service")
 * covering vehicle, dealer, date, service type and contact details.
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

get_header();
?>

<?php /* 1. Hero ------------------------------------------------------------ */ ?>
<section class="section section--flush-bottom">
	<div class="container">
		<div class="hero-split">
			<div class="hero-split__copy stack">
				<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Book a service', 'digicars' ); ?></p>
				<h1 class="t-1"><?php esc_html_e( 'Book a service. Keep the plan valid.', 'digicars' ); ?></h1>
				<p class="t-lead">
					<?php esc_html_e( 'Servicing at the right intervals keeps your service or maintenance plan intact and protects your resale value. Book at Sandton, Northcliff or Melrose Arch — manufacturer-approved technicians, genuine parts, courtesy updates while you wait.', 'digicars' ); ?>
				</p>
				<div class="cluster">
					<a class="btn btn--signal" href="#service-form"><?php esc_html_e( 'Request a booking', 'digicars' ); ?></a>
					<a class="btn btn--outline" href="tel:0105951180"><?php esc_html_e( 'Call 010 595 1180', 'digicars' ); ?></a>
				</div>
				<p class="muted" style="font-size:0.85rem;">
					<?php esc_html_e( 'Service centres open Mon–Fri 07:30–17:00 and Sat 08:00–13:00 (SAST). Closed Sundays and public holidays.', 'digicars' ); ?>
				</p>
			</div>

			<div class="hero-split__stage" style="background:var(--carbon);">
				<div class="surface-carbon" style="padding:clamp(var(--s-5),4vw,var(--s-7));height:100%;display:flex;flex-direction:column;gap:var(--s-4);justify-content:center;">
					<p class="eyebrow"><?php esc_html_e( 'Good to know', 'digicars' ); ?></p>
					<ul class="stack-sm" style="list-style:none;font-size:0.95rem;color:rgba(246,246,244,0.82);">
						<li><?php esc_html_e( 'We confirm your slot by phone before you commit to a date.', 'digicars' ); ?></li>
						<li><?php esc_html_e( 'Genuine and approved parts only — your plan and warranty stay valid.', 'digicars' ); ?></li>
						<li><?php esc_html_e( 'A written quote before any out-of-plan work is started.', 'digicars' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>

<?php /* 2. Booking lead form ---------------------------------------------- */ ?>
<section class="section" id="service-form">
	<div class="container container--narrow">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Booking request', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Request your service booking.', 'digicars' ); ?></h2>
				<p class="t-lead"><?php esc_html_e( 'Send us the details and a service advisor confirms your slot, usually within one working day. Your preferred date is a request, not a guarantee, until we confirm.', 'digicars' ); ?></p>
			</div>
		</div>

		<form class="digicars-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-enquiry-form data-topic="service">
			<div class="digicars-form__grid">
				<div class="field">
					<label class="label" for="service-make"><?php esc_html_e( 'Vehicle make', 'digicars' ); ?></label>
					<input class="input" type="text" id="service-make" name="make" placeholder="<?php esc_attr_e( 'e.g. Chery', 'digicars' ); ?>" required />
				</div>
				<div class="field">
					<label class="label" for="service-model"><?php esc_html_e( 'Vehicle model', 'digicars' ); ?></label>
					<input class="input" type="text" id="service-model" name="model" placeholder="<?php esc_attr_e( 'e.g. Tiggo 7 Pro', 'digicars' ); ?>" required />
				</div>
				<div class="field">
					<label class="label" for="service-year"><?php esc_html_e( 'Year', 'digicars' ); ?></label>
					<input class="input" type="number" inputmode="numeric" min="1980" max="2026" id="service-year" name="year" placeholder="<?php esc_attr_e( 'e.g. 2024', 'digicars' ); ?>" />
				</div>
				<div class="field">
					<label class="label" for="service-reg"><?php esc_html_e( 'Registration number', 'digicars' ); ?></label>
					<input class="input" type="text" id="service-reg" name="reg" placeholder="<?php esc_attr_e( 'e.g. GP 123-456', 'digicars' ); ?>" />
				</div>
				<div class="field">
					<label class="label" for="service-dealer"><?php esc_html_e( 'Preferred dealer', 'digicars' ); ?></label>
					<select class="select" id="service-dealer" name="dealer">
						<option value=""><?php esc_html_e( 'Select…', 'digicars' ); ?></option>
						<option value="Sandton"><?php esc_html_e( 'Sandton', 'digicars' ); ?></option>
						<option value="Northcliff"><?php esc_html_e( 'Northcliff', 'digicars' ); ?></option>
						<option value="Melrose Arch"><?php esc_html_e( 'Melrose Arch', 'digicars' ); ?></option>
					</select>
				</div>
				<div class="field">
					<label class="label" for="service-date"><?php esc_html_e( 'Preferred date', 'digicars' ); ?></label>
					<input class="input" type="date" id="service-date" name="preferred_date" />
				</div>
				<div class="field">
					<label class="label" for="service-type"><?php esc_html_e( 'Service type', 'digicars' ); ?></label>
					<select class="select" id="service-type" name="service_type">
						<option value=""><?php esc_html_e( 'Select…', 'digicars' ); ?></option>
						<option value="scheduled"><?php esc_html_e( 'Scheduled / plan service', 'digicars' ); ?></option>
						<option value="major"><?php esc_html_e( 'Major service', 'digicars' ); ?></option>
						<option value="minor"><?php esc_html_e( 'Minor / interim service', 'digicars' ); ?></option>
						<option value="diagnostic"><?php esc_html_e( 'Diagnostic / warning light', 'digicars' ); ?></option>
						<option value="brakes-tyres"><?php esc_html_e( 'Brakes / tyres', 'digicars' ); ?></option>
						<option value="other"><?php esc_html_e( 'Other (describe below)', 'digicars' ); ?></option>
					</select>
				</div>
			</div>

			<div class="digicars-form__grid" style="margin-top:var(--s-4);">
				<div class="field">
					<label class="label" for="service-name"><?php esc_html_e( 'Your name', 'digicars' ); ?></label>
					<input class="input" type="text" id="service-name" name="name" autocomplete="name" required />
				</div>
				<div class="field">
					<label class="label" for="service-email"><?php esc_html_e( 'Email', 'digicars' ); ?></label>
					<input class="input" type="email" id="service-email" name="email" autocomplete="email" required />
				</div>
				<div class="field">
					<label class="label" for="service-phone"><?php esc_html_e( 'Mobile number', 'digicars' ); ?></label>
					<input class="input" type="tel" id="service-phone" name="phone" autocomplete="tel" required />
				</div>
			</div>

			<div class="field" style="margin-top:var(--s-4);">
				<label class="label" for="service-notes"><?php esc_html_e( 'Notes', 'digicars' ); ?></label>
				<textarea class="textarea" id="service-notes" name="message" placeholder="<?php esc_attr_e( 'Any noises, warning lights, courtesy car needed, drop-off vs wait…', 'digicars' ); ?>"></textarea>
			</div>

			<p class="field-hint" style="margin-top:var(--s-3);">
				<?php esc_html_e( 'We confirm availability before your booking is final. We do not sell your details.', 'digicars' ); ?>
			</p>

			<div class="digicars-form__actions cluster">
				<button type="submit" class="btn btn--signal"><?php esc_html_e( 'Request booking', 'digicars' ); ?></button>
				<span class="muted" style="font-size:0.85rem;">
					<?php esc_html_e( 'Urgent?', 'digicars' ); ?>
					<a href="tel:0105951180">010 595 1180</a>
				</span>
			</div>

			<p class="digicars-form__error field-error" data-form-error hidden></p>
		</form>
	</div>
</section>

<?php
get_footer();
