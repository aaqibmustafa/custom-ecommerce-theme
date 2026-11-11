<?php
/**
 * Enqueue scripts and styles
 *
 * Properly loads Parent Theme style.css even when a Child Theme is active,
 * ensuring all `:root` design tokens and CSS modules load in the exact cascade order.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend styles and scripts in modular order.
 *
 * @return void
 */
function cet_enqueue_assets(): void {
	// Google Fonts (Inter & Playfair Display).
	wp_enqueue_style(
		'cet-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap',
		array(),
		null
	);

	$style_ver = file_exists( CET_DIR . 'style.css' ) ? (string) filemtime( CET_DIR . 'style.css' ) : CET_VERSION;

	// 1. Always load the Parent Theme root style.css (`CET_URI . 'style.css'`) so `:root` variables exist even when Child Theme is active.
	wp_enqueue_style(
		'cet-style',
		CET_URI . 'style.css',
		array(),
		$style_ver
	);

	// Output Dynamic Customizer CSS Variables right after cet-style.
	if ( function_exists( 'cet_get_customizer_Dynamic_css' ) ) {
		wp_add_inline_style( 'cet-style', cet_get_customizer_Dynamic_css() );
	}

	// 2. Modular CSS files in dependency order.
	$css_modules = array(
		'cet-main'        => 'css/main.css',
		'cet-components'  => 'css/components.css',
		'cet-header'      => 'css/header.css',
		'cet-footer'      => 'css/footer.css',
		'cet-woocommerce' => 'css/woocommerce.css',
		'cet-responsive'  => 'css/responsive.css',
	);

	$prev_handle = 'cet-style';
	foreach ( $css_modules as $handle => $rel_path ) {
		$abs_path = CET_DIR . 'assets/' . $rel_path;
		if ( file_exists( $abs_path ) ) {
			wp_enqueue_style(
				$handle,
				CET_ASSETS_URI . $rel_path,
				array( $prev_handle ),
				(string) filemtime( $abs_path )
			);
			$prev_handle = $handle;
		}
	}

	// RTL Stylesheet when site language is Right-to-Left.
	if ( is_rtl() && file_exists( CET_DIR . 'rtl.css' ) ) {
		wp_enqueue_style(
			'cet-rtl',
			CET_URI . 'rtl.css',
			array( $prev_handle ),
			(string) filemtime( CET_DIR . 'rtl.css' )
		);
	}

	// 3. Modular JavaScript files.
	$js_modules = array(
		'cet-main'       => 'js/main.js',
		'cet-navigation' => 'js/navigation.js',
		'cet-search'     => 'js/search.js',
		'cet-product'    => 'js/product.js',
		'cet-cart'       => 'js/cart.js',
	);

	foreach ( $js_modules as $handle => $rel_path ) {
		$abs_path = CET_DIR . 'assets/' . $rel_path;
		if ( file_exists( $abs_path ) ) {
			$deps = ( 'cet-main' === $handle ) ? array() : array( 'cet-main' );
			wp_enqueue_script(
				$handle,
				CET_ASSETS_URI . $rel_path,
				$deps,
				(string) filemtime( $abs_path ),
				true
			);
		}
	}

	// Localize theme data for AJAX search, quick view, wishlist, compare, and cart.
	wp_localize_script(
		'cet-main',
		'cetThemeData',
		array(
			'ajaxUrl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'        => wp_create_nonce( 'cet_public_nonce' ),
			'siteUrl'      => esc_url( home_url( '/' ) ),
			'isWcActive'   => class_exists( 'WooCommerce' ),
			'wishlistIds'  => function_exists( 'cet_get_wishlist_ids' ) ? cet_get_wishlist_ids() : array(),
			'compareIds'   => function_exists( 'cet_get_compare_ids' ) ? cet_get_compare_ids() : array(),
			'i18n'         => array(
				'addedWishlist'   => esc_html__( 'Added to Wishlist', 'custom-ecommerce-theme' ),
				'removedWishlist' => esc_html__( 'Removed from Wishlist', 'custom-ecommerce-theme' ),
				'loading'         => esc_html__( 'Loading...', 'custom-ecommerce-theme' ),
				'noResults'       => esc_html__( 'No matching products found.', 'custom-ecommerce-theme' ),
			),
		)
	);

	// Threaded comment reply script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'cet_enqueue_assets' );
