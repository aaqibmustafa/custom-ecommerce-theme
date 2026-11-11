<?php
/**
 * Template Name: Client Account Portal
 * Template Post Type: page
 *
 * Dedicated front-end Client Account, Sign In, Registration, and Order Tracking template.
 * Keeps shoppers completely inside the branded theme instead of wp-login.php.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'cet_breadcrumbs' ) ) {
	cet_breadcrumbs();
}
?>

<main id="primary" class="site-main cet-main">
	<div class="cet-container" style="padding-top:2rem; padding-bottom:3.5rem;">
		<?php echo do_shortcode( '[cet_account]' ); ?>
	</div>
</main><!-- #primary -->

<?php
get_footer();

