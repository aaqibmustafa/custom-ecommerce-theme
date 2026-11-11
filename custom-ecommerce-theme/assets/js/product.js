/**
 * Custom E-Commerce Theme — Single Product Variations, Quick View, Wishlist, Compare & Quantity Steppers
 *
 * @package Custom_Ecommerce_Theme
 */

( function () {
	'use strict';

	/**
	 * Helper to set a browser cookie.
	 */
	function setCookie( name, value, days = 30 ) {
		const d = new Date();
		d.setTime( d.getTime() + days * 24 * 60 * 60 * 1000 );
		document.cookie = `${ name }=${ encodeURIComponent( value ) };expires=${ d.toUTCString() };path=/;SameSite=Lax`;
	}

	document.addEventListener( 'DOMContentLoaded', () => {
		const compareIds = new Set(
			Array.isArray( window.cetThemeData?.compareIds )
				? window.cetThemeData.compareIds.map( Number )
				: []
		);

		const updateCompareBar = () => {
			const bar = document.getElementById( 'cet-compare-bar' );
			const countText = document.getElementById( 'cet-compare-count-text' );
			if ( ! bar ) return;

			if ( compareIds.size > 0 ) {
				bar.classList.add( 'is-visible' );
				if ( countText ) {
					countText.textContent = `${ compareIds.size } product(s) selected for side-by-side comparison`;
				}
			} else {
				bar.classList.remove( 'is-visible' );
			}
			setCookie( 'cet_compare_ids', Array.from( compareIds ).join( ',' ) );
		};

		// 1. Event Delegation for Quick View, Wishlist, Compare & Quantity Steppers.
		document.addEventListener( 'click', ( e ) => {
			// A. Quantity Stepper Plus / Minus.
			const qtyBtn = e.target.closest( '.cet-qty-btn' );
			if ( qtyBtn ) {
				const wrapper = qtyBtn.closest( '.cet-qty-stepper, .quantity' );
				const input = wrapper ? wrapper.querySelector( 'input.qty' ) : null;
				if ( input ) {
					const current = parseFloat( input.value ) || 1;
					const min = parseFloat( input.min ) || 1;
					const max = parseFloat( input.max ) || 999;
					const step = parseFloat( input.step ) || 1;

					if ( qtyBtn.classList.contains( 'cet-qty-minus' ) ) {
						input.value = String( Math.max( min, current - step ) );
					} else {
						input.value = String( Math.min( max, current + step ) );
					}
					input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				}
				return;
			}

			// B. Quick View Trigger.
			const qvBtn = e.target.closest( '.cet-quickview-trigger' );
			if ( qvBtn && window.cetThemeData ) {
				e.preventDefault();
				const productId = qvBtn.getAttribute( 'data-product-id' );
				if ( ! productId ) return;

				window.CET.openModal( `<p style="text-align:center; padding:2rem;">${ window.cetThemeData.i18n.loading }</p>` );

				const formData = new FormData();
				formData.append( 'action', 'cet_quick_view' );
				formData.append( 'nonce', window.cetThemeData.nonce );
				formData.append( 'product_id', productId );

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
				return;
			}

			// C. Wishlist Toggle Button.
			const wishBtn = e.target.closest( '.cet-wishlist-toggle' );
			if ( wishBtn && window.cetThemeData ) {
				e.preventDefault();
				const productId = wishBtn.getAttribute( 'data-product-id' );
				if ( ! productId ) return;

				const formData = new FormData();
				formData.append( 'action', 'cet_toggle_wishlist' );
				formData.append( 'nonce', window.cetThemeData.nonce );
				formData.append( 'product_id', productId );

				fetch( window.cetThemeData.ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
				} )
					.then( ( r ) => r.json() )
					.then( ( res ) => {
						if ( res.success && res.data ) {
							const allBtns = document.querySelectorAll( `.cet-wishlist-toggle[data-product-id="${ productId }"]` );
							allBtns.forEach( ( b ) => b.classList.toggle( 'is-active', Boolean( res.data.added ) ) );

							const badge = document.getElementById( 'cet-wishlist-count' );
							if ( badge ) badge.textContent = String( res.data.count );

							if ( Array.isArray( res.data.ids ) ) {
								setCookie( 'cet_wishlist_ids', res.data.ids.join( ',' ) );
							}
							if ( res.data.message ) {
								window.CET.showToast( res.data.message );
							}
						}
					} )
					.catch( () => {} );
				return;
			}

			// D. Compare Toggle Button.
			const cmpBtn = e.target.closest( '.cet-compare-toggle' );
			if ( cmpBtn ) {
				e.preventDefault();
				const id = Number( cmpBtn.getAttribute( 'data-product-id' ) );
				if ( ! id ) return;

				if ( compareIds.has( id ) ) {
					compareIds.delete( id );
					cmpBtn.classList.remove( 'is-active' );
					window.CET.showToast( 'Removed from comparison list.' );
				} else {
					if ( compareIds.size >= 4 ) {
						window.CET.showToast( 'You can compare up to 4 products at a time.' );
						return;
					}
					compareIds.add( id );
					cmpBtn.classList.add( 'is-active' );
					window.CET.showToast( 'Added to product comparison.' );
				}
				updateCompareBar();
				return;
			}

			// E. Remove Item Inside Compare Modal.
			const cmpRemove = e.target.closest( '.cet-compare-remove-btn' );
			if ( cmpRemove ) {
				const id = Number( cmpRemove.getAttribute( 'data-product-id' ) );
				compareIds.delete( id );
				updateCompareBar();
				document.getElementById( 'cet-open-compare-modal' )?.click();
			}
		} );

		// 2. Open Compare Modal Button & Clear Compare.
		const openCompareBtn = document.getElementById( 'cet-open-compare-modal' );
		const clearCompareBtn = document.getElementById( 'cet-clear-compare' );

		if ( openCompareBtn && window.cetThemeData ) {
			openCompareBtn.addEventListener( 'click', () => {
				const formData = new FormData();
				formData.append( 'action', 'cet_compare_products' );
				formData.append( 'nonce', window.cetThemeData.nonce );
				formData.append( 'product_ids', Array.from( compareIds ).join( ',' ) );

				window.CET.openModal( `<p style="text-align:center; padding:2rem;">${ window.cetThemeData.i18n.loading }</p>` );

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
			} );
		}

		if ( clearCompareBtn ) {
			clearCompareBtn.addEventListener( 'click', () => {
				compareIds.clear();
				document.querySelectorAll( '.cet-compare-toggle.is-active' ).forEach( ( b ) => b.classList.remove( 'is-active' ) );
				updateCompareBar();
			} );
		}

		// 3. Enhance Native WooCommerce Variation Dropdowns with Interactive Swatch Pills.
		const variationSelects = document.querySelectorAll( '.variations select' );
		variationSelects.forEach( ( selectEl ) => {
			if ( selectEl.dataset.cetSwatchesReady ) return;
			selectEl.dataset.cetSwatchesReady = 'true';

			const group = document.createElement( 'div' );
			group.className = 'cet-swatch-group';

			Array.from( selectEl.options ).forEach( ( opt ) => {
				if ( ! opt.value ) return;
				const pill = document.createElement( 'button' );
				pill.type = 'button';
				pill.className = 'cet-swatch-pill' + ( selectEl.value === opt.value ? ' is-selected' : '' );
				pill.textContent = opt.textContent;
				pill.addEventListener( 'click', () => {
					selectEl.value = opt.value;
					selectEl.dispatchEvent( new Event( 'change', { bubbles: true } ) );
					if ( window.jQuery ) {
						window.jQuery( selectEl ).trigger( 'change' );
					}
					group.querySelectorAll( '.cet-swatch-pill' ).forEach( ( p ) => p.classList.remove( 'is-selected' ) );
					pill.classList.add( 'is-selected' );
				} );
				group.appendChild( pill );
			} );

			selectEl.parentNode.appendChild( group );
		} );

		// 4. Sticky Add-to-Cart Bottom Bar on Single Product Scroll.
		const stickyBar = document.getElementById( 'cet-sticky-product-bar' );
		if ( stickyBar ) {
			window.addEventListener(
				'scroll',
				() => {
					if ( window.scrollY > 520 ) {
						stickyBar.classList.add( 'is-visible' );
						stickyBar.setAttribute( 'aria-hidden', 'false' );
					} else {
						stickyBar.classList.remove( 'is-visible' );
						stickyBar.setAttribute( 'aria-hidden', 'true' );
					}
				},
				{ passive: true }
			);
		}
	} );
} )();

