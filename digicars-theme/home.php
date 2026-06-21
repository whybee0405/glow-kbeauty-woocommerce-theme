<?php
/**
 * Car Torque — blog landing.
 *
 * Assigned as the Posts page under Settings → Reading. Renders the editorial
 * header, then a 3-up grid of post cards via digicars_post_card(), with
 * pagination and an empty state.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main">
	<section class="section">
		<div class="container">
			<header class="stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'Car Torque', 'digicars' ); ?></p>
				<h1 class="t-1"><?php esc_html_e( 'Car buying, finance and the road ahead.', 'digicars' ); ?></h1>
				<p class="t-lead muted"><?php esc_html_e( 'Honest advice on financing your next car, model launches worth knowing about, and what it really costs to own a car in South Africa.', 'digicars' ); ?></p>
			</header>

			<?php if ( have_posts() ) : ?>
				<ul class="post-list grid" style="--cols:3;">
					<?php
					while ( have_posts() ) :
						the_post();
						digicars_post_card();
					endwhile;
					?>
				</ul>

				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => esc_html__( 'Previous', 'digicars' ),
						'next_text' => esc_html__( 'Next', 'digicars' ),
					)
				);
				?>
			<?php else : ?>
				<div class="stack-sm" style="margin-top: var(--s-6);">
					<p class="t-lead muted"><?php esc_html_e( 'No articles have been published yet. New stories are on the way — in the meantime, browse the cars.', 'digicars' ); ?></p>
					<a class="btn btn--signal" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back home', 'digicars' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
