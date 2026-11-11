<?php
/**
 * Custom Accessible Search Form Template (`searchform.php`)
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$unique_id = wp_unique_id( 'cet-search-form-' );
?>
<form role="search" method="get" class="search-form cet-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $unique_id ); ?>">
		<?php esc_html_e( 'Search for:', 'custom-ecommerce-theme' ); ?>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $unique_id ); ?>"
		class="search-field cet-search-form__input"
		placeholder="<?php esc_attr_e( 'Search products & articles...', 'custom-ecommerce-theme' ); ?>"
		value="<?php echo get_search_query(); ?>"
		name="s"
	>
	<button type="submit" class="search-submit cet-search-form__submit" aria-label="<?php esc_attr_e( 'Submit Search', 'custom-ecommerce-theme' ); ?>">
		<?php echo cet_get_svg_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>
</form>
