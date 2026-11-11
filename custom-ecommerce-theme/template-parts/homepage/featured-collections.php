<?php
/**
 * Homepage Section: Featured Collections & Promotional Discount Banners
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_collections_enable', true ) ) {
	return;
}

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

$featured_products = array();
if ( function_exists( 'wc_get_products' ) ) {
	$featured_products = wc_get_products(
		array(
			'status'   => 'publish',
			'featured' => true,
			'limit'    => 4,
		)
	);
}
?>
<section class="cet-section cet-section--surface" id="featured-collections">
	<div class="cet-container">
		<div class="cet-section-header">
			<div>
				<span class="cet-eyebrow"><?php esc_html_e( 'Seasonal Lookbook', 'custom-ecommerce-theme' ); ?></span>
				<h2 class="cet-section-title"><?php echo esc_html( (string) cet_get_option( 'home_collections_title' ) ); ?></h2>
			</div>
		</div>

		<div class="cet-grid-cols-3" style="margin-bottom: 3rem;">
			<div class="cet-promo-card cet-promo-card--dark">
				<span class="cet-eyebrow"><?php esc_html_e( 'Collection 01', 'custom-ecommerce-theme' ); ?></span>
				<h3><?php esc_html_e( 'Midnight Navy Sovereign Series', 'custom-ecommerce-theme' ); ?></h3>
				<p style="opacity:0.85;"><?php esc_html_e( 'Architectural silhouettes paired with brushed Champagne Gold hardware.', 'custom-ecommerce-theme' ); ?></p>
				<div style="margin-top:auto;">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--gold cet-btn--sm">
						<?php esc_html_e( 'Explore Series', 'custom-ecommerce-theme' ); ?>
					</a>
				</div>
			</div>

			<div class="cet-promo-card">
				<span class="cet-eyebrow"><?php esc_html_e( 'Collection 02', 'custom-ecommerce-theme' ); ?></span>
				<h3><?php esc_html_e( 'The Executive Travel Capsule', 'custom-ecommerce-theme' ); ?></h3>
				<p style="color:var(--color-text-muted);"><?php esc_html_e( 'Engineered for seamless global transit and uncompromising durability.', 'custom-ecommerce-theme' ); ?></p>
				<div style="margin-top:auto;">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--primary cet-btn--sm">
						<?php esc_html_e( 'Shop Capsule', 'custom-ecommerce-theme' ); ?>
					</a>
				</div>
			</div>

			<div class="cet-promo-card">
				<span class="cet-eyebrow"><?php esc_html_e( 'Collection 03', 'custom-ecommerce-theme' ); ?></span>
				<h3><?php esc_html_e( 'Artisanal Gift Editions', 'custom-ecommerce-theme' ); ?></h3>
				<p style="color:var(--color-text-muted);"><?php esc_html_e( 'Presented in bespoke presentation boxes with personalized monogramming.', 'custom-ecommerce-theme' ); ?></p>
				<div style="margin-top:auto;">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--outline cet-btn--sm">
						<?php esc_html_e( 'Browse Gifts', 'custom-ecommerce-theme' ); ?>
					</a>
				</div>
			</div>
		</div>

		<?php if ( ! empty( $featured_products ) ) : ?>
			<div class="cet-products-grid">
				<?php
				foreach ( $featured_products as $f_prod ) {
					set_query_var( 'cet_card_product', $f_prod );
					get_template_part( 'template-parts/product/product-card' );
				}
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
