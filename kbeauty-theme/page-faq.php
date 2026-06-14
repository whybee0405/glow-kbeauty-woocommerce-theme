<?php
/**
 * Template Name: Help & FAQ
 *
 * Q&As come from glow_faq_items(), the same source that builds the
 * FAQPage schema — page and structured data can never drift apart.
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
			<p class="eyebrow"><?php esc_html_e( 'Straight answers', 'glow-kbeauty' ); ?></p>
			<h1 class="t-hero"><?php esc_html_e( 'Help & FAQ', 'glow-kbeauty' ); ?></h1>
			<p class="lead"><?php esc_html_e( 'The questions we actually get — about authenticity, sensitive skin, delivery and what to do when something goes wrong.', 'glow-kbeauty' ); ?></p>
		</header>
	</div>

	<section class="section-tight">
		<div class="container">
			<div class="faq-list" data-reveal>
				<?php foreach ( glow_faq_items() as $glow_i => $glow_faq ) : ?>
					<details class="faq-item" <?php echo 0 === $glow_i ? 'open' : ''; ?>>
						<summary><?php echo esc_html( $glow_faq['question'] ); ?></summary>
						<div class="faq-body"><p><?php echo esc_html( $glow_faq['answer'] ); ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="split-panel" data-reveal>
				<div class="panel-a">
					<p class="eyebrow"><?php esc_html_e( 'Sensitive skin?', 'glow-kbeauty' ); ?></p>
					<h3 class="t-2"><?php esc_html_e( 'Been burnt before. Literally.', 'glow-kbeauty' ); ?></h3>
					<p><?php esc_html_e( 'We get it — “gentle” on the front of a bottle means nothing. Filter by the Sensitive skin type, read the actives chips before you click, and patch test everything for three days. If something still reacts, our returns policy covers opened products.', 'glow-kbeauty' ); ?></p>
					<a class="btn btn-outline" href="<?php echo esc_url( glow_tax_url( 'sensitive', 'skin_type' ) ); ?>"><?php esc_html_e( 'Shop sensitive-safe', 'glow-kbeauty' ); ?></a>
				</div>
				<div class="panel-b">
					<p class="eyebrow"><?php esc_html_e( 'Still unsure?', 'glow-kbeauty' ); ?></p>
					<h3 class="t-2"><?php esc_html_e( 'Ask before you buy.', 'glow-kbeauty' ); ?></h3>
					<p><?php esc_html_e( 'Send us the products you\'re eyeing and what your skin has reacted to before. We\'ll tell you honestly which to try first — and which to skip entirely. That advice has talked people out of purchases. Good.', 'glow-kbeauty' ); ?></p>
					<a class="btn btn-solid" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Ask a person', 'glow-kbeauty' ); ?></a>
				</div>
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
