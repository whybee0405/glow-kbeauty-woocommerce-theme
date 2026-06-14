<?php
/**
 * Template Name: Contact
 *
 * @package Glow_KBeauty
 */

get_header();

if ( glow_is_elementor_page() ) {
	echo '<main id="main">';
	while ( have_posts() ) { the_post(); the_content(); }
	echo '</main>';
	get_footer();
	return;
}
?>

<main id="main">

	<div class="container">
		<header class="page-hero">
			<p class="eyebrow"><?php esc_html_e( 'Get in touch', 'glow-kbeauty' ); ?></p>
			<h1 class="t-hero"><?php esc_html_e( 'Talk to a person about your skin.', 'glow-kbeauty' ); ?></h1>
			<p class="lead"><?php esc_html_e( 'No chatbots, no ticket numbers that go nowhere. A human reads every message and replies within one working day.', 'glow-kbeauty' ); ?></p>
		</header>
	</div>

	<section class="section-tight">
		<div class="container contact-grid">

			<div class="contact-channels" data-reveal>
				<div class="channel">
					<span class="mono"><?php esc_html_e( 'WhatsApp', 'glow-kbeauty' ); ?></span>
					<a href="<?php echo esc_url( get_theme_mod( 'glow_whatsapp_url', 'https://wa.me/27110000000' ) ); ?>">
						<?php echo esc_html( get_theme_mod( 'glow_whatsapp_number', '+27 11 000 0000' ) ); ?>
					</a>
					<p><?php esc_html_e( 'Fastest for order questions. Voice notes welcome.', 'glow-kbeauty' ); ?></p>
				</div>
				<div class="channel">
					<span class="mono"><?php esc_html_e( 'Email', 'glow-kbeauty' ); ?></span>
					<?php $email = get_theme_mod( 'glow_email', 'hello@glowkbeauty.co.za' ); ?>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<p><?php esc_html_e( 'Best for ingredient questions — we can attach the documentation.', 'glow-kbeauty' ); ?></p>
				</div>
				<div class="channel">
					<span class="mono"><?php esc_html_e( 'Hours', 'glow-kbeauty' ); ?></span>
					<a href="#contact-form"><?php echo esc_html( get_theme_mod( 'glow_hours', 'Mon–Fri, 08:30–17:00 SAST' ) ); ?></a>
					<p><?php esc_html_e( 'Messages after hours get answered first thing the next morning.', 'glow-kbeauty' ); ?></p>
				</div>

			</div>

			<div class="contact-form-panel" id="contact-form" data-reveal>
				<form data-ajax-form="glow_contact">
					<div class="field">
						<label for="contact-name"><?php esc_html_e( 'Your name', 'glow-kbeauty' ); ?></label>
						<input type="text" id="contact-name" name="name" required autocomplete="name" />
					</div>
					<div class="field">
						<label for="contact-email"><?php esc_html_e( 'Email', 'glow-kbeauty' ); ?></label>
						<input type="email" id="contact-email" name="email" required autocomplete="email" />
					</div>
					<div class="field">
						<label for="contact-topic"><?php esc_html_e( 'What\'s this about?', 'glow-kbeauty' ); ?></label>
						<select id="contact-topic" name="topic">
							<option value="<?php esc_attr_e( 'Order help', 'glow-kbeauty' ); ?>"><?php esc_html_e( 'Order help', 'glow-kbeauty' ); ?></option>
							<option value="<?php esc_attr_e( 'Routine advice', 'glow-kbeauty' ); ?>"><?php esc_html_e( 'Routine advice', 'glow-kbeauty' ); ?></option>
							<option value="<?php esc_attr_e( 'Skin reaction', 'glow-kbeauty' ); ?>"><?php esc_html_e( 'Skin reaction', 'glow-kbeauty' ); ?></option>
							<option value="<?php esc_attr_e( 'Stockists & wholesale', 'glow-kbeauty' ); ?>"><?php esc_html_e( 'Stockists & wholesale', 'glow-kbeauty' ); ?></option>
							<option value="<?php esc_attr_e( 'Something else', 'glow-kbeauty' ); ?>"><?php esc_html_e( 'Something else', 'glow-kbeauty' ); ?></option>
						</select>
					</div>
					<div class="field">
						<label for="contact-message"><?php esc_html_e( 'Message', 'glow-kbeauty' ); ?></label>
						<textarea id="contact-message" name="message" required placeholder="<?php esc_attr_e( 'If it\'s about a reaction, the product name and batch number help us move fast.', 'glow-kbeauty' ); ?>"></textarea>
					</div>
					<button class="btn btn-solid" type="submit"><?php esc_html_e( 'Send message', 'glow-kbeauty' ); ?></button>
					<p class="form-note"><?php esc_html_e( 'We reply within one working day. Usually faster.', 'glow-kbeauty' ); ?></p>
				</form>
			</div>

		</div>
	</section>

	<?php
	while ( have_posts() ) :
		the_post();
		if ( get_the_content() ) :
			?>
			<section class="section-tight">
				<div class="container">
					<div class="prose"><?php the_content(); ?></div>
				</div>
			</section>
			<?php
		endif;
	endwhile;
	?>

</main>

<?php get_footer(); ?>
