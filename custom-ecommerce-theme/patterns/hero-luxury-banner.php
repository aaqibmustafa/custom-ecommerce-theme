<?php
/**
 * Title: Luxury Midnight Navy & Champagne Gold Hero Banner
 * Slug: custom-ecommerce-theme/hero-luxury-banner
 * Categories: featured, banner
 * Description: Signature Midnight Navy hero showcase with Champagne Gold typography and call-to-action buttons.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"16px","color":"#D6AD65","width":"1px"}},"backgroundColor":"midnight-navy","textColor":"pure-white","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-pure-white-color has-midnight-navy-background-color has-text-color has-background" style="border-color:#D6AD65;border-width:1px;border-radius:16px;padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.14em","fontWeight":"700"}},"textColor":"champagne-gold","fontSize":"small"} -->
	<p class="has-champagne-gold-color has-text-color has-small-font-size" style="font-weight:700;letter-spacing:0.14em;text-transform:uppercase"><?php esc_html_e( 'Signature Atelier Collection', 'custom-ecommerce-theme' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1,"textColor":"pure-white","fontSize":"xx-large"} -->
	<h1 class="wp-block-heading has-pure-white-color has-text-color has-xx-large-font-size"><?php esc_html_e( 'Refined Craftsmanship in Midnight Navy & Champagne Gold', 'custom-ecommerce-theme' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"textColor":"cloud-white","fontSize":"medium"} -->
	<p class="has-cloud-white-color has-text-color has-medium-font-size"><?php esc_html_e( 'Explore timeless silhouettes, bespoke details, and enduring materials designed for modern collectors.', 'custom-ecommerce-theme' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"champagne-gold","textColor":"midnight-navy","style":{"typography":{"fontWeight":"700"}}} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-midnight-navy-color has-champagne-gold-background-color has-text-color has-background wp-element-button" style="font-weight:700"><?php esc_html_e( 'Explore the Collection', 'custom-ecommerce-theme' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
