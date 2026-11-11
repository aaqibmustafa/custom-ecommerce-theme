<?php
/**
 * Custom E-Commerce Child Theme functions and definitions
 *
 * Safely override or extend parent theme functionality here.
 * Parent theme updates will never overwrite this file.
 *
 * @package Custom_Ecommerce_Child
 * @author  Aaqib Mustafa
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load child theme textdomain for translations.
 *
 * @return void
 */
function cet_child_setup(): void {
	load_child_theme_textdomain(
		'custom-ecommerce-child',
		get_stylesheet_directory() . '/languages'
	);
}
add_action( 'after_setup_theme', 'cet_child_setup', 20 );

/**
 * Enqueue parent and child theme stylesheets in the proper cascade order.
 *
 * @return void
 */
function cet_child_enqueue_styles(): void {
	$parent_style_path = get_template_directory() . '/style.css';
	$parent_version    = file_exists( $parent_style_path ) ? (string) filemtime( $parent_style_path ) : '1.0.0';

	// Ensure the parent root style.css is loaded with its own handle when child theme is active.
	wp_enqueue_style(
		'cet-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		$parent_version
	);

	$child_style_path = get_stylesheet_directory() . '/style.css';
	$child_version    = file_exists( $child_style_path ) ? (string) filemtime( $child_style_path ) : '1.0.0';

	// Determine highest-priority parent dependency handle so child styles always load last.
	$deps = array( 'cet-parent-style' );
	foreach ( array( 'cet-main', 'cet-components', 'cet-header', 'cet-footer', 'cet-woocommerce', 'cet-responsive' ) as $handle ) {
		if ( wp_style_is( $handle, 'enqueued' ) || wp_style_is( $handle, 'registered' ) ) {
			$deps[] = $handle;
		}
	}

	wp_enqueue_style(
		'cet-child-style',
		get_stylesheet_uri(),
		$deps,
		$child_version
	);
}
add_action( 'wp_enqueue_scripts', 'cet_child_enqueue_styles', 30 );

/**
 * Example Customization 1: Add a custom class to the body tag when the Child Theme is active.
 *
 * @param array<int, string> $classes Existing body classes.
 * @return array<int, string>
 */
function cet_child_body_class( array $classes ): array {
	$classes[] = 'cet-child-theme-active';
	return $classes;
}
add_filter( 'body_class', 'cet_child_body_class', 20 );

/**
 * Example Customization 2: Filter parent theme default Customizer options if desired.
 *
 * @param array<string, mixed> $defaults Parent theme default options.
 * @return array<string, mixed>
 */
function cet_child_filter_defaults( array $defaults ): array {
	// Uncomment below to customize default values in the child theme:
	// $defaults['free_shipping_threshold'] = 100;
	return $defaults;
}
add_filter( 'cet_default_theme_options', 'cet_child_filter_defaults' );
