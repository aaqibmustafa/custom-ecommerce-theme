<?php
/**
 * The template for displaying all single posts
 *
 * Includes post content, post navigation, related posts, and comments.
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

$has_sidebar = is_active_sidebar( 'sidebar-blog' ) && 'full-width' !== cet_get_option( 'blog_sidebar_layout', 'right-sidebar' );
?>

<main id="primary" class="site-main cet-main">
	<div class="cet-container">
		<div class="<?php echo $has_sidebar ? 'cet-layout-with-sidebar' : 'cet-layout-full-width'; ?>">
			<div>
				<?php
				while ( have_posts() ) :
					the_post();

					get_template_part( 'template-parts/content/content', 'single' );

					the_post_navigation(
						array(
							'prev_text' => '<span class="cet-eyebrow">' . esc_html__( 'Previous Article', 'custom-ecommerce-theme' ) . '</span> <strong>%title</strong>',
							'next_text' => '<span class="cet-eyebrow">' . esc_html__( 'Next Article', 'custom-ecommerce-theme' ) . '</span> <strong>%title</strong>',
						)
					);

					// Related Posts Section.
					if ( cet_get_option( 'blog_show_related_posts', true ) ) {
						$categories = wp_get_post_categories( get_the_ID() );
						if ( ! empty( $categories ) ) {
							$related_query = new WP_Query(
								array(
									'category__in'        => $categories,
									'post__not_in'        => array( get_the_ID() ),
									'posts_per_page'      => 2,
									'ignore_sticky_posts' => true,
								)
							);
							if ( $related_query->have_posts() ) :
								?>
								<section class="cet-related-posts" style="margin-top:2.5rem;">
									<h3 class="cet-section-title" style="margin-bottom:1.25rem;"><?php esc_html_e( 'Related Journal Articles', 'custom-ecommerce-theme' ); ?></h3>
									<div class="cet-grid-cols-2">
										<?php
										while ( $related_query->have_posts() ) :
											$related_query->the_post();
											get_template_part( 'template-parts/content/content' );
										endwhile;
										wp_reset_postdata();
										?>
									</div>
								</section>
								<?php
							endif;
						}
					}

					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

				endwhile;
				?>
			</div>

			<?php if ( $has_sidebar ) : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</div>
</main><!-- #primary -->

<?php
get_footer();

