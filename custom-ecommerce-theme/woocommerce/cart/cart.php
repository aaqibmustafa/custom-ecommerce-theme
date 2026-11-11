<?php
/**
 * Cart Page Template
 *
 * Two-column luxury Cart presentation with Free Shipping progress bar and sticky Cart Totals.
 *
 * @package Custom_Ecommerce_Theme
 * @version 7.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_cart' );
?>

<div class="cet-cart-layout">
	<div class="cet-cart-main-panel">
		<?php
		if ( function_exists( 'cet_render_free_shipping_progress_bar' ) ) {
			cet_render_free_shipping_progress_bar();
		}
		?>

		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
				<thead>
					<tr>
						<th class="product-remove"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'custom-ecommerce-theme' ); ?></span></th>
						<th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail image', 'custom-ecommerce-theme' ); ?></span></th>
						<th class="product-name"><?php esc_html_e( 'Product', 'custom-ecommerce-theme' ); ?></th>
						<th class="product-price"><?php esc_html_e( 'Price', 'custom-ecommerce-theme' ); ?></th>
						<th class="product-quantity"><?php esc_html_e( 'Quantity', 'custom-ecommerce-theme' ); ?></th>
						<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'custom-ecommerce-theme' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
						$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
								<td class="product-remove">
									<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											/* translators: %s is the product name */
											esc_attr( sprintf( __( 'Remove %s from cart', 'custom-ecommerce-theme' ), wp_strip_all_tags( $product_name ) ) ),
											esc_attr( (string) $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$cart_item_key
									);
									?>
								</td>

								<td class="product-thumbnail">
									<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
									if ( ! $product_permalink ) {
										echo wp_kses_post( $thumbnail );
									} else {
										printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
									}
									?>
								</td>

								<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'custom-ecommerce-theme' ); ?>">
									<?php
									if ( ! $product_permalink ) {
										echo wp_kses_post( $product_name . '&nbsp;' );
									} else {
										echo wp_kses_post( sprintf( '<a href="%s" style="font-weight:600;">%s</a>', esc_url( $product_permalink ), $_product->get_name() ) );
									}

									do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
									echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'custom-ecommerce-theme' ) . '</p>', $product_id ) );
									}
									?>
								</td>

								<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'custom-ecommerce-theme' ); ?>">
									<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</td>

								<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'custom-ecommerce-theme' ); ?>">
									<?php
									if ( $_product->is_sold_individually() ) {
										$min_quantity = 1;
										$max_quantity = 1;
									} else {
										$min_quantity = 0;
										$max_quantity = $_product->get_max_purchase_quantity();
									}

									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $max_quantity,
											'min_value'    => $min_quantity,
											'product_name' => $product_name,
										),
										$_product,
										false
									);

									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</td>

								<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'custom-ecommerce-theme' ); ?>">
									<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</td>
							</tr>
							<?php
						}
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>

					<tr>
						<td colspan="6" class="actions" style="padding-top:1.5rem;">
							<div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem;">
								<?php if ( wc_coupons_enabled() ) : ?>
									<div class="coupon" style="display:flex; gap:0.5rem;">
										<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'custom-ecommerce-theme' ); ?></label>
										<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Privilege Coupon Code', 'custom-ecommerce-theme' ); ?>" style="width:220px;">
										<button type="submit" class="button cet-btn cet-btn--outline" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'custom-ecommerce-theme' ); ?>">
											<?php esc_html_e( 'Apply Coupon', 'custom-ecommerce-theme' ); ?>
										</button>
										<?php do_action( 'woocommerce_cart_coupon' ); ?>
									</div>
								<?php endif; ?>

								<button type="submit" class="button cet-btn cet-btn--primary" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'custom-ecommerce-theme' ); ?>">
									<?php esc_html_e( 'Update Bag', 'custom-ecommerce-theme' ); ?>
								</button>

								<?php do_action( 'woocommerce_cart_actions' ); ?>
								<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
							</div>
						</td>
					</tr>

					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</tbody>
			</table>
			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>
	</div>

	<div class="cet-cart-summary-panel">
		<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
		<div class="cart-collaterals">
			<?php do_action( 'woocommerce_cart_collaterals' ); ?>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>

