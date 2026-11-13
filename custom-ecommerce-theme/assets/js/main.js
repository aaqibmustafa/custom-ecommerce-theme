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

	/**
	 * Open Client Account Portal Modal (Sign In, Create Account, or Client Orders).
	 *
	 * @param {string} tab Initial tab ('login' or 'register').
	 */
	window.CET.openAccountModal = function ( tab = 'login' ) {
		if ( ! window.cetThemeData ) return;
		window.CET.openModal( `<p style="text-align:center; padding:2.5rem;">${ window.cetThemeData.i18n.loading }</p>` );

		const formData = new FormData();
		formData.append( 'action', 'cet_get_account_modal' );
		formData.append( 'nonce', window.cetThemeData.nonce );
		formData.append( 'tab', tab );

		fetch( window.cetThemeData.ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin',
		} )
			.then( ( r ) => r.json() )
			.then( ( res ) => {
				if ( res.success && res.data?.html ) {
					window.CET.openModal( res.data.html );
				}
			} )
			.catch( () => {} );
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

		// 5. Client Account Portal Triggers, Tab Switching, Login, Registration & Sign Out.
		const syncAccountUI = ( data ) => {
			if ( ! data ) return;
			const labelEl = document.getElementById( 'cet-account-action-label' );
			if ( labelEl && data.label ) {
				labelEl.textContent = data.label;
			}
			if ( window.cetThemeData && 'undefined' !== typeof data.isLoggedIn ) {
				window.cetThemeData.isLoggedIn = Boolean( data.isLoggedIn );
			}
			if ( 'undefined' !== typeof data.wishlistCount ) {
				const wishBadge = document.getElementById( 'cet-wishlist-count' );
				if ( wishBadge ) wishBadge.textContent = String( data.wishlistCount );
			}
			if ( 'undefined' !== typeof data.bagCount ) {
				document.querySelectorAll( '.cet-cart-count' ).forEach( ( badge ) => {
					badge.textContent = String( data.bagCount );
				} );
			}
			if ( data.bagDrawerHtml ) {
				const bagBody = document.querySelector( '#cet-mini-cart-drawer .cet-mini-cart-body' );
				if ( bagBody ) bagBody.innerHTML = data.bagDrawerHtml;
			}
		};

		document.addEventListener( 'click', ( e ) => {
			// A. Open Account Modal instead of redirecting to wp-login.php.
			const accTrigger = e.target.closest( '.cet-account-modal-trigger' );
			if ( accTrigger ) {
				e.preventDefault();
				const mobileDrawer = document.getElementById( 'cet-mobile-drawer' );
				const backdrop = document.getElementById( 'cet-drawer-backdrop' );
				if ( mobileDrawer ) {
					mobileDrawer.classList.remove( 'is-open' );
					mobileDrawer.setAttribute( 'aria-hidden', 'true' );
				}
				if ( backdrop ) backdrop.classList.remove( 'is-open' );
				window.CET.openAccountModal( accTrigger.getAttribute( 'data-account-tab' ) || 'login' );
				return;
			}

			// B. Switch Sign In <-> Create Account Tabs.
			const tabBtn = e.target.closest( '.cet-account-tab-btn' );
			if ( tabBtn ) {
				e.preventDefault();
				const portal = tabBtn.closest( '.cet-account-portal' );
				if ( ! portal ) return;

				const target = tabBtn.getAttribute( 'data-target' );
				const loginForm = portal.querySelector( '#cet-account-login-form' );
				const regForm = portal.querySelector( '#cet-account-register-form' );
				const feedbackBox = portal.querySelector( '.cet-account-feedback' );

				portal.querySelectorAll( '.cet-account-tab-btn' ).forEach( ( b ) => {
					const active = b === tabBtn;
					b.classList.toggle( 'cet-btn--primary', active );
					b.classList.toggle( 'cet-btn--outline', ! active );
				} );

				if ( loginForm ) loginForm.style.display = ( 'login' === target ) ? '' : 'none';
				if ( regForm ) regForm.style.display = ( 'register' === target ) ? '' : 'none';
				if ( feedbackBox ) feedbackBox.style.display = 'none';
				return;
			}

			// C. Sign Out Shopper inside Account Portal.
			const logoutBtn = e.target.closest( '#cet-account-logout-btn' );
			if ( logoutBtn && window.cetThemeData ) {
				e.preventDefault();
				logoutBtn.disabled = true;

				const formData = new FormData();
				formData.append( 'action', 'cet_account_logout' );
				formData.append( 'nonce', window.cetThemeData.nonce );

				fetch( window.cetThemeData.ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
				} )
					.then( ( r ) => r.json() )
					.then( ( res ) => {
						if ( res.success && res.data ) {
							syncAccountUI( res.data );
							const portalWrap = logoutBtn.closest( '.cet-account-portal' );
							if ( portalWrap && res.data.html ) {
								portalWrap.outerHTML = res.data.html;
							} else if ( res.data.html ) {
								window.CET.openModal( res.data.html );
							}
							if ( res.data.message ) {
								window.CET.showToast( res.data.message );
							}
						}
					} )
					.catch( () => {
						logoutBtn.disabled = false;
					} );
			}
		} );

		// Handle Login & Create Account Form Submissions (works in Modal OR [cet_account] Page).
		document.addEventListener( 'submit', ( e ) => {
			const form = e.target;
			if ( ! form || ( form.id !== 'cet-account-login-form' && form.id !== 'cet-account-register-form' ) ) {
				return;
			}
			if ( ! window.cetThemeData ) return;
			e.preventDefault();

			const portal = form.closest( '.cet-account-portal' );
			const feedbackBox = portal ? portal.querySelector( '.cet-account-feedback' ) : null;
			const submitBtn = form.querySelector( 'button[type="submit"]' );
			const origLabel = submitBtn ? submitBtn.textContent : '';
			const ajaxAction = ( form.id === 'cet-account-register-form' ) ? 'cet_account_register' : 'cet_account_login';

			if ( feedbackBox ) feedbackBox.style.display = 'none';
			if ( submitBtn ) {
				submitBtn.disabled = true;
				submitBtn.textContent = window.cetThemeData.i18n.loading || 'Please wait...';
			}

			const formData = new FormData( form );
			formData.append( 'action', ajaxAction );
			formData.append( 'nonce', window.cetThemeData.nonce );

			fetch( window.cetThemeData.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			} )
				.then( ( r ) => r.json() )
				.then( ( res ) => {
					if ( res.success && res.data ) {
						syncAccountUI( res.data );
						if ( portal && res.data.html ) {
							portal.outerHTML = res.data.html;
						}
						if ( res.data.message ) {
							window.CET.showToast( res.data.message );
						}
					} else {
						if ( feedbackBox ) {
							feedbackBox.style.display = 'block';
							feedbackBox.style.background = '#FEF2F2';
							feedbackBox.style.color = '#991B1B';
							feedbackBox.style.border = '1px solid #FECACA';
							feedbackBox.textContent = res.data?.message || 'Authentication failed. Please check your details.';
						}
						if ( submitBtn ) {
							submitBtn.disabled = false;
							submitBtn.textContent = origLabel;
						}
					}
				} )
				.catch( () => {
					if ( submitBtn ) {
						submitBtn.disabled = false;
						submitBtn.textContent = origLabel;
					}
				} );
		} );

		// 9. Interactive Light & Dark Mode System.
		( function initThemeMode() {
			const getEffectiveTheme = () => {
				try {
					const stored = localStorage.getItem( 'cet_theme_mode' );
					if ( stored === 'dark' || stored === 'light' ) {
						return stored;
					}
				} catch ( err ) {}

				const def = ( window.cetThemeData && window.cetThemeData.defaultColorMode ) || 'light';
				if ( def === 'dark' ) {
					return 'dark';
				}
				if ( def === 'auto' && window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches ) {
					return 'dark';
				}
				return 'light';
			};

			const applyTheme = ( theme, announce = false ) => {
				const html = document.documentElement;
				const body = document.body;

				html.classList.add( 'cet-theme-transitioning' );
				html.setAttribute( 'data-theme', theme );

				if ( theme === 'dark' ) {
					html.classList.add( 'cet-dark-mode' );
					if ( body ) body.classList.add( 'cet-dark-mode' );
				} else {
					html.classList.remove( 'cet-dark-mode' );
					if ( body ) body.classList.remove( 'cet-dark-mode' );
				}

				const isDark = ( theme === 'dark' );
				const nextLabel = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
				const statusText = isDark ? 'Dark Mode' : 'Light Mode';

				document.querySelectorAll( '.cet-theme-toggle-btn' ).forEach( ( btn ) => {
					btn.setAttribute( 'aria-label', nextLabel );
					btn.setAttribute( 'title', nextLabel );
					const textEl = btn.querySelector( '.cet-theme-mode-text' );
					if ( textEl ) {
						textEl.textContent = statusText;
					}
				} );

				setTimeout( () => {
					html.classList.remove( 'cet-theme-transitioning' );
				}, 350 );

				if ( announce && window.CET && window.CET.showToast ) {
					window.CET.showToast( isDark ? 'Dark Mode Activated' : 'Light Mode Activated' );
				}
			};

			document.addEventListener( 'click', ( e ) => {
				const btn = e.target.closest( '.cet-theme-toggle-btn' );
				if ( ! btn ) return;

				e.preventDefault();
				const current = document.documentElement.getAttribute( 'data-theme' ) || 'light';
				const next = ( current === 'dark' ) ? 'light' : 'dark';

				try {
					localStorage.setItem( 'cet_theme_mode', next );
				} catch ( err ) {}

				applyTheme( next, true );
			} );

			window.addEventListener( 'storage', ( e ) => {
				if ( e.key === 'cet_theme_mode' && ( e.newValue === 'light' || e.newValue === 'dark' ) ) {
					applyTheme( e.newValue, false );
				}
			} );

			if ( window.matchMedia ) {
				window.matchMedia( '(prefers-color-scheme: dark)' ).addEventListener( 'change', ( e ) => {
					if ( ! localStorage.getItem( 'cet_theme_mode' ) ) {
						applyTheme( e.matches ? 'dark' : 'light', false );
					}
				} );
			}

			applyTheme( getEffectiveTheme(), false );
		} )();
	} );
} )();
