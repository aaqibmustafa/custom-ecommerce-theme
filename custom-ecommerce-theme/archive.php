<?php
/**
 * The template for displaying archive pages (categories, tags, authors, dates)
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
		<header class="page-header cet-section-header">
			<div>
				<span class="cet-eyebrow"><?php esc_html_e( 'Archive Collection', 'custom-ecommerce-theme' ); ?></span>
				<?php
				the_archive_title( '<h1 class="page-title cet-section-title">', '</h1>' );
				the_archive_description( '<div class="archive-description cet-section-subtitle">', '</div>' );
				?>
			</div>
		</header>

		<div class="<?php echo $has_sidebar ? 'cet-layout-with-sidebar' : 'cet-layout-full-width'; ?>">
			<div>
				<?php if ( have_posts() ) : ?>
					<div class="cet-grid-cols-2">
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

			<?php if ( $has_sidebar ) : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</div>
</main><!-- #primary -->

<?php
get_footer();
