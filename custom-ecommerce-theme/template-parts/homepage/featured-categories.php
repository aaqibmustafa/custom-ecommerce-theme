<?php
/**
 * Homepage Section: Featured Product Categories
 *
 * Dynamically queries WooCommerce `product_cat` terms when available,
 * and falls back to the 6 curated luxury showcase departments when the catalog is empty.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_categories_enable', true ) ) {
	return;
}

$categories = array();
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
		$categories = $terms;
	}
}

$section_title    = (string) cet_get_option( 'home_categories_title' );
$section_subtitle = (string) cet_get_option( 'home_categories_subtitle' );
$shop_url         = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/#signature-products' );
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
			<?php if ( ! empty( $categories ) ) : ?>
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
			<?php else : ?>
				<?php foreach ( cet_get_showcase_categories() as $s_cat ) : ?>
					<a href="<?php echo esc_url( (string) $s_cat['url'] ); ?>" class="cet-cat-card">
						<div class="cet-cat-card__thumb">
							<span><?php echo esc_html( (string) $s_cat['initial'] ); ?></span>
						</div>
						<h3 class="cet-cat-card__name"><?php echo esc_html( (string) $s_cat['name'] ); ?></h3>
						<span class="cet-cat-card__count">
							<?php
							printf(
								/* translators: %d: Product count. */
								esc_html__( '%d Curated Pieces', 'custom-ecommerce-theme' ),
								(int) $s_cat['count']
							);
							?>
						</span>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
