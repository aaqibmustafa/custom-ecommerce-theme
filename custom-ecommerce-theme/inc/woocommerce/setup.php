<?php
/**
 * WooCommerce initial theme support and foundational configuration
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare WooCommerce support and configure product gallery features.
 *
 * @return void
 */
function cet_woocommerce_setup(): void {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 450,
			'single_image_width'    => 800,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'max_rows'        => 8,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 5,
			),
		)
	);

	// Enable native WooCommerce product gallery features.
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'cet_woocommerce_setup' );

