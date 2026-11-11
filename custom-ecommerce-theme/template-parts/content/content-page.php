<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'cet-page-content-card' ); ?>>
	<?php if ( ! ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ) : ?>
		<header class="entry-header cet-section-header">
			<div>
				<?php the_title( '<h1 class="entry-title cet-section-title">', '</h1>' ); ?>
			</div>
		</header>
	<?php else : ?>
		<header class="entry-header" style="margin-bottom:2rem;">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header>
	<?php endif; ?>

	<?php if ( has_post_thumbnail() && ! ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ) : ?>
		<div class="post-thumbnail" style="margin-bottom:2rem; border-radius:var(--radius-md); overflow:hidden;">
			<?php the_post_thumbnail( 'large' ); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'custom-ecommerce-theme' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>
</article>

