<?php
/**
 * Search results. Product searches get the full shop experience
 * (grid, filters, rail); everything else falls back to the index
 * listing.
 *
 * @package Glow_KBeauty
 */

if ( glow_wc_active() && 'product' === get_query_var( 'post_type' ) ) {
	require locate_template( 'archive-product.php' );
	return;
}

require locate_template( 'index.php' );
