<?php
/**
 * My Account Page Template
 *
 * Wraps WooCommerce account navigation and content in our luxury two-column portal layout.
 *
 * @package Custom_Ecommerce_Theme
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="cet-myaccount-wrapper">
	<?php
	/**
	 * My Account navigation.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_navigation' );
	?>

	<div class="woocommerce-MyAccount-content">
		<?php
		/**
		 * My Account content.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_account_content' );
		?>
	</div>
</div>
