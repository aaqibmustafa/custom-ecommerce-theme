/**
 * Custom E-Commerce Theme — Live AJAX Product Search & Shop Grid/List Switcher
 *
 * @package Custom_Ecommerce_Theme
 */

( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', () => {
		const searchInput = document.getElementById( 'cet-header-search-input' );
		const searchCat = document.querySelector( '.cet-search-form__cat' );
		const resultsBox = document.getElementById( 'cet-live-search-results' );

		// 1. Live AJAX Product Search with Debounce.
		if ( searchInput && resultsBox && window.cetThemeData ) {
			let debounceTimer = null;

			const performSearch = () => {
				const query = searchInput.value.trim();
				const category = searchCat ? searchCat.value : '';

				if ( query.length < 2 ) {
					resultsBox.classList.remove( 'is-open' );
					resultsBox.innerHTML = '';
					return;
				}

				const formData = new FormData();
				formData.append( 'action', 'cet_live_search' );
				formData.append( 'nonce', window.cetThemeData.nonce );
				formData.append( 'query', query );
				formData.append( 'category', category );

				fetch( window.cetThemeData.ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
				} )
					.then( ( r ) => r.json() )
					.then( ( res ) => {
						if ( ! res.success || ! res.data ) {
							return;
						}

						const items = res.data.results || [];
						if ( 0 === items.length ) {
							resultsBox.innerHTML = `<div class="cet-search-item"><span style="color:var(--color-text-muted);">${ window.cetThemeData.i18n.noResults }</span></div>`;
							resultsBox.classList.add( 'is-open' );
							return;
						}

						let html = '';
						items.forEach( ( item ) => {
							const imgTag = item.image
								? `<img src="${ item.image }" alt="" class="cet-search-item__img">`
								: '';
							html += `
								<a href="${ item.url }" class="cet-search-item">
									${ imgTag }
									<div>
										<h4 class="cet-search-item__title">${ item.title }</h4>
										<div class="cet-search-item__price">${ item.price_html }</div>
									</div>
								</a>
							`;
						} );

						if ( res.data.view_all ) {
							html += `<a href="${ res.data.view_all }" class="cet-search-item" style="justify-content:center; font-weight:700; color:var(--color-accent-hover);">View All Matching Results &rarr;</a>`;
						}

						resultsBox.innerHTML = html;
						resultsBox.classList.add( 'is-open' );
					} )
					.catch( () => {} );
			};

			searchInput.addEventListener( 'input', () => {
				clearTimeout( debounceTimer );
				debounceTimer = setTimeout( performSearch, 280 );
			} );

			if ( searchCat ) {
				searchCat.addEventListener( 'change', performSearch );
			}

			document.addEventListener( 'click', ( e ) => {
				if ( ! resultsBox.contains( e.target ) && e.target !== searchInput ) {
					resultsBox.classList.remove( 'is-open' );
				}
			} );
		}

		// 2. Shop Archive Grid / List View Switcher.
		const viewButtons = document.querySelectorAll( '.cet-view-btn[data-shop-view]' );
		const shopContainer = document.getElementById( 'cet-shop-products-container' );

		if ( viewButtons.length && shopContainer ) {
			viewButtons.forEach( ( btn ) => {
				btn.addEventListener( 'click', () => {
					const mode = btn.getAttribute( 'data-shop-view' );
					viewButtons.forEach( ( b ) => b.classList.remove( 'is-active' ) );
					btn.classList.add( 'is-active' );

					if ( 'list' === mode ) {
						shopContainer.classList.remove( 'cet-products-view-grid' );
						shopContainer.classList.add( 'cet-products-view-list' );
					} else {
						shopContainer.classList.remove( 'cet-products-view-list' );
						shopContainer.classList.add( 'cet-products-view-grid' );
					}
				} );
			} );
		}
	} );
} )();
