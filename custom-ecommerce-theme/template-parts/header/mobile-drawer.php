<?php
/**
 * Mobile Off-Canvas Navigation Drawer Template Part
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="cet-drawer-backdrop" id="cet-drawer-backdrop" aria-hidden="true"></div>

<aside id="cet-mobile-drawer" class="cet-mobile-drawer" aria-label="<?php esc_attr_e( 'Mobile Navigation Drawer', 'custom-ecommerce-theme' ); ?>" aria-hidden="true">
	<div class="cet-mobile-drawer__header">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cet-brand-link">
			<?php bloginfo( 'name' ); ?>
		</a>
		<button type="button" class="cet-icon-action-btn" id="cet-mobile-drawer-close" aria-label="<?php esc_attr_e( 'Close Mobile Menu', 'custom-ecommerce-theme' ); ?>">
			<?php echo cet_get_svg_icon( 'close', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>

	<div class="cet-mobile-drawer__body">
		<?php get_search_form(); ?>

		<nav aria-label="<?php esc_attr_e( 'Mobile Menu', 'custom-ecommerce-theme' ); ?>">
			<?php
			$mobile_location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
			if ( has_nav_menu( $mobile_location ) ) {
				wp_nav_menu(
					array(
						'theme_location' => $mobile_location,
						'menu_class'     => 'cet-mobile-nav-list',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
			} else {
				?>
				<?php
				$has_wc_acc    = function_exists( 'cet_has_wc_account_page' ) && cet_has_wc_account_page();
				$account_url   = function_exists( 'cet_get_account_url' ) ? cet_get_account_url() : '#cet-account-portal';
				$shop_url      = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'shop' ) : home_url( '/#signature-products' );
				$cart_url      = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'cart' ) : $shop_url;
				$checkout_url  = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'checkout' ) : $shop_url;
				$wishlist_url  = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'wishlist' ) : $shop_url;
				$track_url     = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'track-order' ) : $shop_url;
				$acct_page_url = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'my-account' ) : $account_url;
				?>
				<ul class="cet-mobile-nav-list">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'custom-ecommerce-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop Catalog', 'custom-ecommerce-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $cart_url ); ?>"><?php esc_html_e( 'Shopping Bag Page', 'custom-ecommerce-theme' ); ?></a></li>
					<li><a href="#cet-mini-cart-drawer" class="cet-cart-drawer-trigger"><?php esc_html_e( 'Quick Bag Drawer', 'custom-ecommerce-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $checkout_url ); ?>"><?php esc_html_e( 'Checkout & Delivery', 'custom-ecommerce-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $wishlist_url ); ?>"><?php esc_html_e( 'Saved Wishlist', 'custom-ecommerce-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $track_url ); ?>"><?php esc_html_e( 'Track Order', 'custom-ecommerce-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $acct_page_url ); ?>"><?php echo is_user_logged_in() ? esc_html__( 'My Account Page', 'custom-ecommerce-theme' ) : esc_html__( 'Account Sign In / Register', 'custom-ecommerce-theme' ); ?></a></li>
					<?php if ( ! $has_wc_acc ) : ?>
						<li><a href="<?php echo esc_url( $account_url ); ?>" class="cet-account-modal-trigger"><?php esc_html_e( 'Quick Sign-In Portal', 'custom-ecommerce-theme' ); ?></a></li>
					<?php endif; ?>
				</ul>
				<?php
			}
			?>
		</nav>
	</div>
</aside>

