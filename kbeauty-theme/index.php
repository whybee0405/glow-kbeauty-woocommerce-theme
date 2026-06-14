<?php
/**
 * Final fallback template — blog index, generic archives, search.
 *
 * @package Glow_KBeauty
 */

get_header();
?>

<main id="main">
	<div class="container">

		<header class="page-hero">
			<?php if ( is_search() ) : ?>
				<h1 class="t-1">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: search query. */
							__( 'Results for “%s”', 'glow-kbeauty' ),
							get_search_query()
						)
					);
					?>
				</h1>
			<?php elseif ( is_archive() ) : ?>
				<h1 class="t-1"><?php the_archive_title(); ?></h1>
				<?php the_archive_description( '<p class="lead">', '</p>' ); ?>
			<?php elseif ( is_home() ) : ?>
				<h1 class="t-1"><?php esc_html_e( 'Journal', 'glow-kbeauty' ); ?></h1>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'post-list-item' ); ?>>
						<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
						<h2 class="t-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="prose"><?php the_excerpt(); ?></div>
						<a class="link-arrow" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read on', 'glow-kbeauty' ); ?> <span class="arrow" aria-hidden="true">→</span></a>
					</article>
					<hr />
				<?php endwhile; ?>
			</div>

			<nav class="shop-pagination" aria-label="<?php esc_attr_e( 'Pagination', 'glow-kbeauty' ); ?>">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => __( '← Previous', 'glow-kbeauty' ),
						'next_text' => __( 'Next →', 'glow-kbeauty' ),
					)
				);
				?>
			</nav>

		<?php else : ?>

			<div class="shop-empty">
				<h2 class="t-2"><?php esc_html_e( 'Nothing here matches that.', 'glow-kbeauty' ); ?></h2>
				<p><?php esc_html_e( 'Try a different word, or search products by ingredient — “niacinamide” and “centella” both work.', 'glow-kbeauty' ); ?></p>
				<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form-row error-search">
					<label class="screen-reader-text" for="glow-index-search"><?php esc_html_e( 'Search', 'glow-kbeauty' ); ?></label>
					<input type="search" id="glow-index-search" name="s" placeholder="<?php esc_attr_e( 'Search again', 'glow-kbeauty' ); ?>" />
					<input type="hidden" name="post_type" value="product" />
					<button class="btn btn-solid" type="submit"><?php esc_html_e( 'Search', 'glow-kbeauty' ); ?></button>
				</form>
			</div>

		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
