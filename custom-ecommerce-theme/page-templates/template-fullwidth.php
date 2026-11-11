<?php
/**
 * Template Name: Full Width Luxury Layout
 * Template Post Type: page, post
 *
 * Wide container layout for Lookbooks, Gutenberg Block Patterns, and landing pages.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'cet_breadcrumbs' ) && ! is_front_page() ) {
	cet_breadcrumbs();
}
?>

<main id="primary" class="site-main cet-main">
	<div class="cet-container cet-container--wide">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
		?>
	</div>
</main><!-- #primary -->

<?php
get_footer();

