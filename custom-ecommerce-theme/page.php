<?php
/**
 * The template for displaying all pages
 *
 * Also handles WooCommerce Cart, Checkout, and My Account pages seamlessly.
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
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content/content', 'page' );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>
	</div>
</main><!-- #primary -->

<?php
get_footer();
