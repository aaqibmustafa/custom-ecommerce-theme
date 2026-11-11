<?php
/**
 * Homepage Section: Blog & Latest Journal Articles
 *
 * Displays published WordPress posts with featured SVG fallback artwork,
 * or 3 curated luxury editorial journal cards on a brand-new installation.
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

$editorial_fallbacks = array(
	array(
		'date'     => gmdate( 'F j, Y', strtotime( '-5 days' ) ),
		'read'     => 4,
		'category' => __( 'Haute Horology', 'custom-ecommerce-theme' ),
		'title'    => __( 'The Art of Guilloché: Inside Our Geneva Dial Atelier', 'custom-ecommerce-theme' ),
		'excerpt'  => __( 'Discover how master artisans hand-engrave geometric light patterns onto solid Champagne Gold and Midnight Navy watch dials.', 'custom-ecommerce-theme' ),
		'svg_type' => 'watch',
		'sku'      => 'JOURNAL 01',
	),
	array(
		'date'     => gmdate( 'F j, Y', strtotime( '-12 days' ) ),
		'read'     => 5,
		'category' => __( 'Material Mastery', 'custom-ecommerce-theme' ),
		'title'    => __( 'Selecting Full-Grain Italian Calfskin for Lifetime Travel', 'custom-ecommerce-theme' ),
		'excerpt'  => __( 'A behind-the-scenes look at vegetable tanning, hand-waxed edges, and brushed gold hardware engineered to age with character.', 'custom-ecommerce-theme' ),
		'svg_type' => 'bag',
		'sku'      => 'JOURNAL 02',
	),
	array(
		'date'     => gmdate( 'F j, Y', strtotime( '-20 days' ) ),
		'read'     => 3,
		'category' => __( 'Style & Curation', 'custom-ecommerce-theme' ),
		'title'    => __( 'Midnight Navy & Champagne Gold: The 2026 Luxury Palette', 'custom-ecommerce-theme' ),
		'excerpt'  => __( 'Why pairing authoritative deep navy tones with warm metallic gold accents defines modern architectural minimalism.', 'custom-ecommerce-theme' ),
		'svg_type' => 'gem',
		'sku'      => 'JOURNAL 03',
	),
);
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
			<?php if ( $blog_query->have_posts() && $blog_query->found_posts > 1 ) : ?>
				<?php
				$idx = 0;
				while ( $blog_query->have_posts() ) :
					$blog_query->the_post();
					$fallback_art = $editorial_fallbacks[ $idx % 3 ];
					$idx++;
					?>
					<article <?php post_class( 'cet-card cet-post-card' ); ?>>
						<div class="cet-post-card__thumbnail" style="aspect-ratio:16/10; overflow:hidden; background:#14243A;">
							<a href="<?php the_permalink(); ?>" style="display:block; width:100%; height:100%;">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'cet-blog-card' ); ?>
								<?php elseif ( function_exists( 'cet_get_showcase_product_svg' ) ) : ?>
									<?php echo cet_get_showcase_product_svg( $fallback_art['svg_type'], $fallback_art['sku'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php endif; ?>
							</a>
						</div>

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
							<p style="color:#5A6578; font-size:0.92rem;">
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
			<?php else : ?>
				<?php foreach ( $editorial_fallbacks as $article ) : ?>
					<article class="cet-card cet-post-card">
						<div class="cet-post-card__thumbnail" style="aspect-ratio:16/10; overflow:hidden; background:#14243A;">
							<?php if ( function_exists( 'cet_get_showcase_product_svg' ) ) : ?>
								<?php echo cet_get_showcase_product_svg( $article['svg_type'], $article['sku'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endif; ?>
						</div>
						<div class="cet-post-card__body">
							<div class="cet-eyebrow" style="margin-bottom:0.4rem;">
								<span><?php echo esc_html( $article['category'] ); ?></span>
								<span>&bull;</span>
								<span>
									<?php
									printf(
										/* translators: %d: Reading time in minutes. */
										esc_html__( '%d min read', 'custom-ecommerce-theme' ),
										(int) $article['read']
									);
									?>
								</span>
							</div>
							<h3 class="cet-post-card__title">
								<a href="#latest-journal"><?php echo esc_html( $article['title'] ); ?></a>
							</h3>
							<p style="color:#5A6578; font-size:0.92rem;">
								<?php echo esc_html( $article['excerpt'] ); ?>
							</p>
							<a href="#signature-products" class="cet-btn cet-btn--outline cet-btn--sm">
								<?php esc_html_e( 'Explore Story', 'custom-ecommerce-theme' ); ?>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
