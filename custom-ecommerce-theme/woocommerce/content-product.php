<?php
/**
 * The template for displaying product content within loops
 *
 * Delegates to our reusable `template-parts/product/product-card.php` component.
 *
 * @package Custom_Ecommerce_Theme
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'cet-loop-item', $product ); ?>>
	<?php
	set_query_var( 'cet_card_product', $product );
	get_template_part( 'template-parts/product/product-card' );
	?>
</li>

