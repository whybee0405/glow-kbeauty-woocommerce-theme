<?php
/**
 * Default page template — clean editorial fallback. WooCommerce
 * pages (cart, checkout, account) render through this too.
 *
 * @package Glow_KBeauty
 */

get_header();
?>

<main id="main">
	<div class="container">

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<header class="page-hero">
				<h1 class="t-1"><?php the_title(); ?></h1>
			</header>

			<div class="prose page-content">
				<?php the_content(); ?>
			</div>
			<?php
		endwhile;
		?>

	</div>
</main>

<?php get_footer(); ?>
