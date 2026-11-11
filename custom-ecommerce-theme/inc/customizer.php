<?php
/**
 * WordPress Customizer Settings, Panels, Controls & Sanitization
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cet_default_options' ) ) {
	/**
	 * Centralized default values for all Custom E-Commerce Theme settings.
	 * Child themes can filter these defaults via `cet_default_theme_options`.
	 *
	 * @return array<string, mixed>
	 */
	function cet_default_options(): array {
		$defaults = array(
			// Brand Colors.
			'color_primary'              => '#14243A',
			'color_accent'               => '#D6AD65',
			'color_secondary'            => '#356AE6',
			'color_background'           => '#F5F7FA',
			'color_text'                 => '#202938',
			'color_border'               => '#E8EDF5',

			// Typography & Layout.
			'container_width'            => 1280,
			'heading_font'               => 'Playfair Display',
			'body_font'                  => 'Inter',

			// Announcement Bar & Header.
			'announcement_enable'        => true,
			'announcement_text'          => __( 'Complimentary Express Shipping on Orders Over $150 — Use Code: GOLD2026', 'custom-ecommerce-theme' ),
			'announcement_link_text'     => __( 'Explore Collection', 'custom-ecommerce-theme' ),
			'announcement_link_url'      => '#featured-collections',
			'sticky_header_enable'       => true,
			'header_layout'              => 'luxury-split', // luxury-split | centered-brand | minimal-bar
			'enable_mega_menu'           => true,
			'enable_header_search'       => true,
			'enable_header_wishlist'     => true,

			// Homepage Sections Visibility & Content.
			'hero_enable'                => true,
			'hero_eyebrow'               => __( 'AUTUMN / WINTER 2026 ATELIER', 'custom-ecommerce-theme' ),
			'hero_title'                 => __( 'Timeless Craftsmanship Meets Modern Luxury', 'custom-ecommerce-theme' ),
			'hero_subtitle'              => __( 'Discover curated timepieces, fine leather goods, and signature essentials designed in Midnight Navy and Champagne Gold.', 'custom-ecommerce-theme' ),
			'hero_primary_cta_text'      => __( 'Shop New Arrivals', 'custom-ecommerce-theme' ),
			'hero_primary_cta_url'       => '',
			'hero_secondary_cta_text'    => __( 'Explore Lookbook', 'custom-ecommerce-theme' ),
			'hero_secondary_cta_url'     => '#featured-collections',
			'hero_side_1_title'          => __( 'Signature Gold Edition', 'custom-ecommerce-theme' ),
			'hero_side_1_subtitle'       => __( 'Up to 25% Off Limited Pieces', 'custom-ecommerce-theme' ),
			'hero_side_2_title'          => __( 'Bespoke Essentials', 'custom-ecommerce-theme' ),
			'hero_side_2_subtitle'       => __( 'Handcrafted Daily Luxury', 'custom-ecommerce-theme' ),

			'home_categories_enable'     => true,
			'home_categories_title'      => __( 'Shop by Curated Category', 'custom-ecommerce-theme' ),
			'home_categories_subtitle'   => __( 'Explore our signature departments tailored for refined everyday living.', 'custom-ecommerce-theme' ),

			'home_product_tabs_enable'   => true,
			'home_product_tabs_title'    => __( 'The Signature Selection', 'custom-ecommerce-theme' ),
			'home_product_tabs_count'    => 8,

			'home_collections_enable'    => true,
			'home_collections_title'     => __( 'Featured Collections', 'custom-ecommerce-theme' ),

			'home_countdown_enable'      => true,
			'home_countdown_eyebrow'     => __( 'PRIVATE VAULT EVENT — LIMITED TIME OFFER', 'custom-ecommerce-theme' ),
			'home_countdown_title'       => __( 'Champagne Gold Collector’s Event — Save Up to 30%', 'custom-ecommerce-theme' ),
			'home_countdown_date'        => gmdate( 'Y-m-d', strtotime( '+14 days' ) ) . 'T23:59:59',
			'home_countdown_code'        => 'GOLDVAULT30',

			'home_brands_enable'         => true,
			'home_recommendations_enable'=> true,
			'home_testimonials_enable'   => true,
			'home_blog_enable'           => true,
			'home_newsletter_enable'     => true,
			'home_social_enable'         => true,
			'home_instagram_handle'      => '@CustomEcommerceAtelier',

			// Shop & Product Settings.
			'shop_sidebar_layout'        => 'left-sidebar', // left-sidebar | right-sidebar | full-width
			'shop_default_columns'       => 4,
			'shop_products_per_page'     => 12,
			'product_card_show_rating'   => true,
			'product_card_show_category' => true,
			'product_card_show_quickview'=> true,
			'product_card_show_wishlist' => true,
			'product_card_show_compare'  => true,
			'free_shipping_threshold'    => 150,
			'single_product_buy_now'     => true,
			'single_product_sticky_bar'  => true,

			// Blog Settings.
			'blog_sidebar_layout'        => 'right-sidebar',
			'blog_show_related_posts'    => true,

			// Footer & Social Links.
			'footer_about_text'          => __( 'An elevated WooCommerce destination crafted with Midnight Navy authority and Champagne Gold refinement. Designed for discerning modern shoppers.', 'custom-ecommerce-theme' ),
			'footer_phone'               => '+1 (800) 555-0199',
			'footer_email'               => 'concierge@customecommerce.example',
			'footer_address'             => '742 Madison Avenue, Suite 1200, New York, NY',
			'social_instagram'           => 'https://instagram.com/',
			'social_facebook'            => 'https://facebook.com/',
			'social_x'                   => 'https://x.com/',
			'social_pinterest'           => 'https://pinterest.com/',
		);

		return (array) apply_filters( 'cet_default_theme_options', $defaults );
	}
}

if ( ! function_exists( 'cet_get_option' ) ) {
	/**
	 * Retrieve a theme modification value with fallback to centralized default.
	 *
	 * @param string $key           Option key.
	 * @param mixed  $fallback_override Optional explicit fallback.
	 * @return mixed
	 */
	function cet_get_option( string $key, $fallback_override = null ) {
		$defaults = cet_default_options();
		$default  = ( null !== $fallback_override ) ? $fallback_override : ( $defaults[ $key ] ?? '' );
		return get_theme_mod( $key, $default );
	}
}

/**
 * Sanitize boolean checkbox values.
 *
 * @param mixed $checked Whether the checkbox is checked.
 * @return bool
 */
function cet_sanitize_checkbox( $checked ): bool {
	return ( isset( $checked ) && ( true === $checked || '1' === $checked || 1 === $checked ) );
}

/**
 * Sanitize select / radio choices against valid control choices.
 *
 * @param string               $input   Selected value.
 * @param WP_Customize_Setting $setting Setting object.
 * @return string
 */
function cet_sanitize_select( string $input, $setting ): string {
	$input   = sanitize_key( $input );
	$control = $setting->manager->get_control( $setting->id );
	if ( $control && isset( $control->choices[ $input ] ) ) {
		return $input;
	}
	return (string) $setting->default;
}

/**
 * Register Custom E-Commerce Theme panels, sections, and controls in the WordPress Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function cet_customize_register( WP_Customize_Manager $wp_customize ): void {
	$defaults = cet_default_options();

	// Main Theme Panel.
	$wp_customize->add_panel(
		'cet_theme_panel',
		array(
			'title'       => esc_html__( 'Custom E-Commerce Theme Options', 'custom-ecommerce-theme' ),
			'description' => esc_html__( 'Configure brand colors, header, homepage sections, WooCommerce shop layout, and footer settings.', 'custom-ecommerce-theme' ),
			'priority'    => 25,
		)
	);

	/*
	 * 1. Brand Identity & Color System Section
	 */
	$wp_customize->add_section(
		'cet_section_colors',
		array(
			'title'    => esc_html__( '1. Brand Colors (Champagne Gold System)', 'custom-ecommerce-theme' ),
			'panel'    => 'cet_theme_panel',
			'priority' => 10,
		)
	);

	$color_settings = array(
		'color_primary'    => esc_html__( 'Primary Color (Midnight Navy)', 'custom-ecommerce-theme' ),
		'color_accent'     => esc_html__( 'Signature Accent (Champagne Gold)', 'custom-ecommerce-theme' ),
		'color_secondary'  => esc_html__( 'Secondary Interactive (Electric Blue)', 'custom-ecommerce-theme' ),
		'color_background' => esc_html__( 'Main Background (Cloud White)', 'custom-ecommerce-theme' ),
		'color_text'       => esc_html__( 'Main Text (Graphite)', 'custom-ecommerce-theme' ),
		'color_border'     => esc_html__( 'Border Color (Soft Slate)', 'custom-ecommerce-theme' ),
	);

	foreach ( $color_settings as $color_key => $color_label ) {
		$wp_customize->add_setting(
			$color_key,
			array(
				'default'           => $defaults[ $color_key ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color_key,
				array(
					'label'   => $color_label,
					'section' => 'cet_section_colors',
				)
			)
		);
	}

	/*
	 * 2. Header, Announcement Bar & Navigation Section
	 */
	$wp_customize->add_section(
		'cet_section_header',
		array(
			'title'    => esc_html__( '2. Header, Mega Menu & Announcement Bar', 'custom-ecommerce-theme' ),
			'panel'    => 'cet_theme_panel',
			'priority' => 20,
		)
	);

	$header_checkboxes = array(
		'announcement_enable'    => esc_html__( 'Display Top Announcement Bar', 'custom-ecommerce-theme' ),
		'sticky_header_enable'   => esc_html__( 'Enable Sticky Header on Scroll', 'custom-ecommerce-theme' ),
		'enable_mega_menu'       => esc_html__( 'Enable Department Mega Menu', 'custom-ecommerce-theme' ),
		'enable_header_search'   => esc_html__( 'Enable Live Product Search in Header', 'custom-ecommerce-theme' ),
		'enable_header_wishlist' => esc_html__( 'Show Wishlist Icon in Header', 'custom-ecommerce-theme' ),
	);

	foreach ( $header_checkboxes as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $setting_id ],
				'sanitize_callback' => 'cet_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'cet_section_header',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'announcement_text',
		array(
			'default'           => $defaults['announcement_text'],
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'announcement_text',
		array(
			'label'   => esc_html__( 'Announcement Bar Message', 'custom-ecommerce-theme' ),
			'section' => 'cet_section_header',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'header_layout',
		array(
			'default'           => $defaults['header_layout'],
			'sanitize_callback' => 'cet_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'header_layout',
		array(
			'label'   => esc_html__( 'Header Layout Style', 'custom-ecommerce-theme' ),
			'section' => 'cet_section_header',
			'type'    => 'select',
			'choices' => array(
				'luxury-split'   => esc_html__( 'Luxury Two-Tier Header (Recommended)', 'custom-ecommerce-theme' ),
				'centered-brand' => esc_html__( 'Centered Brand Identity', 'custom-ecommerce-theme' ),
				'minimal-bar'    => esc_html__( 'Compact Single-Row Bar', 'custom-ecommerce-theme' ),
			),
		)
	);

	/*
	 * 3. Homepage Sections Manager
	 */
	$wp_customize->add_section(
		'cet_section_homepage',
		array(
			'title'    => esc_html__( '3. Homepage Sections & Promotions', 'custom-ecommerce-theme' ),
			'panel'    => 'cet_theme_panel',
			'priority' => 30,
		)
	);

	$home_toggles = array(
		'hero_enable'                 => esc_html__( 'Enable Hero Banner Showcase', 'custom-ecommerce-theme' ),
		'home_categories_enable'      => esc_html__( 'Enable Featured Categories Section', 'custom-ecommerce-theme' ),
		'home_product_tabs_enable'    => esc_html__( 'Enable Trending / Best-Selling / New Arrivals Tabs', 'custom-ecommerce-theme' ),
		'home_collections_enable'     => esc_html__( 'Enable Featured Collections Section', 'custom-ecommerce-theme' ),
		'home_countdown_enable'       => esc_html__( 'Enable Special Offer Countdown Section', 'custom-ecommerce-theme' ),
		'home_brands_enable'          => esc_html__( 'Enable Brand Showcase Strip', 'custom-ecommerce-theme' ),
		'home_recommendations_enable' => esc_html__( 'Enable Recommended & Recently Viewed Products', 'custom-ecommerce-theme' ),
		'home_testimonials_enable'    => esc_html__( 'Enable Customer Testimonials Section', 'custom-ecommerce-theme' ),
		'home_blog_enable'            => esc_html__( 'Enable Latest Blog Articles Section', 'custom-ecommerce-theme' ),
		'home_newsletter_enable'      => esc_html__( 'Enable Newsletter Subscription Section', 'custom-ecommerce-theme' ),
		'home_social_enable'          => esc_html__( 'Enable Instagram / Social Gallery Strip', 'custom-ecommerce-theme' ),
	);

	foreach ( $home_toggles as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $setting_id ],
				'sanitize_callback' => 'cet_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'cet_section_homepage',
				'type'    => 'checkbox',
			)
		);
	}

	$home_text_fields = array(
		'hero_eyebrow'           => esc_html__( 'Hero Eyebrow Badge', 'custom-ecommerce-theme' ),
		'hero_title'             => esc_html__( 'Hero Main Headline', 'custom-ecommerce-theme' ),
		'hero_subtitle'          => esc_html__( 'Hero Subtitle Description', 'custom-ecommerce-theme' ),
		'hero_primary_cta_text'  => esc_html__( 'Hero Primary Button Label', 'custom-ecommerce-theme' ),
		'home_countdown_title'   => esc_html__( 'Special Offer Countdown Headline', 'custom-ecommerce-theme' ),
		'home_countdown_date'    => esc_html__( 'Countdown Target Date (YYYY-MM-DDTHH:MM:SS)', 'custom-ecommerce-theme' ),
		'home_countdown_code'    => esc_html__( 'Promotional Coupon Code Highlight', 'custom-ecommerce-theme' ),
		'home_instagram_handle'  => esc_html__( 'Instagram Handle Display', 'custom-ecommerce-theme' ),
	);

	foreach ( $home_text_fields as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $setting_id ],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'cet_section_homepage',
				'type'    => 'text',
			)
		);
	}

	/*
	 * 4. WooCommerce Shop & Product Layout Section
	 */
	$wp_customize->add_section(
		'cet_section_shop',
		array(
			'title'    => esc_html__( '4. WooCommerce Shop & Product Settings', 'custom-ecommerce-theme' ),
			'panel'    => 'cet_theme_panel',
			'priority' => 40,
		)
	);

	$wp_customize->add_setting(
		'shop_sidebar_layout',
		array(
			'default'           => $defaults['shop_sidebar_layout'],
			'sanitize_callback' => 'cet_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'shop_sidebar_layout',
		array(
			'label'   => esc_html__( 'Shop Archive Sidebar Layout', 'custom-ecommerce-theme' ),
			'section' => 'cet_section_shop',
			'type'    => 'select',
			'choices' => array(
				'left-sidebar'  => esc_html__( 'Left Filter Sidebar', 'custom-ecommerce-theme' ),
				'right-sidebar' => esc_html__( 'Right Filter Sidebar', 'custom-ecommerce-theme' ),
				'full-width'    => esc_html__( 'Full Width (Collapsible Filter Drawer)', 'custom-ecommerce-theme' ),
			),
		)
	);

	$wp_customize->add_setting(
		'shop_default_columns',
		array(
			'default'           => $defaults['shop_default_columns'],
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'shop_default_columns',
		array(
			'label'       => esc_html__( 'Default Product Grid Columns (2–5)', 'custom-ecommerce-theme' ),
			'section'     => 'cet_section_shop',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 2,
				'max'  => 5,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'free_shipping_threshold',
		array(
			'default'           => $defaults['free_shipping_threshold'],
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'free_shipping_threshold',
		array(
			'label'   => esc_html__( 'Free Shipping Goal Bar Amount (0 to disable)', 'custom-ecommerce-theme' ),
			'section' => 'cet_section_shop',
			'type'    => 'number',
		)
	);

	$shop_checkboxes = array(
		'product_card_show_rating'    => esc_html__( 'Show Star Rating on Product Cards', 'custom-ecommerce-theme' ),
		'product_card_show_category'  => esc_html__( 'Show Primary Category on Product Cards', 'custom-ecommerce-theme' ),
		'product_card_show_quickview' => esc_html__( 'Enable Quick View Button on Product Cards', 'custom-ecommerce-theme' ),
		'product_card_show_wishlist'  => esc_html__( 'Enable Wishlist Heart Button on Product Cards', 'custom-ecommerce-theme' ),
		'product_card_show_compare'   => esc_html__( 'Enable Product Compare Button on Product Cards', 'custom-ecommerce-theme' ),
		'single_product_buy_now'      => esc_html__( 'Enable Instant "Buy Now" Button on Single Product', 'custom-ecommerce-theme' ),
		'single_product_sticky_bar'   => esc_html__( 'Enable Sticky Add-to-Cart Bar on Single Product', 'custom-ecommerce-theme' ),
	);

	foreach ( $shop_checkboxes as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $setting_id ],
				'sanitize_callback' => 'cet_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'cet_section_shop',
				'type'    => 'checkbox',
			)
		);
	}

	/*
	 * 5. Footer & Contact Information
	 */
	$wp_customize->add_section(
		'cet_section_footer',
		array(
			'title'    => esc_html__( '5. Footer, Contact & Social Links', 'custom-ecommerce-theme' ),
			'panel'    => 'cet_theme_panel',
			'priority' => 50,
		)
	);

	$footer_fields = array(
		'footer_about_text' => esc_html__( 'Footer Brand Summary', 'custom-ecommerce-theme' ),
		'footer_phone'      => esc_html__( 'Customer Concierge Phone', 'custom-ecommerce-theme' ),
		'footer_email'      => esc_html__( 'Customer Concierge Email', 'custom-ecommerce-theme' ),
		'footer_address'    => esc_html__( 'Store / Atelier Address', 'custom-ecommerce-theme' ),
		'social_instagram'  => esc_html__( 'Instagram URL', 'custom-ecommerce-theme' ),
		'social_facebook'   => esc_html__( 'Facebook URL', 'custom-ecommerce-theme' ),
		'social_x'          => esc_html__( 'X (Twitter) URL', 'custom-ecommerce-theme' ),
		'social_pinterest'  => esc_html__( 'Pinterest URL', 'custom-ecommerce-theme' ),
	);

	foreach ( $footer_fields as $setting_id => $label ) {
		$sanitize_fn = ( 0 === strpos( $setting_id, 'social_' ) ) ? 'esc_url_raw' : 'sanitize_text_field';
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $setting_id ],
				'sanitize_callback' => $sanitize_fn,
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'cet_section_footer',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'cet_customize_register' );

/**
 * Generate dynamic CSS variables from Customizer settings.
 *
 * @return string
 */
function cet_get_customizer_Dynamic_css(): string {
	$primary    = sanitize_hex_color( (string) cet_get_option( 'color_primary' ) ) ?: '#14243A';
	$accent     = sanitize_hex_color( (string) cet_get_option( 'color_accent' ) ) ?: '#D6AD65';
	$secondary  = sanitize_hex_color( (string) cet_get_option( 'color_secondary' ) ) ?: '#356AE6';
	$background = sanitize_hex_color( (string) cet_get_option( 'color_background' ) ) ?: '#F5F7FA';
	$text       = sanitize_hex_color( (string) cet_get_option( 'color_text' ) ) ?: '#202938';
	$border     = sanitize_hex_color( (string) cet_get_option( 'color_border' ) ) ?: '#E8EDF5';
	$width      = absint( cet_get_option( 'container_width' ) ) ?: 1280;

	return sprintf(
		':root { --color-primary: %1$s; --color-accent: %2$s; --color-secondary: %3$s; --color-background: %4$s; --color-text: %5$s; --color-border: %6$s; --container-max-width: %7$dpx; }',
		esc_attr( $primary ),
		esc_attr( $accent ),
		esc_attr( $secondary ),
		esc_attr( $background ),
		esc_attr( $text ),
		esc_attr( $border ),
		$width
	);
}

