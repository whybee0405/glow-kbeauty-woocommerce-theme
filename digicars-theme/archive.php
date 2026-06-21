<?php
/**
 * Car Torque archive — category / tag / date / author listings.
 *
 * Dynamic header (the bare term/archive title, no "Category:" prefix), an
 * optional description, then a 3-up grid of post cards via digicars_post_card()
 * with pagination and an empty state.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

get_header();

/*
 * single_term_title() prints the bare term name on a taxonomy archive. For
 * date / author / other archives, strip the "Label:" prefix that
 * get_the_archive_title() prepends.
 */
$digicars_title = get_the_archive_title();
if ( false !== strpos( $digicars_title, ':' ) ) {
	$digicars_title = trim( substr( $digicars_title, strpos( $digicars_title, ':' ) + 1 ) );
}
?>

<main id="main" class="site-main">
	<section class="section">
		<div class="container">
			<header class="stack-sm">
				<p class="eyebrow"><?php esc_html_e( 'Car Torque', 'digicars' ); ?></p>
				<h1 class="t-1"><?php echo esc_html( $digicars_title ); ?></h1>
				<?php if ( get_the_archive_description() ) : ?>
					<div class="t-lead muted"><?php the_archive_description(); ?></div>
				<?php endif; ?>
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
					<p class="t-lead muted"><?php esc_html_e( 'Nothing here yet. Try another topic or head back to Car Torque.', 'digicars' ); ?></p>
					<a class="btn btn--signal" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back home', 'digicars' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
