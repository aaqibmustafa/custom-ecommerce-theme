/**
 * Custom E-Commerce Theme — Off-Canvas Mini Cart Drawer, Showcase/WC Bag & Delivery/Payment Checkout
 *
 * @package Custom_Ecommerce_Theme
 */

( function () {
	'use strict';

	/**
	 * Save sanitized cookie string across root path.
	 *
	 * @param {string} val Encoded bag value `id:qty,id:qty`.
	 */
	function setBagCookie( val ) {
		const d = new Date();
		d.setTime( d.getTime() + 30 * 24 * 60 * 60 * 1000 );
		document.cookie = `cet_bag_items=${ encodeURIComponent( val || '' ) };expires=${ d.toUTCString() };path=/;SameSite=Lax`;
	}

	document.addEventListener( 'DOMContentLoaded', () => {
		const cartDrawer = document.getElementById( 'cet-mini-cart-drawer' );
		const cartClose = document.getElementById( 'cet-mini-cart-close' );
		const backdrop = document.getElementById( 'cet-drawer-backdrop' );

		const openCartDrawer = () => {
			if ( ! cartDrawer || ! backdrop ) return;
			// Ensure mobile nav drawer and center modal are closed cleanly first.
			const mobileDrawer = document.getElementById( 'cet-mobile-drawer' );
			if ( mobileDrawer ) {
				mobileDrawer.classList.remove( 'is-open' );
				mobileDrawer.setAttribute( 'aria-hidden', 'true' );
			}
			if ( window.CET && typeof window.CET.closeModal === 'function' ) {
				window.CET.closeModal();
			}

			cartDrawer.classList.add( 'is-open' );
			cartDrawer.setAttribute( 'aria-hidden', 'false' );
			backdrop.classList.add( 'is-open' );
			const primaryTrigger = document.getElementById( 'cet-cart-drawer-trigger' );
			if ( primaryTrigger ) primaryTrigger.setAttribute( 'aria-expanded', 'true' );
			document.body.style.overflow = 'hidden';
		};

		const closeCartDrawer = () => {
			if ( ! cartDrawer || ! backdrop ) return;
			cartDrawer.classList.remove( 'is-open' );
			cartDrawer.setAttribute( 'aria-hidden', 'true' );
			backdrop.classList.remove( 'is-open' );
			const primaryTrigger = document.getElementById( 'cet-cart-drawer-trigger' );
			if ( primaryTrigger ) primaryTrigger.setAttribute( 'aria-expanded', 'false' );
			document.body.style.overflow = '';
		};

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

		/**
		 * Synchronize bag badge count, drawer HTML, and cookie state.
		 *
		 * @param {object} data Response data from `cet_bag_action` or `cet_place_order`.
		 */
		const updateBagUI = ( data ) => {
			if ( ! data ) return;

			if ( 'undefined' !== typeof data.cookieValue ) {
				setBagCookie( data.cookieValue );
			}

			if ( 'undefined' !== typeof data.count ) {
				document.querySelectorAll( '.cet-cart-count' ).forEach( ( badge ) => {
					badge.textContent = String( data.count );
				} );
			}

			if ( 'undefined' !== typeof data.drawerHtml ) {
				const drawerBody = cartDrawer ? cartDrawer.querySelector( '.cet-mini-cart-body' ) : null;
				if ( drawerBody ) {
					drawerBody.innerHTML = data.drawerHtml;
				}
			}

			if ( 'undefined' !== typeof data.cartPageHtml && data.cartPageHtml ) {
				const cartPageContainer = document.getElementById( 'cet-cart-page-container' );
				if ( cartPageContainer ) {
					cartPageContainer.outerHTML = data.cartPageHtml;
				}
			}
		};

		/**
		 * Execute an AJAX bag mutation (`add`, `update_qty`, `remove`, `clear`).
		 */
		const sendBagAction = ( subAction, productId = 0, qty = 1, openAfter = false ) => {
			if ( ! window.cetThemeData ) return Promise.resolve( null );

			const formData = new FormData();
			formData.append( 'action', 'cet_bag_action' );
			formData.append( 'nonce', window.cetThemeData.nonce );
			formData.append( 'sub_action', subAction );
			formData.append( 'product_id', String( productId ) );
			formData.append( 'qty', String( qty ) );

			return fetch( window.cetThemeData.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			} )
				.then( ( r ) => r.json() )
				.then( ( res ) => {
					if ( res.success && res.data ) {
						updateBagUI( res.data );
						if ( res.data.message && window.CET && window.CET.showToast ) {
							window.CET.showToast( res.data.message );
						}
						if ( openAfter ) {
							openCartDrawer();
						}
						return res.data;
					}
					return null;
				} )
				.catch( () => null );
		};

		/**
		 * Open the Delivery & Payment Checkout Modal.
		 */
		const openCheckoutModal = () => {
			if ( ! window.cetThemeData || ! window.CET ) return;
			closeCartDrawer();
			window.CET.openModal( `<p style="text-align:center; padding:2.5rem;">${ window.cetThemeData.i18n.loading }</p>` );

			const formData = new FormData();
			formData.append( 'action', 'cet_get_checkout_modal' );
			formData.append( 'nonce', window.cetThemeData.nonce );

			fetch( window.cetThemeData.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			} )
				.then( ( r ) => r.json() )
				.then( ( res ) => {
					if ( res.success && res.data?.html ) {
						window.CET.openModal( res.data.html );
					} else {
						const msg = res.data?.message || 'Your shopping bag is currently empty.';
						window.CET.openModal( `<div class="cet-empty-state"><p>${ msg }</p></div>` );
					}
				} )
				.catch( () => {} );
		};

		/**
		 * Resolve quantity from button attributes (`data-qty-selector` or `data-qty`).
		 */
		const resolveButtonQty = ( btn ) => {
			const selector = btn.getAttribute( 'data-qty-selector' );
			if ( selector ) {
				const qtyInput = document.querySelector( selector );
				if ( qtyInput ) {
					return Math.max( 1, parseInt( qtyInput.value, 10 ) || 1 );
				}
			}
			return Math.max( 1, parseInt( btn.getAttribute( 'data-qty' ) || '1', 10 ) || 1 );
		};

		// Global Click Delegation for Cart Triggers, Add-to-Bag, Buy Now & Bag Item Actions.
		document.addEventListener( 'click', ( e ) => {
			// 1. Open Shopping Bag Drawer (Header, Mobile Drawer, Footer link).
			const trigger = e.target.closest( '.cet-cart-drawer-trigger' );
			if ( trigger && cartDrawer ) {
				if ( document.body.classList.contains( 'woocommerce-cart' ) || document.body.classList.contains( 'woocommerce-checkout' ) ) {
					return;
				}
				e.preventDefault();
				openCartDrawer();
				return;
			}

			// 2. Add to Shopping Bag button.
			const addBtn = e.target.closest( '.cet-add-to-bag-btn' );
			if ( addBtn ) {
				e.preventDefault();
				const productId = parseInt( addBtn.getAttribute( 'data-product-id' ) || '0', 10 );
				if ( ! productId ) return;

				const qty = resolveButtonQty( addBtn );
				addBtn.disabled = true;
				sendBagAction( 'add', productId, qty, true ).finally( () => {
					addBtn.disabled = false;
				} );
				return;
			}

			// 3. Instant Buy Now button -> Add to Bag + Immediately launch Delivery & Payment Checkout Modal.
			const buyNowBtn = e.target.closest( '.cet-buy-now-instant-btn' );
			if ( buyNowBtn ) {
				e.preventDefault();
				const productId = parseInt( buyNowBtn.getAttribute( 'data-product-id' ) || '0', 10 );
				if ( ! productId ) return;

				const qty = resolveButtonQty( buyNowBtn );
				buyNowBtn.disabled = true;
				sendBagAction( 'add', productId, qty, false )
					.then( () => {
						openCheckoutModal();
					} )
					.finally( () => {
						buyNowBtn.disabled = false;
					} );
				return;
			}

			// 4. Mini-Cart Drawer: Update Quantity (- / +).
			const qtyBtn = e.target.closest( '.cet-bag-qty-btn' );
			if ( qtyBtn ) {
				e.preventDefault();
				const productId = parseInt( qtyBtn.getAttribute( 'data-product-id' ) || '0', 10 );
				const delta = parseInt( qtyBtn.getAttribute( 'data-delta' ) || '0', 10 );
				if ( productId && delta ) {
					sendBagAction( 'update_qty', productId, delta, false );
				}
				return;
			}

			// 5. Mini-Cart Drawer: Remove Item.
			const removeBtn = e.target.closest( '.cet-bag-remove-btn' );
			if ( removeBtn ) {
				e.preventDefault();
				const productId = parseInt( removeBtn.getAttribute( 'data-product-id' ) || '0', 10 );
				if ( productId ) {
					sendBagAction( 'remove', productId, 0, false );
				}
				return;
			}

			// 6. Mini-Cart Drawer: Clear Bag.
			if ( e.target.closest( '#cet-clear-bag-btn' ) ) {
				e.preventDefault();
				sendBagAction( 'clear', 0, 0, false );
				return;
			}

			// 7. Mini-Cart Drawer: Proceed to Delivery & Payment Modal.
			if ( e.target.closest( '#cet-open-checkout-modal' ) ) {
				e.preventDefault();
				openCheckoutModal();
			}
		} );

		// Live Delivery Service & Payment Option UI Updates inside Checkout Modal.
		document.addEventListener( 'change', ( e ) => {
			const input = e.target;
			if ( ! input || ( input.name !== 'delivery_method' && input.name !== 'payment_method' ) ) {
				return;
			}

			const form = input.closest( '#cet-direct-checkout-form' );
			if ( ! form ) return;

			// Highlight selected radio label cards with Champagne Gold border.
			form.querySelectorAll( `input[name="${ input.name }"]` ).forEach( ( radio ) => {
				const label = radio.closest( 'label' );
				if ( label ) {
					label.classList.toggle( 'is-selected', radio.checked );
				}
			} );

			if ( input.name === 'delivery_method' ) {
				const portal = form.closest( '.cet-checkout-portal' );
				const subtotal = parseFloat( portal?.dataset.subtotal || '0' );
				const stdFee = parseFloat( portal?.dataset.standardFee || '0' );
				const priFee = parseFloat( portal?.dataset.priorityFee || '25' );
				const activeFee = ( input.value === 'priority' ) ? priFee : stdFee;

				const deliveryDisplay = document.getElementById( 'cet-checkout-delivery-display' );
				const totalDisplay = document.getElementById( 'cet-checkout-total-display' );
				if ( deliveryDisplay ) {
					deliveryDisplay.textContent = ( activeFee === 0 )
						? 'FREE Insured Express'
						: `$${ activeFee.toFixed( 2 ) }`;
				}
				if ( totalDisplay ) {
					totalDisplay.textContent = `$${ ( subtotal + activeFee ).toFixed( 2 ) }`;
				}
			}
		} );

		// Process Direct Delivery & Payment Checkout Order Submission.
		document.addEventListener( 'submit', ( e ) => {
			const form = e.target;
			if ( ! form || form.id !== 'cet-direct-checkout-form' || ! window.cetThemeData ) {
				return;
			}
			e.preventDefault();

			const submitBtn = form.querySelector( 'button[type="submit"]' );
			const origText = submitBtn ? submitBtn.textContent : '';
			if ( submitBtn ) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Confirming Order...';
			}

			const formData = new FormData( form );
			formData.append( 'action', 'cet_place_order' );
			formData.append( 'nonce', window.cetThemeData.nonce );

			fetch( window.cetThemeData.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			} )
				.then( ( r ) => r.json() )
				.then( ( res ) => {
					if ( res.success && res.data?.html ) {
						setBagCookie( '' );
						updateBagUI( {
							count: 0,
							drawerHtml: res.data.drawerHtml,
							cartPageHtml: res.data.cartPageHtml || '',
						} );
						const inlinePortal = form.closest( '.cet-checkout-portal--page' );
						if ( inlinePortal ) {
							inlinePortal.innerHTML = `<div class="cet-card" style="padding:1.5rem 1.25rem; max-width:680px; margin:0 auto;">${ res.data.html }</div>`;
							inlinePortal.scrollIntoView( { behavior: 'smooth', block: 'start' } );
						} else {
							window.CET.openModal( res.data.html );
						}
						if ( window.CET.showToast ) {
							window.CET.showToast( 'Order confirmed! Thank you for shopping with us.' );
						}
					} else {
						if ( window.CET.showToast ) {
							window.CET.showToast( res.data?.message || 'Unable to place order. Please check your details.' );
						}
						if ( submitBtn ) {
							submitBtn.disabled = false;
							submitBtn.textContent = origText;
						}
					}
				} )
				.catch( () => {
					if ( submitBtn ) {
						submitBtn.disabled = false;
						submitBtn.textContent = origText;
					}
				} );
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
