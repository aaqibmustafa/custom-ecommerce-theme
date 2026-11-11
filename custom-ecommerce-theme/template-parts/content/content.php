<?php
/**
 * Template part for displaying posts in archives, blog home, and search results
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'cet-card cet-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="cet-post-card__thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'cet-blog-card' ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="cet-post-card__body">
		<div class="cet-eyebrow" style="margin-bottom:0.45rem;">
			<span><?php echo esc_html( get_the_date() ); ?></span>
			<?php if ( 'post' === get_post_type() ) : ?>
				<span>&bull;</span>
				<span>
					<?php
					printf(
						/* translators: %d: Reading time in minutes. */
						esc_html__( '%d min read', 'custom-ecommerce-theme' ),
						cet_get_reading_time( get_the_ID() )
					);
					?>
				</span>
			<?php endif; ?>
		</div>

		<header class="entry-header">
			<?php the_title( '<h2 class="entry-title cet-post-card__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		</header>

		<div class="entry-summary cet-post-card__excerpt" style="color:var(--color-text-muted); margin-bottom:1.25rem;">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
		</div>

		<div style="margin-top:auto;">
			<a href="<?php the_permalink(); ?>" class="cet-btn cet-btn--outline cet-btn--sm">
				<?php esc_html_e( 'Continue Reading', 'custom-ecommerce-theme' ); ?>
			</a>
		</div>
	</div>
</article>

