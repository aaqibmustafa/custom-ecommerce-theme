<?php
/**
 * Template Name: Checkout & Delivery Page
 * Template Post Type: page
 *
 * Responsive two-column Delivery Destination & Payment Method Checkout page.
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
	<div class="cet-container">
		<header class="cet-section-header">
			<div>
				<span class="cet-eyebrow"><?php esc_html_e( 'Encrypted Client Checkout', 'custom-ecommerce-theme' ); ?></span>
				<h1 class="cet-section-title"><?php the_title(); ?></h1>
			</div>
		</header>

		<?php echo do_shortcode( '[cet_checkout]' ); ?>
	</div>
</main><!-- #primary -->

<?php
get_footer();

