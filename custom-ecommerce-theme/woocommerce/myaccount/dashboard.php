<?php
/**
 * My Account Dashboard
 *
 * Shows the customer greeting and quick-access luxury portal cards.
 *
 * @package Custom_Ecommerce_Theme
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<div class="cet-account-welcome" style="margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid var(--color-border);">
	<span class="cet-eyebrow"><?php esc_html_e( 'Private Client Portal', 'custom-ecommerce-theme' ); ?></span>
	<p style="font-size:1.05rem; margin-bottom:0.75rem;">
		<?php
		printf(
			/* translators: 1: user display name 2: logout url */
			wp_kses( __( 'Welcome back, <strong>%1$s</strong> (not <strong>%1$s</strong>? <a href="%2$s">Sign out</a>)', 'custom-ecommerce-theme' ), $allowed_html ),
			'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
			esc_url( wc_logout_url() )
		);
		?>
	</p>
	<p style="color:var(--color-text-muted); margin:0;">
		<?php
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'custom-ecommerce-theme' );
		if ( wc_shipping_enabled() ) {
			$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'custom-ecommerce-theme' );
		}
		printf(
			wp_kses( $dashboard_desc, $allowed_html ),
			esc_url( wc_get_endpoint_url( 'orders' ) ),
			esc_url( wc_get_endpoint_url( 'edit-address' ) ),
			esc_url( wc_get_endpoint_url( 'edit-account' ) )
		);
		?>
	</p>
</div>

<div class="cet-grid-cols-3" style="margin-bottom:2rem;">
	<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="cet-promo-card" style="padding:1.5rem;">
		<span class="cet-eyebrow"><?php esc_html_e( 'Purchases', 'custom-ecommerce-theme' ); ?></span>
		<h4 style="margin-bottom:0.35rem;"><?php esc_html_e( 'Order History', 'custom-ecommerce-theme' ); ?></h4>
		<p style="font-size:0.85rem; color:var(--color-text-muted); margin:0;"><?php esc_html_e( 'Track shipments, view invoices, and manage returns.', 'custom-ecommerce-theme' ); ?></p>
	</a>

	<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="cet-promo-card" style="padding:1.5rem;">
		<span class="cet-eyebrow"><?php esc_html_e( 'Destinations', 'custom-ecommerce-theme' ); ?></span>
		<h4 style="margin-bottom:0.35rem;"><?php esc_html_e( 'Saved Addresses', 'custom-ecommerce-theme' ); ?></h4>
		<p style="font-size:0.85rem; color:var(--color-text-muted); margin:0;"><?php esc_html_e( 'Manage billing and delivery locations for faster checkout.', 'custom-ecommerce-theme' ); ?></p>
	</a>

	<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="cet-promo-card" style="padding:1.5rem;">
		<span class="cet-eyebrow"><?php esc_html_e( 'Security', 'custom-ecommerce-theme' ); ?></span>
		<h4 style="margin-bottom:0.35rem;"><?php esc_html_e( 'Account Profile', 'custom-ecommerce-theme' ); ?></h4>
		<p style="font-size:0.85rem; color:var(--color-text-muted); margin:0;"><?php esc_html_e( 'Update your personal profile and security credentials.', 'custom-ecommerce-theme' ); ?></p>
	</a>
</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );
?>

