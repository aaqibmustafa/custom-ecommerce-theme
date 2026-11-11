/**
 * Custom E-Commerce Theme — Core Frontend Controller, Toast Notifications, Tabs, Countdown & Newsletter
 *
 * @package Custom_Ecommerce_Theme
 */

( function () {
	'use strict';

	window.CET = window.CET || {};

	/**
	 * Display a luxury toast notification in the bottom-right corner.
	 *
	 * @param {string} message Notification text.
	 */
	window.CET.showToast = function ( message ) {
		const container = document.getElementById( 'cet-toast-container' );
		if ( ! container || ! message ) {
			return;
		}

		const toast = document.createElement( 'div' );
		toast.className = 'cet-toast';
		toast.textContent = message;
		container.appendChild( toast );

		setTimeout( () => {
			toast.style.opacity = '0';
			toast.style.transition = 'opacity 0.3s ease';
			setTimeout( () => toast.remove(), 320 );
		}, 3400 );
	};

	/**
	 * Open the shared modal dialog with custom HTML content.
	 *
	 * @param {string} html Inner HTML for the modal body.
	 */
	window.CET.openModal = function ( html ) {
		const backdrop = document.getElementById( 'cet-modal-backdrop' );
		const body = document.getElementById( 'cet-modal-body' );
		if ( ! backdrop || ! body ) {
			return;
		}
		body.innerHTML = html;
		backdrop.classList.add( 'is-open' );
		backdrop.setAttribute( 'aria-hidden', 'false' );
		document.body.style.overflow = 'hidden';
	};

	/**
	 * Close the shared modal dialog.
	 */
	window.CET.closeModal = function () {
		const backdrop = document.getElementById( 'cet-modal-backdrop' );
		if ( ! backdrop ) {
			return;
		}
		backdrop.classList.remove( 'is-open' );
		backdrop.setAttribute( 'aria-hidden', 'true' );
		document.body.style.overflow = '';
	};

	document.addEventListener( 'DOMContentLoaded', () => {
		document.documentElement.classList.add( 'cet-js-loaded' );

		// 1. Modal Close Handlers.
		const modalClose = document.getElementById( 'cet-modal-close' );
		const modalBackdrop = document.getElementById( 'cet-modal-backdrop' );
		if ( modalClose ) {
			modalClose.addEventListener( 'click', window.CET.closeModal );
		}
		if ( modalBackdrop ) {
			modalBackdrop.addEventListener( 'click', ( e ) => {
				if ( e.target === modalBackdrop ) {
					window.CET.closeModal();
				}
			} );
		}

		// Escape key closes modals and drawers.
		document.addEventListener( 'keydown', ( e ) => {
			if ( 'Escape' === e.key ) {
				window.CET.closeModal();
			}
		} );

		// 2. Interactive Homepage Product Tabs.
		const tabButtons = document.querySelectorAll( '.cet-tab-btn[data-tab-target]' );
		tabButtons.forEach( ( btn ) => {
			btn.addEventListener( 'click', () => {
				const targetId = btn.getAttribute( 'data-tab-target' );
				const parentSection = btn.closest( '.cet-section' ) || document;

				parentSection.querySelectorAll( '.cet-tab-btn' ).forEach( ( b ) => {
					b.classList.remove( 'is-active' );
					b.setAttribute( 'aria-selected', 'false' );
				} );
				parentSection.querySelectorAll( '.cet-tab-panel' ).forEach( ( p ) => {
					p.classList.remove( 'is-active' );
				} );

				btn.classList.add( 'is-active' );
				btn.setAttribute( 'aria-selected', 'true' );
				const targetPanel = document.getElementById( targetId );
				if ( targetPanel ) {
					targetPanel.classList.add( 'is-active' );
				}
			} );
		} );

		// 3. Special Offer Countdown Timer.
		const countdownBanner = document.querySelector( '[data-countdown-target]' );
		if ( countdownBanner ) {
			const targetStr = countdownBanner.getAttribute( 'data-countdown-target' );
			const targetTime = Date.parse( targetStr );

			if ( ! Number.isNaN( targetTime ) ) {
				const updateTimer = () => {
					const now = Date.now();
					const diff = Math.max( 0, targetTime - now );

					const days = Math.floor( diff / ( 1000 * 60 * 60 * 24 ) );
					const hours = Math.floor( ( diff / ( 1000 * 60 * 60 ) ) % 24 );
					const minutes = Math.floor( ( diff / 1000 / 60 ) % 60 );
					const seconds = Math.floor( ( diff / 1000 ) % 60 );

					const dEl = countdownBanner.querySelector( '[data-unit="days"]' );
					const hEl = countdownBanner.querySelector( '[data-unit="hours"]' );
					const mEl = countdownBanner.querySelector( '[data-unit="minutes"]' );
					const sEl = countdownBanner.querySelector( '[data-unit="seconds"]' );

					if ( dEl ) dEl.textContent = String( days ).padStart( 2, '0' );
					if ( hEl ) hEl.textContent = String( hours ).padStart( 2, '0' );
					if ( mEl ) mEl.textContent = String( minutes ).padStart( 2, '0' );
					if ( sEl ) sEl.textContent = String( seconds ).padStart( 2, '0' );
				};
				updateTimer();
				setInterval( updateTimer, 1000 );
			}
		}

		// 4. AJAX Newsletter Subscription Form.
		const newsletterForm = document.getElementById( 'cet-newsletter-form' );
		if ( newsletterForm && window.cetThemeData ) {
			newsletterForm.addEventListener( 'submit', ( e ) => {
				e.preventDefault();
				const emailInput = document.getElementById( 'cet-newsletter-email' );
				const feedback = document.getElementById( 'cet-newsletter-feedback' );
				if ( ! emailInput || ! emailInput.value ) {
					return;
				}

				const formData = new FormData();
				formData.append( 'action', 'cet_newsletter_subscribe' );
				formData.append( 'nonce', window.cetThemeData.nonce );
				formData.append( 'email', emailInput.value );

				fetch( window.cetThemeData.ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
				} )
					.then( ( r ) => r.json() )
					.then( ( res ) => {
						if ( res.success && res.data && res.data.message ) {
							if ( feedback ) feedback.textContent = res.data.message;
							window.CET.showToast( res.data.message );
							newsletterForm.reset();
						} else if ( res.data && res.data.message ) {
							if ( feedback ) feedback.textContent = res.data.message;
						}
					} )
					.catch( () => {} );
			} );
		}
	} );
} )();
