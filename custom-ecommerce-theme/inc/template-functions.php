<?php
/**
 * Custom template helper functions, shortcodes, and hooks
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Shim for sites older than WordPress 5.2.
	 *
	 * @return void
	 */
	function wp_body_open(): void {
		do_action( 'wp_body_open' );
	}
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array<int, string> $classes Classes for the body element.
 * @return array<int, string>
 */
function cet_body_classes( array $classes ): array {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	$classes[] = 'cet-theme-body';

	if ( class_exists( 'WooCommerce' ) ) {
		$classes[] = 'cet-wc-active';
	} else {
		$classes[] = 'cet-wc-inactive';
	}

	if ( function_exists( 'cet_get_option' ) && cet_get_option( 'sticky_header_enable', true ) ) {
		$classes[] = 'cet-has-sticky-header';
	}

	$shop_layout = function_exists( 'cet_get_option' ) ? (string) cet_get_option( 'shop_sidebar_layout', 'left-sidebar' ) : 'left-sidebar';
	$classes[]   = 'cet-shop-layout-' . sanitize_html_class( $shop_layout );

	return $classes;
}
add_filter( 'body_class', 'cet_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 *
 * @return void
 */
function cet_pingback_header(): void {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'cet_pingback_header' );

/**
 * Calculate estimated reading time in minutes for a post.
 *
 * @param int|null $post_id Optional post ID.
 * @return int
 */
function cet_get_reading_time( ?int $post_id = null ): int {
	$content    = get_post_field( 'post_content', $post_id ?: get_the_ID() );
	$word_count = str_word_count( wp_strip_all_tags( (string) $content ) );
	$minutes    = (int) ceil( $word_count / 200 );
	return max( 1, $minutes );
}

/**
 * Render Wishlist Shortcode `[cet_wishlist]` for dedicated Wishlist pages.
 *
 * @return string
 */
function cet_wishlist_shortcode(): string {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return '<p>' . esc_html__( 'WooCommerce is required to view the wishlist.', 'custom-ecommerce-theme' ) . '</p>';
	}

	$ids = function_exists( 'cet_get_wishlist_ids' ) ? cet_get_wishlist_ids() : array();

	ob_start();
	if ( empty( $ids ) ) {
		?>
		<div class="cet-empty-state">
			<h3><?php esc_html_e( 'Your Wishlist is Currently Empty', 'custom-ecommerce-theme' ); ?></h3>
			<p><?php esc_html_e( 'Save your favorite pieces by clicking the heart icon on any product card.', 'custom-ecommerce-theme' ); ?></p>
			<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>" class="cet-btn cet-btn--gold">
				<?php esc_html_e( 'Explore the Shop', 'custom-ecommerce-theme' ); ?>
			</a>
		</div>
		<?php
	} else {
		echo '<div class="cet-products-grid cet-grid-cols-4">';
		foreach ( $ids as $product_id ) {
			$product_obj = wc_get_product( $product_id );
			if ( $product_obj && $product_obj->is_visible() ) {
				set_query_var( 'cet_card_product', $product_obj );
				get_template_part( 'template-parts/product/product-card' );
			}
		}
		echo '</div>';
	}
	return (string) ob_get_clean();
}
add_shortcode( 'cet_wishlist', 'cet_wishlist_shortcode' );
