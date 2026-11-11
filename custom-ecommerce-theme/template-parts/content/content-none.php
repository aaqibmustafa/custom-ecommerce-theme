<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="no-results not-found cet-empty-state" style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-md); padding:3rem; text-align:center;">
	<header class="page-header">
		<span class="cet-eyebrow"><?php esc_html_e( 'Catalog Notice', 'custom-ecommerce-theme' ); ?></span>
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'custom-ecommerce-theme' ); ?></h1>
	</header>

	<div class="page-content" style="max-width:520px; margin:0 auto;">
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'We could not find any matching items for your search query. Please try different keywords or browse our departments.', 'custom-ecommerce-theme' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'It seems we cannot find what you are looking for. Perhaps searching can help.', 'custom-ecommerce-theme' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>
