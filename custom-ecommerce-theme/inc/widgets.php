<?php
/**
 * Register widget areas and sidebars
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme sidebars and footer widget columns.
 *
 * @return void
 */
function cet_widgets_init(): void {
	// Blog Sidebar.
	register_sidebar(
		array(
			'name'          => esc_html__( 'Blog Sidebar', 'custom-ecommerce-theme' ),
			'id'            => 'sidebar-blog',
			'description'   => esc_html__( 'Add widgets here to appear in your blog archive and single post sidebar.', 'custom-ecommerce-theme' ),
			'before_widget' => '<section id="%1$s" class="widget cet-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title cet-widget__title">',
			'after_title'   => '</h3>',
		)
	);

	// WooCommerce Shop Sidebar.
	register_sidebar(
		array(
			'name'          => esc_html__( 'Shop & Product Archive Sidebar', 'custom-ecommerce-theme' ),
			'id'            => 'sidebar-shop',
			'description'   => esc_html__( 'Add WooCommerce filter widgets here for the shop, category, and tag pages.', 'custom-ecommerce-theme' ),
			'before_widget' => '<section id="%1$s" class="widget cet-widget cet-shop-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title cet-widget__title">',
			'after_title'   => '</h3>',
		)
	);

	// 4 Footer Widget Columns.
	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: Footer column number. */
				'name'          => sprintf( esc_html__( 'Footer Widget Area %d', 'custom-ecommerce-theme' ), $i ),
				'id'            => 'footer-widget-' . $i,
				/* translators: %d: Footer column number. */
				'description'   => sprintf( esc_html__( 'Widgets displayed in footer column %d.', 'custom-ecommerce-theme' ), $i ),
				'before_widget' => '<div id="%1$s" class="widget cet-footer-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h4 class="cet-footer__heading">',
				'after_title'   => '</h4>',
			)
		);
	}
}
add_action( 'widgets_init', 'cet_widgets_init' );
