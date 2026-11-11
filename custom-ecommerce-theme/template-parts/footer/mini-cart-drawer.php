<?php
/**
 * Off-Canvas Mini Cart Drawer, Quick View Modal, Compare Modal & Toast Container
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- Off-Canvas Mini Cart Drawer -->
<aside id="cet-mini-cart-drawer" class="cet-mini-cart-drawer" aria-label="<?php esc_attr_e( 'Shopping Bag Drawer', 'custom-ecommerce-theme' ); ?>" aria-hidden="true">
	<div class="cet-mini-cart-drawer__header">
		<h3 class="cet-mini-cart-drawer__title">
			<?php echo cet_get_svg_icon( 'bag', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'Your Shopping Bag', 'custom-ecommerce-theme' ); ?></span>
		</h3>
		<button type="button" class="cet-icon-action-btn" id="cet-mini-cart-close" aria-label="<?php esc_attr_e( 'Close Shopping Bag', 'custom-ecommerce-theme' ); ?>">
			<?php echo cet_get_svg_icon( 'close', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>

	<div class="cet-mini-cart-body">
		<?php
		if ( function_exists( 'cet_render_free_shipping_progress_bar' ) ) {
			cet_render_free_shipping_progress_bar();
		}
		?>
		<div class="widget_shopping_cart_content">
			<?php
			if ( function_exists( 'woocommerce_mini_cart' ) && function_exists( 'WC' ) && WC()->cart ) {
				woocommerce_mini_cart();
			} else {
				?>
				<p class="woocommerce-mini-cart__empty-message">
					<?php esc_html_e( 'No products in your shopping bag yet.', 'custom-ecommerce-theme' ); ?>
				</p>
				<?php
			}
			?>
		</div>
	</div>
</aside>

<!-- Quick View & Product Comparison Modal -->
<div id="cet-modal-backdrop" class="cet-modal-backdrop" role="dialog" aria-modal="true" aria-hidden="true" aria-label="<?php esc_attr_e( 'Product Details Modal', 'custom-ecommerce-theme' ); ?>">
	<div class="cet-modal">
		<button type="button" class="cet-modal__close" id="cet-modal-close" aria-label="<?php esc_attr_e( 'Close Modal', 'custom-ecommerce-theme' ); ?>">
			<?php echo cet_get_svg_icon( 'close', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<div id="cet-modal-body" class="cet-modal__body"></div>
	</div>
</div>

<!-- Sticky Product Comparison Bar -->
<div id="cet-compare-bar" class="cet-compare-bar" aria-live="polite">
	<div class="cet-container cet-compare-bar__inner">
		<div>
			<strong class="cet-eyebrow" style="margin:0;"><?php esc_html_e( 'Product Comparison', 'custom-ecommerce-theme' ); ?></strong>
			<span id="cet-compare-count-text"><?php esc_html_e( 'Select products to compare side by side', 'custom-ecommerce-theme' ); ?></span>
		</div>
		<div style="display:flex; gap:0.75rem;">
			<button type="button" class="cet-btn cet-btn--gold cet-btn--sm" id="cet-open-compare-modal">
				<?php esc_html_e( 'Compare Now', 'custom-ecommerce-theme' ); ?>
			</button>
			<button type="button" class="cet-btn cet-btn--outline-gold cet-btn--sm" id="cet-clear-compare">
				<?php esc_html_e( 'Clear', 'custom-ecommerce-theme' ); ?>
			</button>
		</div>
	</div>
</div>

<!-- Toast Notifications Container -->
<div id="cet-toast-container" class="cet-toast-container" aria-live="polite"></div>

