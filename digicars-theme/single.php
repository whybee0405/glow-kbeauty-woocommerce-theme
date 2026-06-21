<?php
/**
 * Single Car Torque article.
 *
 * Single-column editorial read: breadcrumb, category eyebrow, title, byline +
 * date, featured image, prose body, tag chips, prev/next nav and a "Keep
 * reading" block of up to 3 related posts in the same category. No sidebar;
 * comments omitted. BlogPosting schema is emitted by inc/seo.php.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$digicars_id   = get_the_ID();
	$digicars_cats = get_the_category();
	$digicars_blog = get_permalink( (int) get_option( 'page_for_posts' ) );
	if ( ! $digicars_blog ) {
		$digicars_blog = home_url( '/' );
	}
	?>

	<main id="main" class="site-main">
		<article <?php post_class(); ?>>
			<section class="section">
				<div class="container">

					<nav class="article-breadcrumb t-mono muted" aria-label="<?php esc_attr_e( 'Breadcrumb', 'digicars' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'digicars' ); ?></a>
						<span class="breadcrumb-sep" aria-hidden="true">&rsaquo;</span>
						<a href="<?php echo esc_url( $digicars_blog ); ?>"><?php esc_html_e( 'Car Torque', 'digicars' ); ?></a>
						<span class="breadcrumb-sep" aria-hidden="true">&rsaquo;</span>
						<span><?php echo esc_html( get_the_title() ); ?></span>
					</nav>

					<header class="article-header stack-sm">
						<?php if ( ! empty( $digicars_cats ) ) : ?>
							<p class="eyebrow">
								<a href="<?php echo esc_url( get_category_link( $digicars_cats[0]->term_id ) ); ?>"><?php echo esc_html( $digicars_cats[0]->name ); ?></a>
							</p>
						<?php endif; ?>
						<h1 class="t-1"><?php the_title(); ?></h1>
						<p class="muted t-mono article-byline">
							<?php
							printf(
								/* translators: %s: post author name. */
								esc_html__( 'By %s', 'digicars' ),
								esc_html( get_the_author() )
							);
							?>
							<span class="breadcrumb-sep" aria-hidden="true">&middot;</span>
							<?php echo esc_html( get_the_date() ); ?>
						</p>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="article-media">
							<?php the_post_thumbnail( 'large' ); ?>
						</figure>
					<?php endif; ?>

					<div class="prose article-body">
						<?php the_content(); ?>
					</div>

					<?php
					wp_link_pages(
						array(
							'before' => '<nav class="page-links cluster" aria-label="' . esc_attr__( 'Article pages', 'digicars' ) . '"><span class="muted t-mono">' . esc_html__( 'Pages:', 'digicars' ) . '</span>',
							'after'  => '</nav>',
						)
					);

					$digicars_tags = get_the_tags();
					if ( ! empty( $digicars_tags ) && ! is_wp_error( $digicars_tags ) ) :
						?>
						<div class="article-tags cluster" aria-label="<?php esc_attr_e( 'Tags', 'digicars' ); ?>">
							<?php foreach ( $digicars_tags as $digicars_tag ) : ?>
								<a class="chip" href="<?php echo esc_url( get_tag_link( $digicars_tag->term_id ) ); ?>">#<?php echo esc_html( $digicars_tag->name ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php
					$digicars_prev = get_previous_post_link( '%link', '&larr; %title' );
					$digicars_next = get_next_post_link( '%link', '%title &rarr;' );
					if ( $digicars_prev || $digicars_next ) :
						?>
						<nav class="article-nav cluster" aria-label="<?php esc_attr_e( 'More articles', 'digicars' ); ?>">
							<span class="article-nav__prev"><?php echo $digicars_prev; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="article-nav__next"><?php echo $digicars_next; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</nav>
					<?php endif; ?>

				</div>
			</section>

			<?php
			/* ---- Keep reading: up to 3 related posts in the same category. --- */
			if ( ! empty( $digicars_cats ) ) :
				$digicars_related = new WP_Query(
					array(
						'category__in'        => array( (int) $digicars_cats[0]->term_id ),
						'post__not_in'        => array( $digicars_id ),
						'posts_per_page'      => 3,
						'no_found_rows'       => true,
						'ignore_sticky_posts' => true,
					)
				);
				if ( $digicars_related->have_posts() ) :
					?>
					<section class="section surface-soft">
						<div class="container">
							<p class="eyebrow"><?php esc_html_e( 'Keep reading', 'digicars' ); ?></p>
							<ul class="post-list grid" style="--cols:3;">
								<?php
								while ( $digicars_related->have_posts() ) :
									$digicars_related->the_post();
									digicars_post_card();
								endwhile;
								?>
							</ul>
						</div>
					</section>
					<?php
				endif;
				wp_reset_postdata();
			endif;
			?>
		</article>
	</main>

	<?php
endwhile;

get_footer();
