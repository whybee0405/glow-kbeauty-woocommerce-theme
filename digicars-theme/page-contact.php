<?php
/**
 * Template Name: Contact
 *
 * Contact page with a lead form, company details (phone, email, address,
 * trading hours) and a Google Maps embed placeholder. The form posts via
 * the shared digicars_enquiry AJAX handler (topic=contact).
 *
 * @package Digicars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php /* Hero --------------------------------------------------------------- */ ?>
<section class="section section--tight">
	<div class="container container--narrow">
		<div class="stack">
			<p class="eyebrow eyebrow--signal"><?php esc_html_e( 'Contact', 'digicars' ); ?></p>
			<h1 class="t-hero"><?php esc_html_e( 'We are here when you need us.', 'digicars' ); ?></h1>
			<p class="t-lead">
				<?php esc_html_e( 'Call, email or fill in the form below — a Digicars consultant responds within one working day. For urgent matters, call us directly on 010 595 1180.', 'digicars' ); ?>
			</p>
		</div>
	</div>
</section>

<?php /* Contact layout — details + form ------------------------------------ */ ?>
<section class="section section--flush-top">
	<div class="container">
		<div class="contact-layout">

			<?php /* Left: contact details ----------------------------------- */ ?>
			<div class="contact-details">

				<div class="contact-detail-item">
					<span class="contact-detail-item__label"><?php esc_html_e( 'Phone', 'digicars' ); ?></span>
					<a class="t-2" href="tel:0105951180" style="font-size:1.6rem; text-decoration:none; color:var(--carbon);">010 595 1180</a>
					<p class="muted"><?php esc_html_e( 'Mon–Fri 08:00–17:30 · Sat 08:00–13:00', 'digicars' ); ?></p>
				</div>

				<div class="contact-detail-item">
					<span class="contact-detail-item__label"><?php esc_html_e( 'Email', 'digicars' ); ?></span>
					<a href="mailto:info@digicars.co.za" style="font-weight:600; color:var(--carbon);">info@digicars.co.za</a>
					<p class="muted"><?php esc_html_e( 'For general enquiries, finance and support.', 'digicars' ); ?></p>
				</div>

				<div class="contact-detail-item">
					<span class="contact-detail-item__label"><?php esc_html_e( 'Head office', 'digicars' ); ?></span>
					<address style="font-style:normal; font-weight:600; color:var(--carbon);">
						<?php esc_html_e( '168 Grayston Drive', 'digicars' ); ?><br>
						<?php esc_html_e( 'Sandown, Sandton, 2196', 'digicars' ); ?>
					</address>
					<p class="muted"><?php esc_html_e( 'Group sales, finance and support.', 'digicars' ); ?></p>
				</div>

				<div class="contact-detail-item">
					<span class="contact-detail-item__label"><?php esc_html_e( 'WhatsApp', 'digicars' ); ?></span>
					<a href="https://wa.me/27105951180" target="_blank" rel="noopener noreferrer" class="btn btn--outline btn--sm" style="align-self:flex-start;">
						<?php esc_html_e( 'Chat on WhatsApp', 'digicars' ); ?>
					</a>
				</div>

				<div class="contact-detail-item">
					<span class="contact-detail-item__label"><?php esc_html_e( 'Social media', 'digicars' ); ?></span>
					<div class="cluster">
						<a href="https://facebook.com/DigiCarsSA" target="_blank" rel="noopener noreferrer" style="color:var(--carbon); font-weight:600; font-size:0.9rem;">Facebook</a>
						<a href="https://instagram.com/digicarssa" target="_blank" rel="noopener noreferrer" style="color:var(--carbon); font-weight:600; font-size:0.9rem;">Instagram</a>
						<a href="https://twitter.com/digicarsza" target="_blank" rel="noopener noreferrer" style="color:var(--carbon); font-weight:600; font-size:0.9rem;">X (Twitter)</a>
					</div>
				</div>

				<?php /* Map placeholder / directions link */ ?>
				<div class="contact-map" aria-label="<?php esc_attr_e( 'Digicars Head Office location', 'digicars' ); ?>">
					<div class="stack" style="text-align:center; padding:var(--s-5);">
						<p class="muted"><?php esc_html_e( '168 Grayston Drive, Sandown, Sandton', 'digicars' ); ?></p>
						<a class="btn btn--outline btn--sm" href="https://www.google.com/maps/dir/?api=1&destination=168+Grayston+Drive+Sandown+Sandton+2196" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Get directions', 'digicars' ); ?>
						</a>
					</div>
				</div>

			</div>

			<?php /* Right: contact form ------------------------------------- */ ?>
			<div class="contact-form-wrap">
				<h2 class="t-2" style="margin-bottom:var(--s-5);"><?php esc_html_e( 'Send us a message', 'digicars' ); ?></h2>

				<form
					class="stack"
					method="post"
					data-enquiry-form
					data-topic="contact"
					novalidate
				>
					<div class="digicars-form__grid">
						<div class="field">
							<label class="label" for="contact-name"><?php esc_html_e( 'Full name', 'digicars' ); ?></label>
							<input class="input" type="text" id="contact-name" name="name" required placeholder="<?php esc_attr_e( 'Your name', 'digicars' ); ?>">
						</div>
						<div class="field">
							<label class="label" for="contact-phone"><?php esc_html_e( 'Phone number', 'digicars' ); ?></label>
							<input class="input" type="tel" id="contact-phone" name="phone" placeholder="<?php esc_attr_e( '010 000 0000', 'digicars' ); ?>">
						</div>
					</div>

					<div class="field">
						<label class="label" for="contact-email"><?php esc_html_e( 'Email address', 'digicars' ); ?></label>
						<input class="input" type="email" id="contact-email" name="email" required placeholder="<?php esc_attr_e( 'you@example.com', 'digicars' ); ?>">
					</div>

					<div class="field">
						<label class="label" for="contact-subject"><?php esc_html_e( 'Subject', 'digicars' ); ?></label>
						<select class="select" id="contact-subject" name="subject">
							<option value="general"><?php esc_html_e( 'General enquiry', 'digicars' ); ?></option>
							<option value="vehicle"><?php esc_html_e( 'Vehicle enquiry', 'digicars' ); ?></option>
							<option value="finance"><?php esc_html_e( 'Finance question', 'digicars' ); ?></option>
							<option value="trade-in"><?php esc_html_e( 'Trade-in / sell my car', 'digicars' ); ?></option>
							<option value="service"><?php esc_html_e( 'Book a service', 'digicars' ); ?></option>
							<option value="complaint"><?php esc_html_e( 'Complaint or feedback', 'digicars' ); ?></option>
						</select>
					</div>

					<div class="field">
						<label class="label" for="contact-message"><?php esc_html_e( 'Message', 'digicars' ); ?></label>
						<textarea class="textarea" id="contact-message" name="message" required placeholder="<?php esc_attr_e( 'Tell us how we can help…', 'digicars' ); ?>"></textarea>
					</div>

					<p class="muted" style="font-size:0.8rem;">
						<?php esc_html_e( 'By submitting this form you consent to Digicars contacting you. We never share your data with third parties.', 'digicars' ); ?>
					</p>

					<p class="digicars-form__error" data-form-error hidden></p>

					<div class="digicars-form__actions cluster">
						<button type="submit" class="btn btn--signal btn--lg"><?php esc_html_e( 'Send message', 'digicars' ); ?></button>
					</div>
				</form>
			</div>

		</div>
	</div>
</section>

<?php /* Branch locations strip --------------------------------------------- */ ?>
<section class="section section--tight surface-soft" data-reveal>
	<div class="container">
		<div class="section-head">
			<div class="section-head__copy stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'Our branches', 'digicars' ); ?></p>
				<h2 class="t-2"><?php esc_html_e( 'Find us in Gauteng.', 'digicars' ); ?></h2>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( home_url( '/find-a-dealer' ) ); ?>"><?php esc_html_e( 'All dealers', 'digicars' ); ?></a>
		</div>

		<div class="grid digicars-branches" style="--cols:4;">
			<div class="digicars-branch stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Sandton', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Chery and Omoda & Jaecoo off Rivonia Road.', 'digicars' ); ?></p>
				<a href="tel:0105951180" class="t-mono" style="font-size:0.85rem;">010 595 1180</a>
			</div>
			<div class="digicars-branch stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Northcliff', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Chery on Beyers Naudé Drive.', 'digicars' ); ?></p>
				<a href="tel:0105951184" class="t-mono" style="font-size:0.85rem;">010 595 1184</a>
			</div>
			<div class="digicars-branch stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Melrose Arch', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( 'Omoda & Jaecoo in the precinct.', 'digicars' ); ?></p>
				<a href="tel:0105951188" class="t-mono" style="font-size:0.85rem;">010 595 1188</a>
			</div>
			<div class="digicars-branch stack-sm">
				<h3 class="t-3"><?php esc_html_e( 'Sandown', 'digicars' ); ?></h3>
				<p class="muted"><?php esc_html_e( '168 Grayston Drive — group head office.', 'digicars' ); ?></p>
				<a href="tel:0105951180" class="t-mono" style="font-size:0.85rem;">010 595 1180</a>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
