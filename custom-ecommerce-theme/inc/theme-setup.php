<?php
/**
 * Theme setup and feature registration
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cet_theme_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @return void
	 */
	function cet_theme_setup(): void {
		/*
		 * Make theme available for translation.
		 */
		load_theme_textdomain( 'custom-ecommerce-theme', CET_DIR . 'languages' );

		/*
		 * Add default posts and comments RSS feed links to head.
		 */
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Register custom image sizes for product cards and blog layouts.
		 */
		add_image_size( 'cet-product-card', 600, 750, true );
		add_image_size( 'cet-blog-card', 800, 520, true );

		/*
		 * Register navigation menu locations.
		 */
		register_nav_menus(
			array(
				'primary'  => esc_html__( 'Primary Navigation', 'custom-ecommerce-theme' ),
				'mobile'   => esc_html__( 'Mobile Navigation Drawer', 'custom-ecommerce-theme' ),
				'topbar'   => esc_html__( 'Top Announcement Bar Menu', 'custom-ecommerce-theme' ),
				'footer-1' => esc_html__( 'Footer Column 1 (Shop)', 'custom-ecommerce-theme' ),
				'footer-2' => esc_html__( 'Footer Column 2 (Company)', 'custom-ecommerce-theme' ),
				'footer-3' => esc_html__( 'Footer Column 3 (Customer Care)', 'custom-ecommerce-theme' ),
			)
		);

		/*
		 * Switch default core markup to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		/*
		 * Add theme support for Custom Logo.
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 80,
				'width'       => 240,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		/*
		 * Add theme support for selective refresh for widgets.
		 */
		add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		 * Add Gutenberg / Block Editor support.
		 */
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'F5F7FA',
			)
		);
	}
}
add_action( 'after_setup_theme', 'cet_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * @global int $content_width
 * @return void
 */
function cet_content_width(): void {
	$GLOBALS['content_width'] = apply_filters( 'cet_content_width', 1280 );
}
add_action( 'after_setup_theme', 'cet_content_width', 0 );

