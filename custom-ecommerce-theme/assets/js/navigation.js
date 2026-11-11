/**
 * Custom E-Commerce Theme — Sticky Header, Accessible Mega Menu & Mobile Navigation Drawer
 *
 * @package Custom_Ecommerce_Theme
 */

( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', () => {
		const header = document.getElementById( 'masthead' );
		const mobileTrigger = document.getElementById( 'cet-mobile-menu-trigger' );
		const mobileDrawer = document.getElementById( 'cet-mobile-drawer' );
		const mobileClose = document.getElementById( 'cet-mobile-drawer-close' );
		const backdrop = document.getElementById( 'cet-drawer-backdrop' );
		const megaTrigger = document.querySelector( '.cet-mega-trigger' );
		const megaWrap = document.querySelector( '.cet-mega-trigger-wrap' );

		// 1. Sticky Header on Scroll.
		if ( header && header.classList.contains( 'cet-header--sticky-enabled' ) ) {
			const handleScroll = () => {
				if ( window.scrollY > 110 ) {
					header.classList.add( 'is-sticky' );
				} else {
					header.classList.remove( 'is-sticky' );
				}
			};
			window.addEventListener( 'scroll', handleScroll, { passive: true } );
			handleScroll();
		}

		// 2. Mobile Navigation Drawer Open/Close.
		const openMobileDrawer = () => {
			if ( ! mobileDrawer || ! backdrop ) return;
			mobileDrawer.classList.add( 'is-open' );
			mobileDrawer.setAttribute( 'aria-hidden', 'false' );
			backdrop.classList.add( 'is-open' );
			if ( mobileTrigger ) mobileTrigger.setAttribute( 'aria-expanded', 'true' );
			document.body.style.overflow = 'hidden';
		};

		const closeMobileDrawer = () => {
			if ( ! mobileDrawer || ! backdrop ) return;
			mobileDrawer.classList.remove( 'is-open' );
			mobileDrawer.setAttribute( 'aria-hidden', 'true' );
			backdrop.classList.remove( 'is-open' );
			if ( mobileTrigger ) mobileTrigger.setAttribute( 'aria-expanded', 'false' );
			document.body.style.overflow = '';
		};

		if ( mobileTrigger ) {
			mobileTrigger.addEventListener( 'click', openMobileDrawer );
		}
		if ( mobileClose ) {
			mobileClose.addEventListener( 'click', closeMobileDrawer );
		}
		if ( backdrop ) {
			backdrop.addEventListener( 'click', closeMobileDrawer );
		}

		// 3. Department Mega Menu Keyboard & Click Accessibility.
		if ( megaTrigger && megaWrap ) {
			megaTrigger.addEventListener( 'click', ( e ) => {
				e.stopPropagation();
				const isOpen = megaWrap.classList.toggle( 'is-open' );
				megaTrigger.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			} );

			document.addEventListener( 'click', ( e ) => {
				if ( ! megaWrap.contains( e.target ) ) {
					megaWrap.classList.remove( 'is-open' );
					megaTrigger.setAttribute( 'aria-expanded', 'false' );
				}
			} );
		}

		document.addEventListener( 'keydown', ( e ) => {
			if ( 'Escape' === e.key ) {
				closeMobileDrawer();
				if ( megaWrap && megaTrigger ) {
					megaWrap.classList.remove( 'is-open' );
					megaTrigger.setAttribute( 'aria-expanded', 'false' );
				}
			}
		} );
	} );
} )();
