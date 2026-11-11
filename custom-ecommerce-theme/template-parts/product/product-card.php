<?php
/**
 * Reusable WooCommerce Product Card Component
 *
 * Uses native WC_Product methods and Customizer settings.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$card_product = get_query_var( 'cet_card_product', null );
if ( $card_product instanceof WC_Product ) {
	$product_obj = $card_product;
} elseif ( $product instanceof WC_Product ) {
	$product_obj = $product;
} else {
	return;
}

if ( ! $product_obj->is_visible() ) {
	return;
}

$product_id    = $product_obj->get_id();
$permalink     = $product_obj->get_permalink();
$title         = $product_obj->get_name();
$is_on_sale    = $product_obj->is_on_sale();
$is_featured   = $product_obj->is_featured();
$total_sales   = (int) $product_obj->get_total_sales();
$rating_count  = $product_obj->get_rating_count();
$avg_rating    = (float) $product_obj->get_average_rating();
$wishlist_ids  = function_exists( 'cet_get_wishlist_ids' ) ? cet_get_wishlist_ids() : array();
$in_wishlist   = in_array( $product_id, $wishlist_ids, true );

// Calculate Sale Percentage when applicable.
$sale_percent = 0;
if ( $is_on_sale && $product_obj->is_type( 'simple' ) ) {
	$reg_price  = (float) $product_obj->get_regular_price();
	$sale_price = (float) $product_obj->get_sale_price();
	if ( $reg_price > 0 && $sale_price > 0 && $reg_price > $sale_price ) {
		$sale_percent = (int) round( ( ( $reg_price - $sale_price ) / $reg_price ) * 100 );
	}
}

$primary_cat_name = '';
if ( cet_get_option( 'product_card_show_category', true ) ) {
	$terms = get_the_terms( $product_id, 'product_cat' );
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		$primary_cat_name = $terms[0]->name;
	}
}
?>
<article <?php wc_product_class( 'cet-product-card', $product_obj ); ?> data-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
	<div class="cet-product-card__media">
		<!-- Product Badges -->
		<div class="cet-product-card__badges">
			<?php if ( ! $product_obj->is_in_stock() ) : ?>
				<span class="cet-badge cet-badge--outofstock"><?php esc_html_e( 'Sold Out', 'custom-ecommerce-theme' ); ?></span>
			<?php else : ?>
				<?php if ( $is_on_sale ) : ?>
					<span class="cet-badge cet-badge--sale">
						<?php
						echo $sale_percent > 0
							/* translators: %d: Discount percentage. */
							? sprintf( esc_html__( '-%d%%', 'custom-ecommerce-theme' ), $sale_percent )
							: esc_html__( 'Sale', 'custom-ecommerce-theme' );
						?>
					</span>
				<?php endif; ?>

				<?php if ( $is_featured ) : ?>
					<span class="cet-badge cet-badge--featured"><?php esc_html_e( 'Featured', 'custom-ecommerce-theme' ); ?></span>
				<?php elseif ( $total_sales >= 5 ) : ?>
					<span class="cet-badge cet-badge--bestseller"><?php esc_html_e( 'Best Seller', 'custom-ecommerce-theme' ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<!-- Quick Action Floating Buttons -->
		<div class="cet-product-card__quick-actions">
			<?php if ( cet_get_option( 'product_card_show_wishlist', true ) ) : ?>
				<button
					type="button"
					class="cet-icon-action-btn cet-wishlist-toggle <?php echo $in_wishlist ? 'is-active' : ''; ?>"
					data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
					aria-label="<?php esc_attr_e( 'Toggle Wishlist', 'custom-ecommerce-theme' ); ?>"
					title="<?php esc_attr_e( 'Wishlist', 'custom-ecommerce-theme' ); ?>"
				>
					<?php echo cet_get_svg_icon( 'heart', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			<?php endif; ?>

			<?php if ( cet_get_option( 'product_card_show_quickview', true ) ) : ?>
				<button
					type="button"
					class="cet-icon-action-btn cet-quickview-trigger"
					data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
					aria-label="<?php esc_attr_e( 'Quick View Product', 'custom-ecommerce-theme' ); ?>"
					title="<?php esc_attr_e( 'Quick View', 'custom-ecommerce-theme' ); ?>"
				>
					<?php echo cet_get_svg_icon( 'eye', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			<?php endif; ?>

			<?php if ( cet_get_option( 'product_card_show_compare', true ) ) : ?>
				<button
					type="button"
					class="cet-icon-action-btn cet-compare-toggle"
					data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
					data-product-title="<?php echo esc_attr( $title ); ?>"
					aria-label="<?php esc_attr_e( 'Compare Product', 'custom-ecommerce-theme' ); ?>"
					title="<?php esc_attr_e( 'Compare', 'custom-ecommerce-theme' ); ?>"
				>
					<?php echo cet_get_svg_icon( 'compare', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			<?php endif; ?>
		</div>

		<a href="<?php echo esc_url( $permalink ); ?>" class="cet-product-card__img-link" tabindex="-1">
			<?php echo wp_kses_post( $product_obj->get_image( 'woocommerce_thumbnail' ) ); ?>
		</a>
	</div>

	<div class="cet-product-card__body">
		<?php if ( $primary_cat_name ) : ?>
			<div class="cet-product-card__category"><?php echo esc_html( $primary_cat_name ); ?></div>
		<?php endif; ?>

		<h3 class="cet-product-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
		</h3>

		<?php if ( cet_get_option( 'product_card_show_rating', true ) && $rating_count > 0 ) : ?>
			<div class="cet-product-card__rating" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: average rating */ __( 'Rated %s out of 5', 'custom-ecommerce-theme' ), number_format_i18n( $avg_rating, 1 ) ) ); ?>">
				<span class="cet-stars" aria-hidden="true">
					<?php echo esc_html( str_repeat( '★', (int) round( $avg_rating ) ) . str_repeat( '☆', max( 0, 5 - (int) round( $avg_rating ) ) ) ); ?>
				</span>
				<span>(<?php echo esc_html( (string) $rating_count ); ?>)</span>
			</div>
		<?php endif; ?>

		<div class="cet-product-card__footer">
			<div class="cet-product-card__price">
				<?php echo wp_kses_post( $product_obj->get_price_html() ); ?>
			</div>

			<div class="cet-product-card__cart">
				<?php
				$previous_global = $product;
				$product         = $product_obj;
				woocommerce_template_loop_add_to_cart();
				$product         = $previous_global;
				?>
			</div>
		</div>
	</div>
</article>

