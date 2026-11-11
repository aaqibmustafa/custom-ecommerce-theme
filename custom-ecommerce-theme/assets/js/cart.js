/**
 * Custom E-Commerce Theme — Off-Canvas Mini Cart Drawer & WooCommerce AJAX Cart Integration
 *
 * @package Custom_Ecommerce_Theme
 */

( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', () => {
		const cartTrigger = document.getElementById( 'cet-cart-drawer-trigger' );
		const cartDrawer = document.getElementById( 'cet-mini-cart-drawer' );
		const cartClose = document.getElementById( 'cet-mini-cart-close' );
		const backdrop = document.getElementById( 'cet-drawer-backdrop' );

		const openCartDrawer = () => {
			if ( ! cartDrawer || ! backdrop ) return;
			cartDrawer.classList.add( 'is-open' );
			cartDrawer.setAttribute( 'aria-hidden', 'false' );
			backdrop.classList.add( 'is-open' );
			if ( cartTrigger ) cartTrigger.setAttribute( 'aria-expanded', 'true' );
			document.body.style.overflow = 'hidden';
		};

		const closeCartDrawer = () => {
			if ( ! cartDrawer || ! backdrop ) return;
			cartDrawer.classList.remove( 'is-open' );
			cartDrawer.setAttribute( 'aria-hidden', 'true' );
			backdrop.classList.remove( 'is-open' );
			if ( cartTrigger ) cartTrigger.setAttribute( 'aria-expanded', 'false' );
			document.body.style.overflow = '';
		};

		if ( cartTrigger && cartDrawer ) {
			cartTrigger.addEventListener( 'click', ( e ) => {
				// Allow direct navigation on Cart/Checkout pages; open off-canvas drawer elsewhere.
				if ( document.body.classList.contains( 'woocommerce-cart' ) || document.body.classList.contains( 'woocommerce-checkout' ) ) {
					return;
				}
				e.preventDefault();
				openCartDrawer();
			} );
		}

		if ( cartClose ) {
			cartClose.addEventListener( 'click', closeCartDrawer );
		}

		if ( backdrop ) {
			backdrop.addEventListener( 'click', closeCartDrawer );
		}

		document.addEventListener( 'keydown', ( e ) => {
			if ( 'Escape' === e.key ) {
				closeCartDrawer();
			}
		} );

		// Listen for WooCommerce's native jQuery `added_to_cart` event to automatically open the Mini Cart Drawer.
		if ( window.jQuery ) {
			window.jQuery( document.body ).on( 'added_to_cart', () => {
				openCartDrawer();
				if ( window.CET && window.CET.showToast ) {
					window.CET.showToast( 'Added to your shopping bag.' );
				}
			} );
		}
	} );
} )();
