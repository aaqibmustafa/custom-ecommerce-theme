<?php
/**
 * The sidebar containing the main widget area (Blog or WooCommerce Shop)
 *
 * Includes intelligent WooCommerce filter fallbacks when `sidebar-shop` has no custom widgets assigned yet.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_shop_context = function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_shop() || is_product_category() || is_product_tag() );
$sidebar_id      = $is_shop_context ? 'sidebar-shop' : 'sidebar-blog';
?>
<aside id="secondary" class="widget-area cet-sidebar" aria-label="<?php echo $is_shop_context ? esc_attr__( 'Shop Filters Sidebar', 'custom-ecommerce-theme' ) : esc_attr__( 'Blog Sidebar', 'custom-ecommerce-theme' ); ?>">
	<?php if ( is_active_sidebar( $sidebar_id ) ) : ?>
		<?php dynamic_sidebar( $sidebar_id ); ?>
	<?php elseif ( $is_shop_context ) : ?>
		<?php
		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		$cats     = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'parent'     => 0,
			)
		);
		?>
		<section class="widget cet-widget cet-shop-widget">
			<h3 class="widget-title cet-widget__title"><?php esc_html_e( 'Filter by Category', 'custom-ecommerce-theme' ); ?></h3>
			<ul class="cet-filter-cat-list">
				<li>
					<a href="<?php echo esc_url( $shop_url ); ?>" class="<?php echo ( function_exists( 'is_shop' ) && is_shop() ) ? 'is-active' : ''; ?>">
						<span><?php esc_html_e( 'All Collections', 'custom-ecommerce-theme' ); ?></span>
					</a>
				</li>
				<?php if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) : ?>
					<?php foreach ( $cats as $c_term ) : ?>
						<?php
						$t_link    = get_term_link( $c_term );
						$is_active = is_tax( 'product_cat', $c_term->term_id );
						?>
						<li>
							<a href="<?php echo esc_url( is_wp_error( $t_link ) ? $shop_url : $t_link ); ?>" class="<?php echo $is_active ? 'is-active' : ''; ?>">
								<span><?php echo esc_html( $c_term->name ); ?></span>
								<span class="cet-badge" style="background:var(--color-background); color:var(--color-text-muted);"><?php echo esc_html( (string) $c_term->count ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</section>

		<section class="widget cet-widget cet-shop-widget">
			<h3 class="widget-title cet-widget__title"><?php esc_html_e( 'Price Filter', 'custom-ecommerce-theme' ); ?></h3>
			<form method="get" action="<?php echo esc_url( $shop_url ); ?>">
				<div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; margin-bottom:0.75rem;">
					<div>
						<label class="screen-reader-text" for="cet-min-price"><?php esc_html_e( 'Min Price', 'custom-ecommerce-theme' ); ?></label>
						<input type="number" id="cet-min-price" name="min_price" placeholder="<?php esc_attr_e( 'Min $', 'custom-ecommerce-theme' ); ?>" value="<?php echo isset( $_GET['min_price'] ) ? esc_attr( (string) absint( $_GET['min_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>" min="0">
					</div>
					<div>
						<label class="screen-reader-text" for="cet-max-price"><?php esc_html_e( 'Max Price', 'custom-ecommerce-theme' ); ?></label>
						<input type="number" id="cet-max-price" name="max_price" placeholder="<?php esc_attr_e( 'Max $', 'custom-ecommerce-theme' ); ?>" value="<?php echo isset( $_GET['max_price'] ) ? esc_attr( (string) absint( $_GET['max_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>" min="0">
					</div>
				</div>
				<button type="submit" class="cet-btn cet-btn--gold cet-btn--sm" style="width:100%;">
					<?php esc_html_e( 'Apply Price Range', 'custom-ecommerce-theme' ); ?>
				</button>
			</form>
		</section>
	<?php endif; ?>
</aside><!-- #secondary -->
