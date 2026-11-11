<?php
/**
 * Mini-cart template
 *
 * Renders the items inside the off-canvas Mini Cart drawer and WooCommerce cart widget.
 *
 * @package Custom_Ecommerce_Theme
 * @version 7.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>" style="list-style:none; margin:0; padding:0;">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				/**
				 * Filter the product name in the mini-cart.
				 *
				 * @var string $product_name
				 */
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_gallery_thumbnail' ), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
				<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>" style="display:flex; align-items:center; gap:0.9rem; padding:0.85rem 0; border-bottom:1px solid var(--color-border);">
					<div style="width:64px; height:64px; flex-shrink:0; border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--color-border);">
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php echo wp_kses_post( $thumbnail ); ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>">
								<?php echo wp_kses_post( $thumbnail ); ?>
							</a>
						<?php endif; ?>
					</div>

					<div style="flex:1; min-width:0;">
						<h4 style="margin:0 0 0.25rem; font-size:0.92rem; font-family:var(--font-heading);">
							<?php if ( empty( $product_permalink ) ) : ?>
								<?php echo wp_kses_post( $product_name ); ?>
							<?php else : ?>
								<a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo wp_kses_post( $product_name ); ?></a>
							<?php endif; ?>
						</h4>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity" style="font-size:0.82rem; color:var(--color-text-muted);">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<div>
						<?php
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'woocommerce_cart_item_remove_link',
							sprintf(
								'<a href="%s" class="remove remove_from_cart_button cet-icon-action-btn" style="width:30px; height:30px;" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
								esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
								/* translators: %s is the product name */
								esc_attr( sprintf( __( 'Remove %s from cart', 'custom-ecommerce-theme' ), wp_strip_all_tags( $product_name ) ) ),
								esc_attr( (string) $product_id ),
								esc_attr( $cart_item_key ),
								esc_attr( $_product->get_sku() )
							),
							$cart_item_key
						);
						?>
					</div>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<p class="woocommerce-mini-cart__total total" style="display:flex; align-items:center; justify-content:space-between; padding:1.15rem 0; margin:0.5rem 0; border-bottom:1px solid var(--color-border); font-size:1rem;">
		<?php
		/**
		 * Hook: woocommerce_widget_shopping_cart_total.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<div class="woocommerce-mini-cart__buttons buttons" style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-top:1rem;">
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cet-btn cet-btn--outline">
			<?php esc_html_e( 'View Bag', 'custom-ecommerce-theme' ); ?>
		</a>
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="cet-btn cet-btn--gold">
			<?php esc_html_e( 'Checkout', 'custom-ecommerce-theme' ); ?>
		</a>
	</div>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<div class="woocommerce-mini-cart__empty-message" style="text-align:center; padding:2.5rem 1rem;">
		<p style="color:var(--color-text-muted); margin-bottom:1.25rem;"><?php esc_html_e( 'Your shopping bag is currently empty.', 'custom-ecommerce-theme' ); ?></p>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="cet-btn cet-btn--gold cet-btn--sm">
			<?php esc_html_e( 'Explore Collections', 'custom-ecommerce-theme' ); ?>
		</a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
