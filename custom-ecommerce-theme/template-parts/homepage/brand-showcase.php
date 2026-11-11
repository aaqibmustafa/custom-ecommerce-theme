<?php
/**
 * Homepage Section: Brand Showcase Strip
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_brands_enable', true ) ) {
	return;
}

$brands = array(
	'MAISON VÉRA',
	'AURUM GENÈVE',
	'VALENTIN & CO.',
	'SOVEREIGN ATELIER',
	'LUMIÈRE NOIRE',
	'KRONOS HOROLOGY',
);
?>
<section class="cet-section cet-section--surface" aria-label="<?php esc_attr_e( 'Featured Houses & Brands', 'custom-ecommerce-theme' ); ?>">
	<div class="cet-container">
		<div class="cet-section-header cet-section-header--center">
			<span class="cet-eyebrow"><?php esc_html_e( 'Authorized Partner', 'custom-ecommerce-theme' ); ?></span>
			<h2 class="cet-section-title"><?php esc_html_e( 'Distinguished Houses & Artisans', 'custom-ecommerce-theme' ); ?></h2>
		</div>

		<div class="cet-brands-strip">
			<?php foreach ( $brands as $brand_name ) : ?>
				<div class="cet-brand-pill"><?php echo esc_html( $brand_name ); ?></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
