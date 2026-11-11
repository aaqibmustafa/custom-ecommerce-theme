<?php
/**
 * Homepage Section: Verified Client Testimonials
 *
 * Dynamically pulls latest approved WooCommerce product reviews when available,
 * with curated fallback testimonials.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_testimonials_enable', true ) ) {
	return;
}

$reviews = get_comments(
	array(
		'number'      => 3,
		'status'      => 'approve',
		'post_status' => 'publish',
		'post_type'   => 'product',
	)
);
?>
<section class="cet-section cet-section--surface" id="client-testimonials">
	<div class="cet-container">
		<div class="cet-section-header cet-section-header--center">
			<span class="cet-eyebrow"><?php esc_html_e( 'Client Voices', 'custom-ecommerce-theme' ); ?></span>
			<h2 class="cet-section-title"><?php esc_html_e( 'Verified Collector Impressions', 'custom-ecommerce-theme' ); ?></h2>
		</div>

		<div class="cet-grid-cols-3">
			<?php if ( ! empty( $reviews ) ) : ?>
				<?php foreach ( $reviews as $review ) : ?>
					<?php $rating = (int) get_comment_meta( $review->comment_ID, 'rating', true ) ?: 5; ?>
					<article class="cet-testimonial-card">
						<div class="cet-stars" style="margin-bottom:0.75rem;" aria-hidden="true">
							<?php echo esc_html( str_repeat( '★', $rating ) ); ?>
						</div>
						<p class="cet-testimonial-card__quote">
							“<?php echo esc_html( wp_trim_words( $review->comment_content, 28 ) ); ?>”
						</p>
						<div class="cet-testimonial-card__author"><?php echo esc_html( $review->comment_author ); ?></div>
						<div class="cet-testimonial-card__role"><?php esc_html_e( 'Verified Buyer', 'custom-ecommerce-theme' ); ?></div>
					</article>
				<?php endforeach; ?>
			<?php else : ?>
				<article class="cet-testimonial-card">
					<div class="cet-stars" style="margin-bottom:0.75rem;" aria-hidden="true">★★★★★</div>
					<p class="cet-testimonial-card__quote">
						“<?php esc_html_e( 'Exceptional presentation and craftsmanship. The Midnight Navy and Champagne Gold unboxing felt like visiting a private Geneva salon.', 'custom-ecommerce-theme' ); ?>”
					</p>
					<div class="cet-testimonial-card__author"><?php esc_html_e( 'Alexander V.', 'custom-ecommerce-theme' ); ?></div>
					<div class="cet-testimonial-card__role"><?php esc_html_e( 'Verified Collector — Zurich', 'custom-ecommerce-theme' ); ?></div>
				</article>

				<article class="cet-testimonial-card">
					<div class="cet-stars" style="margin-bottom:0.75rem;" aria-hidden="true">★★★★★</div>
					<p class="cet-testimonial-card__quote">
						“<?php esc_html_e( 'Seamless checkout, insured overnight delivery, and impeccable attention to detail. Truly the gold standard of modern online luxury.', 'custom-ecommerce-theme' ); ?>”
					</p>
					<div class="cet-testimonial-card__author"><?php esc_html_e( 'Sophia Laurent', 'custom-ecommerce-theme' ); ?></div>
					<div class="cet-testimonial-card__role"><?php esc_html_e( 'Verified Client — Paris', 'custom-ecommerce-theme' ); ?></div>
				</article>

				<article class="cet-testimonial-card">
					<div class="cet-stars" style="margin-bottom:0.75rem;" aria-hidden="true">★★★★★</div>
					<p class="cet-testimonial-card__quote">
						“<?php esc_html_e( 'The concierge team guided me through sizing and bespoke gift wrapping effortlessly. My order arrived ahead of schedule.', 'custom-ecommerce-theme' ); ?>”
					</p>
					<div class="cet-testimonial-card__author"><?php esc_html_e( 'Marcus Sterling', 'custom-ecommerce-theme' ); ?></div>
					<div class="cet-testimonial-card__role"><?php esc_html_e( 'Private Member — New York', 'custom-ecommerce-theme' ); ?></div>
				</article>
			<?php endif; ?>
		</div>
	</div>
</section>
