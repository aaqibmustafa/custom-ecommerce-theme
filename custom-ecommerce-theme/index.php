<?php
/**
 * The main template file
 *
 * Fallback template in the WordPress template hierarchy.
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
	<div class="cet-container cet-content-area">
		<?php if ( have_posts() ) : ?>

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header class="page-header cet-section-header">
					<h1 class="page-title cet-section-title"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>

			<div class="cet-grid-cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', get_post_type() );
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => esc_html__( 'Previous', 'custom-ecommerce-theme' ),
					'next_text' => esc_html__( 'Next', 'custom-ecommerce-theme' ),
				)
			);

		else :
			get_template_part( 'template-parts/content/content', 'none' );
		endif;
		?>
	</div>
</main><!-- #primary -->

<?php
get_footer();
