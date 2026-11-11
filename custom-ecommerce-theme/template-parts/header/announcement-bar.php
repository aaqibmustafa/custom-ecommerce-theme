<?php
/**
 * Top Announcement & Promotional Bar Template Part
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'announcement_enable', true ) ) {
	return;
}

$promo_text = (string) cet_get_option( 'announcement_text' );
$link_text  = (string) cet_get_option( 'announcement_link_text' );
$link_url   = (string) cet_get_option( 'announcement_link_url' );
?>
<div class="cet-announcement-bar" role="region" aria-label="<?php esc_attr_e( 'Store Announcement', 'custom-ecommerce-theme' ); ?>">
	<div class="cet-container cet-announcement-bar__inner">
		<p class="cet-announcement-bar__promo">
			<span class="cet-announcement-bar__badge"><?php esc_html_e( 'Privilege', 'custom-ecommerce-theme' ); ?></span>
			<span><?php echo esc_html( $promo_text ); ?></span>
			<?php if ( ! empty( $link_text ) && ! empty( $link_url ) ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="cet-announcement-bar__link">
					<?php echo esc_html( $link_text ); ?>
				</a>
			<?php endif; ?>
		</p>

		<div class="cet-announcement-bar__utils">
			<?php if ( has_nav_menu( 'topbar' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'topbar',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			<?php else : ?>
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Boutique Catalog', 'custom-ecommerce-theme' ); ?></a>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Order Tracking', 'custom-ecommerce-theme' ); ?></a>
				<?php endif; ?>
				<span><?php echo esc_html( (string) cet_get_option( 'footer_phone', '+1 (800) 555-0199' ) ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</div>

