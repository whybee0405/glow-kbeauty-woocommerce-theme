<?php
/**
 * Single product — the journey: orient (breadcrumb, step badge) →
 * assess (brand, title, rating, actives, skin fit) → commit (sticky
 * buy panel) → reassure (accordions) → continue (next routine step).
 *
 * @package Glow_KBeauty
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	global $product;

	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_ID() );
	}

	$glow_id      = $product->get_id();
	$glow_brand   = glow_meta( $glow_id, '_product_brand' );
	$glow_step_no = (int) glow_meta( $glow_id, '_product_routine_step' );
	$glow_actives = glow_meta( $glow_id, '_key_ingredients' );
	$glow_fits    = glow_meta( $glow_id, '_skin_types' );
	$glow_vegan   = 'yes' === glow_meta( $glow_id, '_is_vegan' );
	$glow_cf      = 'yes' === glow_meta( $glow_id, '_is_cruelty_free' );
	$glow_steps   = glow_routine_steps();
	$glow_step    = ( $glow_step_no >= 1 && $glow_step_no <= 7 ) ? $glow_steps[ $glow_step_no - 1 ] : null;

	$glow_gallery_ids = $product->get_gallery_image_ids();
	?>

	<main id="main">
		<div class="container">

			<?php woocommerce_breadcrumb(); ?>
			<?php wc_print_notices(); ?>

			<div class="pdp-grid">

				<!-- Gallery -->
				<div class="pdp-gallery">
					<?php $glow_has_image = has_post_thumbnail( $glow_id ); ?>
					<div class="pdp-3d-wrap" id="pdp-3d-wrap" data-has-image="<?php echo $glow_has_image ? 'true' : 'false'; ?>">
						<?php if ( $glow_has_image ) : ?>
							<figure class="pdp-main-image" data-pdp-main>
								<?php glow_product_image( $product, 'woocommerce_single' ); ?>
							</figure>
							<button class="btn-view-3d" id="glow-view-3d-btn" type="button">
								<?php esc_html_e( 'View 3D', 'glow-kbeauty' ); ?>
							</button>
						<?php endif; ?>
						<canvas id="glow-pdp-3d" aria-hidden="true" role="presentation"
							<?php echo $glow_has_image ? 'hidden' : ''; ?>></canvas>
					</div>

					<?php if ( $glow_gallery_ids ) : ?>
						<div class="pdp-thumbs" role="group" aria-label="<?php esc_attr_e( 'Product images', 'glow-kbeauty' ); ?>">
							<?php if ( $product->get_image_id() ) : ?>
								<button class="pdp-thumb is-active" type="button" data-full="<?php echo esc_url( wp_get_attachment_image_url( $product->get_image_id(), 'large' ) ); ?>">
									<?php echo wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ); ?>
								</button>
							<?php endif; ?>
							<?php foreach ( $glow_gallery_ids as $glow_gid ) : ?>
								<button class="pdp-thumb" type="button" data-full="<?php echo esc_url( wp_get_attachment_image_url( $glow_gid, 'large' ) ); ?>">
									<?php echo wp_get_attachment_image( $glow_gid, 'thumbnail' ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Assess + commit -->
				<div class="pdp-info">

					<?php if ( $glow_step ) : ?>
						<a class="step-badge mono" href="<?php echo esc_url( glow_step_url( $glow_step['slug'] ) ); ?>">
							STEP <?php echo esc_html( $glow_step['no'] ); ?> · <?php echo esc_html( $glow_step['name'] ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $glow_brand ) : ?>
						<p class="pdp-brand mono"><?php echo esc_html( $glow_brand ); ?></p>
					<?php endif; ?>

					<h1 class="t-1 pdp-title"><?php the_title(); ?></h1>

					<div class="pdp-rating-row">
						<?php if ( $product->get_review_count() > 0 ) : ?>
							<?php glow_stars( (float) $product->get_average_rating() ); ?>
							<a class="mono" href="#reviews"><?php echo esc_html( sprintf( /* translators: %s: review count. */ _n( '%s review', '%s reviews', $product->get_review_count(), 'glow-kbeauty' ), number_format_i18n( $product->get_review_count() ) ) ); ?></a>
						<?php endif; ?>
						<?php if ( $product->get_sku() ) : ?>
							<span class="mono pdp-sku"><?php echo esc_html( $product->get_sku() ); ?></span>
						<?php endif; ?>
					</div>

					<p class="pdp-price mono"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>

					<?php if ( $product->get_short_description() ) : ?>
						<div class="pdp-callout">
							<?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $glow_actives ) : ?>
						<div class="pdp-chips">
							<h2 class="filter-label mono"><?php esc_html_e( 'Actives', 'glow-kbeauty' ); ?></h2>
							<ul class="chip-list">
								<?php foreach ( array_map( 'trim', explode( ',', $glow_actives ) ) as $glow_active ) : ?>
									<li class="chip"><?php echo esc_html( $glow_active ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php if ( $glow_fits ) : ?>
						<div class="pdp-chips">
							<h2 class="filter-label mono"><?php esc_html_e( 'Skin fit', 'glow-kbeauty' ); ?></h2>
							<ul class="chip-list">
								<?php foreach ( array_map( 'trim', explode( ',', $glow_fits ) ) as $glow_fit ) : ?>
									<li class="chip"><?php echo esc_html( $glow_fit ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php if ( $glow_vegan || $glow_cf ) : ?>
						<p class="pdp-marks mono">
							<?php if ( $glow_vegan ) : ?><span>✓ <?php esc_html_e( 'Vegan', 'glow-kbeauty' ); ?></span><?php endif; ?>
							<?php if ( $glow_cf ) : ?><span>✓ <?php esc_html_e( 'Cruelty-free', 'glow-kbeauty' ); ?></span><?php endif; ?>
						</p>
					<?php endif; ?>

					<div class="pdp-buy-panel">
						<?php woocommerce_template_single_add_to_cart(); ?>

						<ul class="assurance">
							<li><?php esc_html_e( 'Ships from Joburg within one working day', 'glow-kbeauty' ); ?></li>
							<li><?php esc_html_e( 'Free delivery over R500, tracked everywhere', 'glow-kbeauty' ); ?></li>
							<li><?php esc_html_e( 'Reacted to it? We refund opened products', 'glow-kbeauty' ); ?></li>
						</ul>
					</div>

				</div>
			</div>

			<!-- Reassure: accordions, not tabs — scannable in one scroll -->
			<div class="pdp-accordions">

				<details class="faq-item" open>
					<summary><?php esc_html_e( 'About this product', 'glow-kbeauty' ); ?></summary>
					<div class="faq-body prose">
						<?php
						$glow_description = $product->get_description();
						echo $glow_description ? wp_kses_post( wpautop( $glow_description ) ) : '<p>' . esc_html__( 'Details are on their way for this one — the actives and skin-fit chips above are the short version.', 'glow-kbeauty' ) . '</p>';
						?>
					</div>
				</details>

				<?php if ( $glow_step ) : ?>
					<details class="faq-item">
						<summary><?php esc_html_e( 'How it fits your routine', 'glow-kbeauty' ); ?></summary>
						<div class="faq-body">
							<p>
								<?php
								echo esc_html(
									sprintf(
										/* translators: 1: step number, 2: step name. */
										__( 'This is a step %1$s product — %2$s. Apply it in this position, thinnest texture to thickest:', 'glow-kbeauty' ),
										(int) $glow_step_no,
										$glow_step['name']
									)
								);
								?>
							</p>
							<ol class="routine-mini">
								<?php foreach ( $glow_steps as $glow_i => $glow_s ) : ?>
									<li class="<?php echo ( $glow_i + 1 ) === $glow_step_no ? 'is-current' : ''; ?>">
										<span class="mono"><?php echo esc_html( $glow_s['no'] ); ?></span>
										<?php echo esc_html( $glow_s['name'] ); ?>
										<?php if ( ( $glow_i + 1 ) === $glow_step_no ) : ?>
											<span class="mono you-are-here"><?php esc_html_e( '← this product', 'glow-kbeauty' ); ?></span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ol>
						</div>
					</details>
				<?php endif; ?>

				<details class="faq-item">
					<summary><?php esc_html_e( 'Full ingredient disclosure', 'glow-kbeauty' ); ?></summary>
					<div class="faq-body">
						<?php if ( $glow_actives ) : ?>
							<p><strong><?php esc_html_e( 'Key actives:', 'glow-kbeauty' ); ?></strong> <?php echo esc_html( $glow_actives ); ?>.</p>
						<?php endif; ?>
						<p><?php esc_html_e( 'The complete INCI list is printed on the carton and inner label of every unit, exactly as registered in Korea — we don\'t relabel or repackage. Want the list before you buy, or the documentation for a specific batch? Email us the product name and we\'ll send both.', 'glow-kbeauty' ); ?></p>
						<p><?php esc_html_e( 'New to this product? Patch test first: inside of the forearm, once a day for three days, before it goes near your face.', 'glow-kbeauty' ); ?></p>
					</div>
				</details>

				<details class="faq-item" id="reviews">
					<summary>
						<?php
						$glow_rc = $product->get_review_count();
						echo esc_html(
							$glow_rc > 0
								? sprintf( /* translators: %s: review count. */ _n( 'Reviews (%s)', 'Reviews (%s)', $glow_rc, 'glow-kbeauty' ), number_format_i18n( $glow_rc ) )
								: __( 'Reviews', 'glow-kbeauty' )
						);
						?>
					</summary>
					<div class="faq-body">
						<?php comments_template(); ?>
					</div>
				</details>

			</div>

			<!-- Continue the routine -->
			<?php
			$glow_next_products = array();

			if ( $glow_step_no >= 1 && $glow_step_no < 7 ) {
				$glow_next_step     = $glow_steps[ $glow_step_no ]; // The following step.
				$glow_next_products = wc_get_products(
					array(
						'status'   => 'publish',
						'limit'    => 4,
						'category' => array( $glow_next_step['slug'] ),
						'exclude'  => array( $glow_id ),
					)
				);
			}

			if ( count( $glow_next_products ) < 4 ) {
				$glow_related_ids = wc_get_related_products( $glow_id, 4 - count( $glow_next_products ), wp_list_pluck( $glow_next_products, 'id' ) );
				foreach ( $glow_related_ids as $glow_rid ) {
					$glow_rel = wc_get_product( $glow_rid );
					if ( $glow_rel ) {
						$glow_next_products[] = $glow_rel;
					}
				}
			}
			?>

			<?php if ( $glow_next_products ) : ?>
				<section class="section section-tight pdp-continue">
					<div class="section-head">
						<div>
							<p class="eyebrow"><?php esc_html_e( 'What comes next', 'glow-kbeauty' ); ?></p>
							<h2 class="t-1"><?php esc_html_e( 'Continue the routine', 'glow-kbeauty' ); ?></h2>
						</div>
					</div>
					<ul class="products grid-4">
						<?php
						foreach ( $glow_next_products as $glow_next_product ) {
							$post_object = get_post( $glow_next_product->get_id() );
							setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
							wc_get_template_part( 'content', 'product' );
						}
						wp_reset_postdata();
						?>
					</ul>
				</section>
			<?php endif; ?>

		</div>
	</main>

	<?php
endwhile;

get_footer();
