<?php
/**
 * Template Name: Shop Catalog Page
 * Template Post Type: page
 *
 * Full-width responsive e-commerce catalog page supporting category filters & sorting.
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
		<div class="cet-shop-header">
			<span class="cet-eyebrow"><?php esc_html_e( 'Boutique Catalog', 'custom-ecommerce-theme' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<p style="color:rgba(245,247,250,0.88); margin:0;">
				<?php esc_html_e( 'Curated luxury pieces crafted with Midnight Navy poise and Champagne Gold distinction.', 'custom-ecommerce-theme' ); ?>
			</p>
		</div>

		<?php echo do_shortcode( '[cet_shop]' ); ?>
	</div>
</main><!-- #primary -->

<?php
get_footer();

