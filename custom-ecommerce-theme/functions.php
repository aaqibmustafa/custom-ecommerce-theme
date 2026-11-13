<?php
/**
 * Custom E-Commerce Theme functions and definitions
 *
 * @package Custom_Ecommerce_Theme
 * @author  Aaqib Mustafa
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Define core theme constants.
 */
define( 'CET_VERSION', '1.4.1' );
define( 'CET_DIR', trailingslashit( get_template_directory() ) );
define( 'CET_URI', trailingslashit( get_template_directory_uri() ) );
define( 'CET_INC_DIR', CET_DIR . 'inc/' );
define( 'CET_ASSETS_URI', CET_URI . 'assets/' );

/**
 * Load modular theme setup, Customizer, widgets, performance, showcase data, store pages, AJAX, and helper files.
 */
$cet_core_includes = array(
	'inc/theme-setup.php',
	'inc/customizer.php',
	'inc/widgets.php',
	'inc/breadcrumbs.php',
	'inc/performance.php',
	'inc/showcase-data.php',
	'inc/template-functions.php',
	'inc/store-pages.php',
	'inc/ajax-handlers.php',
	'inc/enqueue.php',
);

foreach ( $cet_core_includes as $cet_file ) {
	$cet_filepath = CET_DIR . $cet_file;
	if ( file_exists( $cet_filepath ) ) {
		require_once $cet_filepath;
	}
}

/**
 * Load WooCommerce integration files when WooCommerce is active.
 */
if ( class_exists( 'WooCommerce' ) ) {
	$cet_wc_includes = array(
		'inc/woocommerce/setup.php',
		'inc/woocommerce/hooks.php',
	);

	foreach ( $cet_wc_includes as $cet_wc_file ) {
		$cet_wc_filepath = CET_DIR . $cet_wc_file;
		if ( file_exists( $cet_wc_filepath ) ) {
			require_once $cet_wc_filepath;
		}
	}
}
