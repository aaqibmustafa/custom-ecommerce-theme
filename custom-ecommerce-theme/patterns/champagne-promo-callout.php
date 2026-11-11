<?php
/**
 * Title: Champagne Gold Privilege Callout Banner
 * Slug: custom-ecommerce-theme/champagne-promo-callout
 * Categories: call-to-action, featured
 * Description: Promotional callout card highlighted with a Champagne Gold border and Midnight Navy CTA.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"12px","color":"#D6AD65","width":"2px"}},"backgroundColor":"pure-white","textColor":"graphite","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-graphite-color has-pure-white-background-color has-text-color has-background" style="border-color:#D6AD65;border-width:2px;border-radius:12px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"textAlign":"center","textColor":"midnight-navy"} -->
	<h2 class="wp-block-heading has-text-align-center has-midnight-navy-color has-text-color"><?php esc_html_e( 'Complimentary White-Glove Gift Presentation', 'custom-ecommerce-theme' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center"><?php esc_html_e( 'Every order arrives in our signature Midnight Navy keepsake box with gold-foil embossing and a personalized note.', 'custom-ecommerce-theme' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

