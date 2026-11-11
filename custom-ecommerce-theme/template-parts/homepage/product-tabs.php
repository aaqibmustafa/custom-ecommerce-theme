<?php
/**
 * Homepage Section: Interactive Product Tabs (Trending, Best-Selling, New Arrivals)
 *
 * Uses native `wc_get_products()` queries.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_product_tabs_enable', true ) || ! function_exists( 'wc_get_products' ) ) {
	return;
}

$limit    = absint( cet_get_option( 'home_product_tabs_count', 8 ) ) ?: 8;
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

// 1. Trending Products (Featured first, or highest average rating / recent).
$trending_products = wc_get_products(
	array(
		'status'  => 'publish',
		'limit'   => $limit,
		'orderby' => 'date',
		'order'   => 'DESC',
	)
);

// 2. Best-Selling Products (Ordered by total_sales meta via wc_get_products).
$bestselling_products = wc_get_products(
	array(
		'status'   => 'publish',
		'limit'    => $limit,
		'meta_key' => 'total_sales', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'orderby'  => 'meta_value_num',
		'order'    => 'DESC',
	)
);

// 3. New Arrivals.
$new_arrivals = wc_get_products(
	array(
		'status'  => 'publish',
		'limit'   => $limit,
		'orderby' => 'ID',
		'order'   => 'DESC',
	)
);
?>
<section class="cet-section" id="signature-products">
	<div class="cet-container">
		<div class="cet-section-header">
			<div>
				<span class="cet-eyebrow"><?php esc_html_e( 'Curated Catalog', 'custom-ecommerce-theme' ); ?></span>
				<h2 class="cet-section-title"><?php echo esc_html( (string) cet_get_option( 'home_product_tabs_title' ) ); ?></h2>
			</div>

			<div class="cet-tabs-nav" role="tablist" aria-label="<?php esc_attr_e( 'Product Collections', 'custom-ecommerce-theme' ); ?>">
				<button type="button" class="cet-tab-btn is-active" role="tab" aria-selected="true" aria-controls="cet-tab-trending" data-tab-target="cet-tab-trending">
					<?php esc_html_e( 'Trending Now', 'custom-ecommerce-theme' ); ?>
				</button>
				<button type="button" class="cet-tab-btn" role="tab" aria-selected="false" aria-controls="cet-tab-bestsellers" data-tab-target="cet-tab-bestsellers">
					<?php esc_html_e( 'Best Sellers', 'custom-ecommerce-theme' ); ?>
				</button>
				<button type="button" class="cet-tab-btn" role="tab" aria-selected="false" aria-controls="cet-tab-new" data-tab-target="cet-tab-new">
					<?php esc_html_e( 'New Arrivals', 'custom-ecommerce-theme' ); ?>
				</button>
			</div>
		</div>

		<?php if ( empty( $trending_products ) ) : ?>
			<div class="cet-empty-state" style="background:var(--color-surface); padding:2.5rem; border:1px solid var(--color-border); border-radius:var(--radius-md); text-align:center;">
				<h3><?php esc_html_e( 'Ready for Your Products', 'custom-ecommerce-theme' ); ?></h3>
				<p><?php esc_html_e( 'Add products in WooCommerce > Products to automatically populate Trending, Best Sellers, and New Arrivals.', 'custom-ecommerce-theme' ); ?></p>
				<?php if ( current_user_can( 'manage_woocommerce' ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="cet-btn cet-btn--gold">
						<?php esc_html_e( 'Add First WooCommerce Product', 'custom-ecommerce-theme' ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<!-- Tab 1: Trending -->
			<div id="cet-tab-trending" class="cet-tab-panel is-active" role="tabpanel">
				<div class="cet-products-grid">
					<?php
					foreach ( $trending_products as $prod ) {
						set_query_var( 'cet_card_product', $prod );
						get_template_part( 'template-parts/product/product-card' );
					}
					?>
				</div>
			</div>

			<!-- Tab 2: Best Sellers -->
			<div id="cet-tab-bestsellers" class="cet-tab-panel" role="tabpanel">
				<div class="cet-products-grid">
					<?php
					foreach ( $bestselling_products as $prod ) {
						set_query_var( 'cet_card_product', $prod );
						get_template_part( 'template-parts/product/product-card' );
					}
					?>
				</div>
			</div>

			<!-- Tab 3: New Arrivals -->
			<div id="cet-tab-new" class="cet-tab-panel" role="tabpanel">
				<div class="cet-products-grid">
					<?php
					foreach ( $new_arrivals as $prod ) {
						set_query_var( 'cet_card_product', $prod );
						get_template_part( 'template-parts/product/product-card' );
					}
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

