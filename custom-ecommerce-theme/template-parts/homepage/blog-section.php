<?php
/**
 * Homepage Section: Blog & Latest Journal Articles
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_blog_enable', true ) ) {
	return;
}

$blog_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
	)
);

if ( ! $blog_query->have_posts() ) {
	return;
}
?>
<section class="cet-section" id="latest-journal">
	<div class="cet-container">
		<div class="cet-section-header">
			<div>
				<span class="cet-eyebrow"><?php esc_html_e( 'Editorial & Perspectives', 'custom-ecommerce-theme' ); ?></span>
				<h2 class="cet-section-title"><?php esc_html_e( 'From The Atelier Journal', 'custom-ecommerce-theme' ); ?></h2>
			</div>
		</div>

		<div class="cet-grid-cols-3">
			<?php
			while ( $blog_query->have_posts() ) :
				$blog_query->the_post();
				?>
				<article <?php post_class( 'cet-card cet-post-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="cet-post-card__thumbnail">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'cet-blog-card' ); ?>
							</a>
						</div>
					<?php endif; ?>

					<div class="cet-post-card__body">
						<div class="cet-eyebrow" style="margin-bottom:0.4rem;">
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
						<h3 class="cet-post-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p style="color:var(--color-text-muted); font-size:0.92rem;">
							<?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?>
						</p>
						<a href="<?php the_permalink(); ?>" class="cet-btn cet-btn--outline cet-btn--sm">
							<?php esc_html_e( 'Read Article', 'custom-ecommerce-theme' ); ?>
						</a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
