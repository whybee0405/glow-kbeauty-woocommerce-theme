<?php
/**
 * Generic page fallback — clean editorial single-column layout.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main">
	<section class="section">
		<div class="container">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'page-article' ); ?>>
					<header class="stack-sm">
						<p class="eyebrow"><?php esc_html_e( 'Digicars', 'digicars' ); ?></p>
						<h1 class="t-1"><?php the_title(); ?></h1>
					</header>
					<div class="prose">
						<?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<p class="t-mono muted">' . esc_html__( 'Pages:', 'digicars' ) . ' ',
								'after'  => '</p>',
							)
						);
						?>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>
	</section>
</main>

<?php
get_footer();
