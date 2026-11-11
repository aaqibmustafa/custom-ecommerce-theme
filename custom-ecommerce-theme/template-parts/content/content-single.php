<?php
/**
 * Template part for displaying single blog posts
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'cet-single-post-card' ); ?> style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:clamp(1.5rem, 3.5vw, 3rem); box-shadow:var(--shadow-sm);">
	<header class="entry-header" style="margin-bottom:1.75rem;">
		<div class="cet-eyebrow">
			<?php
			$categories_list = get_the_category_list( ', ' );
			if ( $categories_list ) {
				echo wp_kses_post( $categories_list ) . ' &bull; ';
			}
			?>
			<span><?php echo esc_html( get_the_date() ); ?></span>
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
		</div>

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta" style="font-size:0.875rem; color:var(--color-text-muted);">
			<?php
			printf(
				/* translators: %s: Author display name. */
				esc_html__( 'Written by %s', 'custom-ecommerce-theme' ),
				'<strong>' . esc_html( get_the_author() ) . '</strong>'
			);
			?>
		</div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
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

	<footer class="entry-footer" style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--color-border);">
		<?php
		$tags_list = get_the_tag_list( '', ', ' );
		if ( $tags_list ) {
			printf(
				'<div class="tags-links" style="margin-bottom:1.5rem; font-size:0.875rem;"><strong>%1$s</strong> %2$s</div>',
				esc_html__( 'Tagged:', 'custom-ecommerce-theme' ),
				wp_kses_post( $tags_list )
			);
		}
		?>

		<!-- Author Information Box -->
		<div class="cet-author-box" style="background:var(--color-background); border:1px solid var(--color-border); border-left:3px solid var(--color-accent); border-radius:var(--radius-md); padding:1.5rem; display:flex; gap:1.25rem; align-items:center;">
			<div style="flex-shrink:0;">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', '', array( 'style' => 'border-radius:50%; border:2px solid var(--color-accent);' ) ); ?>
			</div>
			<div>
				<h4 style="margin:0 0 0.25rem;"><?php echo esc_html( get_the_author() ); ?></h4>
				<p style="margin:0; font-size:0.875rem; color:var(--color-text-muted);">
					<?php echo esc_html( get_the_author_meta( 'description' ) ?: __( 'Editorial Contributor at Custom E-Commerce Theme Atelier.', 'custom-ecommerce-theme' ) ); ?>
				</p>
			</div>
		</div>
	</footer>
</article>

