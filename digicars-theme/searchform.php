<?php
/**
 * Branded search form.
 *
 * Used by the header search panel and the 404 page. A clean GET form to the
 * site root with an accessible (sr-only) label, reusing .field / .input.
 *
 * @package digicars
 */

defined( 'ABSPATH' ) || exit;

$digicars_search_id = 'digicars-search-' . uniqid();
?>
<form role="search" method="get" class="search-form field" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="<?php echo esc_attr( $digicars_search_id ); ?>"><?php esc_html_e( 'Search cars and articles', 'digicars' ); ?></label>
	<div class="search-form__row cluster">
		<input
			type="search"
			id="<?php echo esc_attr( $digicars_search_id ); ?>"
			class="input search-form__input"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'Search cars and articles', 'digicars' ); ?>"
			autocomplete="off"
		/>
		<button type="submit" class="btn btn--signal btn--sm">
			<?php esc_html_e( 'Search', 'digicars' ); ?>
		</button>
	</div>
</form>
