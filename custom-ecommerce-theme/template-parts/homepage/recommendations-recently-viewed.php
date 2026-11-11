<?php
/**
 * Homepage Section: Recommended Products & Recently Viewed Products
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_recommendations_enable', true ) || ! function_exists( 'wc_get_products' ) ) {
	return;
}

// 1. Recommended Products (Top rated or on-sale products).
$recommended_products = wc_get_products(
	array(
		'status'  => 'publish',
		'limit'   => 4,
		'orderby' => 'rand',
	)
);

// 2. Recently Viewed Products from visitor cookie.
$recent_ids      = function_exists( 'cet_get_recently_viewed_ids' ) ? array_slice( cet_get_recently_viewed_ids(), 0, 4 ) : array();
$recent_products = array();
foreach ( $recent_ids as $r_id ) {
	$r_prod = wc_get_product( $r_id );
	if ( $r_prod && $r_prod->is_visible() ) {
		$recent_products[] = $r_prod;
	}
}

if ( empty( $recommended_products ) && empty( $recent_products ) ) {
	return;
}
?>
<section class="cet-section" id="recommended-products">
	<div class="cet-container">
		<?php if ( ! empty( $recommended_products ) ) : ?>
			<div class="cet-section-header">
				<div>
					<span class="cet-eyebrow"><?php esc_html_e( 'Tailored For You', 'custom-ecommerce-theme' ); ?></span>
					<h2 class="cet-section-title"><?php esc_html_e( 'Curator Recommendations', 'custom-ecommerce-theme' ); ?></h2>
				</div>
			</div>
			<div class="cet-products-grid" style="margin-bottom: <?php echo ! empty( $recent_products ) ? '4rem' : '0'; ?>;">
				<?php
				foreach ( $recommended_products as $rec_prod ) {
					set_query_var( 'cet_card_product', $rec_prod );
					get_template_part( 'template-parts/product/product-card' );
				}
				?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $recent_products ) ) : ?>
			<div class="cet-section-header">
				<div>
					<span class="cet-eyebrow"><?php esc_html_e( 'Your Browsing History', 'custom-ecommerce-theme' ); ?></span>
					<h2 class="cet-section-title"><?php esc_html_e( 'Recently Viewed Pieces', 'custom-ecommerce-theme' ); ?></h2>
				</div>
			</div>
			<div class="cet-products-grid">
				<?php
				foreach ( $recent_products as $rv_prod ) {
					set_query_var( 'cet_card_product', $rv_prod );
					get_template_part( 'template-parts/product/product-card' );
				}
				?>
			</div>
		<?php endif; ?>
	</div>
</section>

