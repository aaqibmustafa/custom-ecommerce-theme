<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * @package Custom_Ecommerce_Theme
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

$sidebar_layout = (string) cet_get_option( 'shop_sidebar_layout', 'left-sidebar' );
$current_cols   = function_exists( 'cet_loop_shop_columns' ) ? cet_loop_shop_columns() : 4;
$view_mode      = isset( $_GET['view'] ) && 'list' === $_GET['view'] ? 'list' : 'grid'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

do_action( 'woocommerce_before_main_content' );
?>

<header class="woocommerce-products-header cet-shop-header">
	<span class="cet-eyebrow"><?php esc_html_e( 'Official Boutique Catalog', 'custom-ecommerce-theme' ); ?></span>
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php do_action( 'woocommerce_archive_description' ); ?>
</header>

<?php
$layout_class = 'cet-layout-full-width';
if ( 'left-sidebar' === $sidebar_layout ) {
	$layout_class = 'cet-layout-with-sidebar cet-layout-with-sidebar--left';
} elseif ( 'right-sidebar' === $sidebar_layout ) {
	$layout_class = 'cet-layout-with-sidebar';
}
?>

<div class="<?php echo esc_attr( $layout_class ); ?>">
	<?php if ( 'left-sidebar' === $sidebar_layout ) : ?>
		<?php get_sidebar( 'shop' ); ?>
	<?php endif; ?>

	<div class="cet-wc-products-wrap <?php echo 'list' === $view_mode ? 'cet-products-view-list' : 'cet-products-view-grid'; ?>" id="cet-shop-products-container">
		<?php if ( woocommerce_product_loop() ) : ?>

			<div class="cet-shop-toolbar">
				<div class="cet-shop-toolbar__left">
					<?php woocommerce_result_count(); ?>
				</div>

				<div class="cet-shop-toolbar__right">
					<div class="cet-view-switcher" role="group" aria-label="<?php esc_attr_e( 'Product Layout Switcher', 'custom-ecommerce-theme' ); ?>">
						<button type="button" class="cet-view-btn <?php echo 'grid' === $view_mode ? 'is-active' : ''; ?>" data-shop-view="grid" aria-label="<?php esc_attr_e( 'Grid View', 'custom-ecommerce-theme' ); ?>">
							<?php echo cet_get_svg_icon( 'grid', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
						<button type="button" class="cet-view-btn <?php echo 'list' === $view_mode ? 'is-active' : ''; ?>" data-shop-view="list" aria-label="<?php esc_attr_e( 'List View', 'custom-ecommerce-theme' ); ?>">
							<?php echo cet_get_svg_icon( 'list', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					</div>

					<?php woocommerce_catalog_ordering(); ?>
				</div>
			</div>

			<?php
			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			do_action( 'woocommerce_after_shop_loop' );
		else :
			do_action( 'woocommerce_no_products_found' );
		endif;
		?>
	</div>

	<?php if ( 'right-sidebar' === $sidebar_layout ) : ?>
		<?php get_sidebar( 'shop' ); ?>
	<?php endif; ?>
</div>

<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
