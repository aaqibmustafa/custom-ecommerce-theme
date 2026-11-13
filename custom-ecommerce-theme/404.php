<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>

<main id="primary" class="site-main cet-main">
	<div class="cet-container">
		<section class="error-404 not-found" style="background:var(--color-feature-bg); color:var(--color-feature-title); border:1px solid var(--color-feature-border); border-radius:var(--radius-lg); padding:clamp(2.5rem, 6vw, 5rem); text-align:center;">
			<span class="cet-eyebrow"><?php esc_html_e( 'Error 404 — Page Not Found', 'custom-ecommerce-theme' ); ?></span>
			<h1 class="page-title" style="color:var(--color-feature-title); font-size:clamp(2.25rem, 4.5vw, 3.75rem); margin-bottom:1rem;">
				<?php esc_html_e( 'This Destination Could Not Be Located', 'custom-ecommerce-theme' ); ?>
			</h1>
			<p style="max-width:540px; margin:0 auto 2rem; color:var(--color-feature-text);">
				<?php esc_html_e( 'The page or piece you are looking for may have been archived, moved, or sold out. Explore our collections or search below.', 'custom-ecommerce-theme' ); ?>
			</p>

			<div style="max-width:480px; margin:0 auto 2rem;">
				<?php get_search_form(); ?>
			</div>

			<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:1rem;">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cet-btn cet-btn--gold">
					<?php esc_html_e( 'Return to Homepage', 'custom-ecommerce-theme' ); ?>
				</a>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--outline-gold">
					<?php esc_html_e( 'Browse Boutique Catalog', 'custom-ecommerce-theme' ); ?>
				</a>
			</div>
		</section>
	</div>
</main><!-- #primary -->

<?php
get_footer();

