<?php
/**
 * Template Name: About
 *
 * The trust page — origin story, sourcing principles, supply line.
 * Written for the gift shopper and the sceptic in equal measure.
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
			<p class="eyebrow"><?php esc_html_e( 'Who we are', 'glow-kbeauty' ); ?></p>
			<h1 class="t-hero"><?php echo wp_kses( __( 'We read the back of the bottle <em>so you don\'t have to.</em>', 'glow-kbeauty' ), array( 'em' => array() ) ); ?></h1>
		</header>
	</div>

	<section class="section-tight">
		<div class="container">
			<div class="prose" data-reveal>
				<p class="lead"><?php esc_html_e( 'Glow started in a Johannesburg flat with a spreadsheet of INCI lists and a growing irritation: half the “K-beauty” sold in South Africa was expired, heat-damaged, or not Korean at all.', 'glow-kbeauty' ); ?></p>
				<p><?php esc_html_e( 'So we did it the slow way. We built direct relationships with brands and licensed distributors in Seoul — negotiated in Korean, not through a middleman\'s middleman. We fly stock in small, frequent batches instead of letting it cook in a sea container for six weeks. And we log the batch number of every unit that leaves our shelf, so when you ask “is this real and is it fresh?”, the answer is paperwork, not promises.', 'glow-kbeauty' ); ?></p>
				<p><?php esc_html_e( 'We\'re a small team. One of us has reactive skin and vetoes anything we can\'t fully disclose. One of us answers the WhatsApp line personally. None of us will sell you a 10-step routine when three steps would do.', 'glow-kbeauty' ); ?></p>
			</div>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="section-head" data-reveal>
				<div>
					<p class="eyebrow"><?php esc_html_e( 'How we operate', 'glow-kbeauty' ); ?></p>
					<h2 class="t-1"><?php esc_html_e( 'Four principles, no asterisks', 'glow-kbeauty' ); ?></h2>
				</div>
			</div>

			<ol class="principles" data-reveal>
				<li class="principle">
					<span class="mono">01</span>
					<h3><?php esc_html_e( 'Direct sourcing only', 'glow-kbeauty' ); ?></h3>
					<p><?php esc_html_e( 'Brand or licensed distributor — nothing else. If we can\'t verify the chain back to the factory, we don\'t stock it, however good the margin looks.', 'glow-kbeauty' ); ?></p>
				</li>
				<li class="principle">
					<span class="mono">02</span>
					<h3><?php esc_html_e( 'Batch transparency', 'glow-kbeauty' ); ?></h3>
					<p><?php esc_html_e( 'Every unit\'s batch number is logged before it\'s shelved. Ask for the documentation on anything you\'ve bought and we\'ll send it — manufacture date included.', 'glow-kbeauty' ); ?></p>
				</li>
				<li class="principle">
					<span class="mono">03</span>
					<h3><?php esc_html_e( 'Full disclosure', 'glow-kbeauty' ); ?></h3>
					<p><?php esc_html_e( 'Key actives listed on every product page, complete INCI on every label, and straight answers about fragrance, alcohol and anything else your skin has opinions about.', 'glow-kbeauty' ); ?></p>
				</li>
				<li class="principle">
					<span class="mono">04</span>
					<h3><?php esc_html_e( 'Routine over revenue', 'glow-kbeauty' ); ?></h3>
					<p><?php esc_html_e( 'We\'d rather you buy three products that work in sequence than seven that fight each other. The store is organised by step for exactly this reason.', 'glow-kbeauty' ); ?></p>
				</li>
			</ol>
		</div>
	</section>

	<section class="section section-tight">
		<div class="container">
			<div class="sourcing-split" data-reveal>
				<div class="sourcing-media">
					<ul class="supply-line">
						<li><span class="mono"><?php esc_html_e( 'Week 0', 'glow-kbeauty' ); ?></span> <?php esc_html_e( 'Order placed with the brand in Seoul, in Korean', 'glow-kbeauty' ); ?></li>
						<li><span class="mono"><?php esc_html_e( 'Week 1', 'glow-kbeauty' ); ?></span> <?php esc_html_e( 'Air freight to OR Tambo — small batches, no sea containers', 'glow-kbeauty' ); ?></li>
						<li><span class="mono"><?php esc_html_e( 'Week 2', 'glow-kbeauty' ); ?></span> <?php esc_html_e( 'Batch numbers logged and cross-checked at our Joburg studio', 'glow-kbeauty' ); ?></li>
						<li><span class="mono"><?php esc_html_e( 'Same week', 'glow-kbeauty' ); ?></span> <?php esc_html_e( 'On the site, in date, on its way to you', 'glow-kbeauty' ); ?></li>
					</ul>
				</div>
				<div class="sourcing-body">
					<p class="eyebrow"><?php esc_html_e( 'The supply line', 'glow-kbeauty' ); ?></p>
					<h2 class="t-1"><?php esc_html_e( 'Seoul to Joburg in under three weeks', 'glow-kbeauty' ); ?></h2>
					<p class="lead"><?php esc_html_e( 'Freshness is most of the battle with actives like vitamin C and probiotics. Short, frequent supply runs mean what you buy was made recently — not discovered in a warehouse.', 'glow-kbeauty' ); ?></p>
					<a class="btn btn-solid" href="<?php echo esc_url( glow_wc_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'Shop the current batch', 'glow-kbeauty' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<?php
	// Surface any editor content added to the page itself.
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
