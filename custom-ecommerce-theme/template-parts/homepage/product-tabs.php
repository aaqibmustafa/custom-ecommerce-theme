<?php
/**
 * Homepage Section: Interactive Product Tabs (Trending, Best-Selling, New Arrivals)
 *
 * Uses native `wc_get_products()` queries when WooCommerce products exist,
 * and seamlessly renders the 8 luxury showcase products when the store is newly installed.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_product_tabs_enable', true ) ) {
	return;
}

$limit                = absint( cet_get_option( 'home_product_tabs_count', 8 ) ) ?: 8;
$trending_products    = array();
$bestselling_products = array();
$new_arrivals         = array();

if ( function_exists( 'wc_get_products' ) ) {
	$trending_products = wc_get_products(
		array(
			'status'  => 'publish',
			'limit'   => $limit,
			'orderby' => 'date',
			'order'   => 'DESC',
		)
	);

	if ( ! empty( $trending_products ) ) {
		$bestselling_products = wc_get_products(
			array(
				'status'   => 'publish',
				'limit'    => $limit,
				'meta_key' => 'total_sales', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'orderby'  => 'meta_value_num',
				'order'    => 'DESC',
			)
		);

		// If no products have sales yet (e.g. freshly created products in wp-admin), fallback to top-rated or trending so the tab is never blank.
		if ( empty( $bestselling_products ) ) {
			$bestselling_products = wc_get_products(
				array(
					'status'  => 'publish',
					'limit'   => $limit,
					'orderby' => 'rating',
					'order'   => 'DESC',
				)
			);
			if ( empty( $bestselling_products ) ) {
				$bestselling_products = $trending_products;
			}
		}

		$new_arrivals = wc_get_products(
			array(
				'status'  => 'publish',
				'limit'   => $limit,
				'orderby' => 'ID',
				'order'   => 'DESC',
			)
		);

		if ( empty( $new_arrivals ) ) {
			$new_arrivals = $trending_products;
		}
	}
}

$showcase_items = function_exists( 'cet_get_showcase_products' ) ? cet_get_showcase_products() : array();
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

		<?php if ( ! empty( $trending_products ) ) : ?>
			<!-- Live WooCommerce Products -->
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
		<?php else : ?>
			<!-- Built-in Luxury Showcase Products (Displayed automatically until WooCommerce products are added) -->
			<div id="cet-tab-trending" class="cet-tab-panel is-active" role="tabpanel">
				<div class="cet-products-grid">
					<?php
					foreach ( $showcase_items as $s_item ) {
						cet_render_showcase_product_card( $s_item );
					}
					?>
				</div>
			</div>

			<div id="cet-tab-bestsellers" class="cet-tab-panel" role="tabpanel">
				<div class="cet-products-grid">
					<?php
					foreach ( $showcase_items as $s_item ) {
						if ( in_array( 'bestsellers', (array) $s_item['tab'], true ) ) {
							cet_render_showcase_product_card( $s_item );
						}
					}
					?>
				</div>
			</div>

			<div id="cet-tab-new" class="cet-tab-panel" role="tabpanel">
				<div class="cet-products-grid">
					<?php
					foreach ( $showcase_items as $s_item ) {
						if ( in_array( 'new', (array) $s_item['tab'], true ) ) {
							cet_render_showcase_product_card( $s_item );
						}
					}
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
