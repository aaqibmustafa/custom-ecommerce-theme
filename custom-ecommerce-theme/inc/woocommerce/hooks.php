<?php
/**
 * WooCommerce Custom Hooks, Template Filters, Cart Fragments & Product Enhancements
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unhook default WooCommerce wrappers and replace with Custom E-Commerce Theme wrappers.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * Output opening wrapper for WooCommerce archives and single products.
 *
 * @return void
 */
function cet_woocommerce_wrapper_before(): void {
	if ( function_exists( 'cet_breadcrumbs' ) ) {
		cet_breadcrumbs();
	}
	?>
	<main id="primary" class="site-main cet-main cet-woocommerce-main">
		<div class="cet-container cet-wc-container">
	<?php
}
add_action( 'woocommerce_before_main_content', 'cet_woocommerce_wrapper_before', 10 );

/**
 * Output closing wrapper for WooCommerce archives and single products.
 *
 * @return void
 */
function cet_woocommerce_wrapper_after(): void {
	?>
		</div><!-- .cet-wc-container -->
	</main><!-- #primary -->
	<?php
}
add_action( 'woocommerce_after_main_content', 'cet_woocommerce_wrapper_after', 10 );

/**
 * Filter number of products per row based on Customizer or query parameter.
 *
 * @return int
 */
function cet_loop_shop_columns(): int {
	if ( isset( $_GET['cols'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cols = absint( $_GET['cols'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $cols >= 2 && $cols <= 5 ) {
			return $cols;
		}
	}
	$default = absint( cet_get_option( 'shop_default_columns', 4 ) );
	return ( $default >= 2 && $default <= 5 ) ? $default : 4;
}
add_filter( 'loop_shop_columns', 'cet_loop_shop_columns', 20 );

/**
 * Filter number of products displayed per page.
 *
 * @return int
 */
function cet_loop_shop_per_page(): int {
	$per_page = absint( cet_get_option( 'shop_products_per_page', 12 ) );
	return $per_page > 0 ? $per_page : 12;
}
add_filter( 'loop_shop_per_page', 'cet_loop_shop_per_page', 20 );

/**
 * Update Header Cart Count & Mini-Cart Drawer dynamically via WooCommerce AJAX Cart Fragments.
 *
 * @param array<string, string> $fragments Cart fragments array.
 * @return array<string, string>
 */
function cet_woocommerce_header_add_to_cart_fragment( array $fragments ): array {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $fragments;
	}

	$count = WC()->cart->get_cart_contents_count();

	$fragments['span.cet-cart-count'] = sprintf(
		'<span class="cet-action-badge cet-cart-count" aria-live="polite">%d</span>',
		absint( $count )
	);

	ob_start();
	?>
	<div class="cet-mini-cart-body">
		<?php cet_render_free_shipping_progress_bar(); ?>
		<div class="widget_shopping_cart_content">
			<?php woocommerce_mini_cart(); ?>
		</div>
	</div>
	<?php
	$fragments['div.cet-mini-cart-body'] = (string) ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'cet_woocommerce_header_add_to_cart_fragment' );

/**
 * Render Free Shipping Progress Bar in Mini Cart and Cart Page.
 *
 * @return void
 */
function cet_render_free_shipping_progress_bar(): void {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$threshold = (float) cet_get_option( 'free_shipping_threshold', 150 );
	if ( $threshold <= 0 ) {
		return;
	}

	$subtotal   = (float) WC()->cart->get_displayed_subtotal();
	$percentage = min( 100, round( ( $subtotal / $threshold ) * 100 ) );
	$remaining  = max( 0, $threshold - $subtotal );
	?>
	<div class="cet-shipping-goal">
		<div class="cet-shipping-goal__text">
			<?php if ( $remaining > 0 ) : ?>
				<?php
				printf(
					/* translators: %s: Remaining amount formatted as price. */
					esc_html__( 'Add %s more to unlock Complimentary Express Shipping!', 'custom-ecommerce-theme' ),
					wp_kses_post( wc_price( $remaining ) )
				);
				?>
			<?php else : ?>
				<span class="cet-shipping-goal__unlocked">
					<?php echo cet_get_svg_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Congratulations! You have unlocked Complimentary Express Shipping.', 'custom-ecommerce-theme' ); ?>
				</span>
			<?php endif; ?>
		</div>
		<div class="cet-shipping-goal__track" role="progressbar" aria-valuenow="<?php echo esc_attr( (string) $percentage ); ?>" aria-valuemin="0" aria-valuemax="100">
			<div class="cet-shipping-goal__fill" style="width: <?php echo esc_attr( (string) $percentage ); ?>%;"></div>
		</div>
	</div>
	<?php
}

/**
 * Output compatible "Buy Now" instant checkout button, Wishlist, Compare & Share controls on Single Product.
 *
 * @return void
 */
function cet_single_product_extra_actions(): void {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$product_id   = $product->get_id();
	$wishlist_ids = function_exists( 'cet_get_wishlist_ids' ) ? cet_get_wishlist_ids() : array();
	$in_wishlist  = in_array( $product_id, $wishlist_ids, true );
	$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
	?>
	<div class="cet-single-product-utilities">
		<?php if ( cet_get_option( 'single_product_buy_now', true ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_type( 'external' ) ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'add-to-cart', $product_id, $checkout_url ) ); ?>" class="cet-btn cet-btn--primary cet-buy-now-btn">
				<?php esc_html_e( 'Buy It Now — Instant Checkout', 'custom-ecommerce-theme' ); ?>
			</a>
		<?php endif; ?>

		<div class="cet-single-meta-buttons">
			<button type="button" class="cet-util-btn cet-wishlist-toggle <?php echo $in_wishlist ? 'is-active' : ''; ?>" data-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
				<?php echo cet_get_svg_icon( 'heart', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php echo $in_wishlist ? esc_html__( 'Saved in Wishlist', 'custom-ecommerce-theme' ) : esc_html__( 'Add to Wishlist', 'custom-ecommerce-theme' ); ?></span>
			</button>

			<button type="button" class="cet-util-btn cet-compare-toggle" data-product-id="<?php echo esc_attr( (string) $product_id ); ?>" data-product-title="<?php echo esc_attr( $product->get_name() ); ?>">
				<?php echo cet_get_svg_icon( 'compare', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'Compare', 'custom-ecommerce-theme' ); ?></span>
			</button>
		</div>
	</div>

	<div class="cet-product-trust-box">
		<div class="cet-trust-row">
			<?php echo cet_get_svg_icon( 'truck', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'Complimentary insured express delivery on orders over $150.', 'custom-ecommerce-theme' ); ?></span>
		</div>
		<div class="cet-trust-row">
			<?php echo cet_get_svg_icon( 'shield', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( '100% Authenticity Guarantee & 2-Year Atelier Warranty.', 'custom-ecommerce-theme' ); ?></span>
		</div>
		<div class="cet-trust-row">
			<?php echo cet_get_svg_icon( 'refresh', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( '30-Day effortless returns & complimentary exchanges.', 'custom-ecommerce-theme' ); ?></span>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_form', 'cet_single_product_extra_actions', 15 );

/**
 * Configure Related Products args (4 products in 4 columns).
 *
 * @param array<string, mixed> $args Related products arguments.
 * @return array<string, mixed>
 */
function cet_related_products_args( array $args ): array {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'cet_related_products_args', 20 );
