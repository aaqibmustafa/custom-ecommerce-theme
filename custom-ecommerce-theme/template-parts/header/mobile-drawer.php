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
				<ul class="cet-mobile-nav-list">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'custom-ecommerce-theme' ); ?></a></li>
					<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
						<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop All', 'custom-ecommerce-theme' ); ?></a></li>
						<li><a href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>"><?php esc_html_e( 'Shopping Bag', 'custom-ecommerce-theme' ); ?></a></li>
						<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My Account', 'custom-ecommerce-theme' ); ?></a></li>
					<?php endif; ?>
				</ul>
				<?php
			}
			?>
		</nav>
	</div>
</aside>
