<?php
/**
 * Ultimate fallback / blog list. A dedicated home.php styles the Car Torque
 * blog; this template stands alone for any other post listing.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main">
	<section class="section">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<p class="eyebrow"><?php esc_html_e( 'Car Torque', 'digicars' ); ?></p>
				<h1 class="t-1"><?php esc_html_e( 'Latest from Digicars', 'digicars' ); ?></h1>

				<ul class="post-list grid" style="--cols:3;">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<li class="post-card">
							<?php if ( has_post_thumbnail() ) : ?>
								<a class="post-card__media" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'digicars-card' ); ?></a>
							<?php endif; ?>
							<div class="post-card__body stack-sm">
								<?php
								$digicars_cats = get_the_category();
								if ( ! empty( $digicars_cats ) ) :
									?>
									<span class="eyebrow"><?php echo esc_html( $digicars_cats[0]->name ); ?></span>
								<?php endif; ?>
								<h2 class="t-3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="muted t-mono"><?php echo esc_html( get_the_date() ); ?></p>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
								<a class="link-arrow" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read', 'digicars' ); ?></a>
							</div>
						</li>
						<?php
					endwhile;
					?>
				</ul>

				<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
			<?php else : ?>
				<p class="eyebrow"><?php esc_html_e( 'Car Torque', 'digicars' ); ?></p>
				<h1 class="t-1"><?php esc_html_e( 'Nothing here yet.', 'digicars' ); ?></h1>
				<p class="t-lead muted"><?php esc_html_e( 'New articles are on the way. In the meantime, browse the cars.', 'digicars' ); ?></p>
				<a class="btn btn--signal" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back home', 'digicars' ); ?></a>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
