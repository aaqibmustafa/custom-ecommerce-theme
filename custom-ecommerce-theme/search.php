<?php
/**
 * The template for displaying search results pages
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
		<?php if ( have_posts() ) : ?>
			<header class="page-header cet-section-header">
				<div>
					<span class="cet-eyebrow"><?php esc_html_e( 'Catalog & Site Search', 'custom-ecommerce-theme' ); ?></span>
					<h1 class="page-title cet-section-title">
						<?php
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'custom-ecommerce-theme' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
						?>
					</h1>
				</div>
			</header>

			<div class="cet-grid-cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					if ( 'product' === get_post_type() && function_exists( 'wc_get_product' ) ) {
						$prod_obj = wc_get_product( get_the_ID() );
						if ( $prod_obj ) {
							set_query_var( 'cet_card_product', $prod_obj );
							get_template_part( 'template-parts/product/product-card' );
							continue;
						}
					}
					get_template_part( 'template-parts/content/content', 'search' );
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
