<?php
/**
 * The Template for displaying all single products
 *
 * Includes full gallery, variations, specifications, related/upsell products,
 * recently viewed products, and a sticky bottom Add-to-Cart bar.
 *
 * @package Custom_Ecommerce_Theme
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
	the_post();
	global $product;
	?>
	<div class="cet-single-product-card">
		<?php wc_get_template_part( 'content', 'single-product' ); ?>
	</div>

	<?php
	// Recently Viewed Products on Single Product Page.
	$recent_ids = function_exists( 'cet_get_recently_viewed_ids' ) ? array_diff( cet_get_recently_viewed_ids(), array( get_the_ID() ) ) : array();
	$recent_ids = array_slice( $recent_ids, 0, 4 );
	if ( ! empty( $recent_ids ) && function_exists( 'wc_get_product' ) ) :
		?>
		<section class="cet-section" style="padding-bottom:0;">
			<div class="cet-section-header">
				<div>
					<span class="cet-eyebrow"><?php esc_html_e( 'Your History', 'custom-ecommerce-theme' ); ?></span>
					<h2 class="cet-section-title"><?php esc_html_e( 'Recently Viewed Pieces', 'custom-ecommerce-theme' ); ?></h2>
				</div>
			</div>
			<div class="cet-products-grid">
				<?php
				foreach ( $recent_ids as $rv_id ) {
					$rv_prod = wc_get_product( $rv_id );
					if ( $rv_prod && $rv_prod->is_visible() ) {
						set_query_var( 'cet_card_product', $rv_prod );
						get_template_part( 'template-parts/product/product-card' );
					}
				}
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( cet_get_option( 'single_product_sticky_bar', true ) && $product instanceof WC_Product && $product->is_in_stock() ) : ?>
		<div id="cet-sticky-product-bar" class="cet-sticky-product-bar" aria-hidden="true">
			<div class="cet-container cet-sticky-product-bar__inner">
				<div style="display:flex; align-items:center; gap:1rem;">
					<strong style="font-family:var(--font-heading); font-size:1.05rem;"><?php echo esc_html( $product->get_name() ); ?></strong>
					<span style="color:var(--color-accent); font-weight:700;"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
				</div>
				<a href="#primary" class="cet-btn cet-btn--gold cet-btn--sm">
					<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>

<?php endwhile; ?>

<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );

