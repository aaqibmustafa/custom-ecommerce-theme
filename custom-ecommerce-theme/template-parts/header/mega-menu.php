<?php
/**
 * Department Mega Menu Panel Template Part
 *
 * Dynamically queries WooCommerce product categories (`product_cat`) when available,
 * or displays the 6 curated luxury departments when the catalog is newly installed.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'enable_mega_menu', true ) ) {
	return;
}

$product_categories = array();
if ( taxonomy_exists( 'product_cat' ) ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'parent'     => 0,
			'number'     => 6,
			'exclude'    => array( (int) get_option( 'default_product_cat', 0 ) ),
		)
	);
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		$product_categories = $terms;
	}
}

$shop_url = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'shop' ) : home_url( '/#signature-products' );
?>
<div class="cet-mega-trigger-wrap">
	<button type="button" class="cet-mega-trigger" aria-expanded="false" aria-controls="cet-mega-menu-panel">
		<?php echo cet_get_svg_icon( 'grid', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span><?php esc_html_e( 'Shop Departments', 'custom-ecommerce-theme' ); ?></span>
		<?php echo cet_get_svg_icon( 'chevron-down', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>

	<div id="cet-mega-menu-panel" class="cet-mega-panel" role="region" aria-label="<?php esc_attr_e( 'Store Departments Mega Menu', 'custom-ecommerce-theme' ); ?>">
		<div class="cet-mega-cats-col">
			<span class="cet-eyebrow"><?php esc_html_e( 'Curated Departments', 'custom-ecommerce-theme' ); ?></span>
			<div class="cet-mega-cats-grid">
				<?php if ( ! empty( $product_categories ) ) : ?>
					<?php foreach ( $product_categories as $cat_term ) : ?>
						<?php $term_link = get_term_link( $cat_term ); ?>
						<a href="<?php echo esc_url( is_wp_error( $term_link ) ? $shop_url : $term_link ); ?>" class="cet-mega-cat-item">
							<div>
								<h4 class="cet-mega-cat-item__title"><?php echo esc_html( $cat_term->name ); ?></h4>
								<span class="cet-mega-cat-item__meta">
									<?php
									printf(
										/* translators: %d: Number of products in category. */
										esc_html( _n( '%d Piece Available', '%d Pieces Available', (int) $cat_term->count, 'custom-ecommerce-theme' ) ),
										(int) $cat_term->count
									);
									?>
								</span>
							</div>
						</a>
					<?php endforeach; ?>
				<?php elseif ( function_exists( 'cet_get_showcase_categories' ) ) : ?>
					<?php foreach ( cet_get_showcase_categories() as $s_cat ) : ?>
						<a href="<?php echo esc_url( (string) $s_cat['url'] ); ?>" class="cet-mega-cat-item">
							<div>
								<h4 class="cet-mega-cat-item__title"><?php echo esc_html( (string) $s_cat['name'] ); ?></h4>
								<span class="cet-mega-cat-item__meta">
									<?php
									printf(
										/* translators: %d: Number of pieces. */
										esc_html__( '%d Curated Pieces', 'custom-ecommerce-theme' ),
										(int) $s_cat['count']
									);
									?>
								</span>
							</div>
						</a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="cet-mega-spotlight">
			<span class="cet-eyebrow"><?php esc_html_e( 'Signature Edition', 'custom-ecommerce-theme' ); ?></span>
			<h4><?php esc_html_e( 'The Champagne Gold Capsule', 'custom-ecommerce-theme' ); ?></h4>
			<p><?php esc_html_e( 'Hand-selected best sellers and limited seasonal pieces crafted with timeless refinement.', 'custom-ecommerce-theme' ); ?></p>
			<div>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--gold cet-btn--sm">
					<?php esc_html_e( 'Explore Boutique', 'custom-ecommerce-theme' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>
