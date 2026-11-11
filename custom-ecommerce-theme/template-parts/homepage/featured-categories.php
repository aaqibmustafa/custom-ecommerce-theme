<?php
/**
 * Homepage Section: Featured Product Categories
 *
 * Dynamically queries WooCommerce `product_cat` terms.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_categories_enable', true ) || ! taxonomy_exists( 'product_cat' ) ) {
	return;
}

$categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'parent'     => 0,
		'number'     => 6,
	)
);

if ( is_wp_error( $categories ) || empty( $categories ) ) {
	return;
}

$section_title    = (string) cet_get_option( 'home_categories_title' );
$section_subtitle = (string) cet_get_option( 'home_categories_subtitle' );
$shop_url         = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<section class="cet-section cet-section--surface" id="featured-categories">
	<div class="cet-container">
		<div class="cet-section-header">
			<div>
				<span class="cet-eyebrow"><?php esc_html_e( 'Departments', 'custom-ecommerce-theme' ); ?></span>
				<h2 class="cet-section-title"><?php echo esc_html( $section_title ); ?></h2>
				<?php if ( $section_subtitle ) : ?>
					<p class="cet-section-subtitle"><?php echo esc_html( $section_subtitle ); ?></p>
				<?php endif; ?>
			</div>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--outline cet-btn--sm">
				<?php esc_html_e( 'View All Categories', 'custom-ecommerce-theme' ); ?>
			</a>
		</div>

		<div class="cet-grid-cols-6">
			<?php foreach ( $categories as $cat ) : ?>
				<?php
				$thumb_id  = (int) get_term_meta( $cat->term_id, 'thumbnail_id', true );
				$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' ) : '';
				$cat_link  = get_term_link( $cat );
				$initial   = mb_substr( $cat->name, 0, 1 );
				?>
				<a href="<?php echo esc_url( is_wp_error( $cat_link ) ? $shop_url : $cat_link ); ?>" class="cet-cat-card">
					<div class="cet-cat-card__thumb">
						<?php if ( $thumb_url ) : ?>
							<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" loading="lazy">
						<?php else : ?>
							<span><?php echo esc_html( $initial ); ?></span>
						<?php endif; ?>
					</div>
					<h3 class="cet-cat-card__name"><?php echo esc_html( $cat->name ); ?></h3>
					<span class="cet-cat-card__count">
						<?php
						printf(
							/* translators: %d: Product count. */
							esc_html( _n( '%d Product', '%d Products', (int) $cat->count, 'custom-ecommerce-theme' ) ),
							(int) $cat->count
						);
						?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
