<?php
/**
 * The front page template file
 *
 * Orchestrates all customizable homepage sections when set as the site front page.
 * Also renders any Gutenberg block content added to a static front page.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main cet-main cet-front-page">
	<?php
	// 1. Hero Section with Promotional Banners & Service Guarantee Strip.
	get_template_part( 'template-parts/homepage/hero-banner' );

	// 2. Featured Product Categories.
	get_template_part( 'template-parts/homepage/featured-categories' );

	// 3. Trending, Best-Selling & New Arrivals Interactive Product Tabs.
	get_template_part( 'template-parts/homepage/product-tabs' );

	// 4. Featured Collections & Seasonal Lookbook.
	get_template_part( 'template-parts/homepage/featured-collections' );

	// 5. Special Offer Countdown & Promotional Discount Banner.
	get_template_part( 'template-parts/homepage/promo-countdown' );

	// 6. Brand Showcase Strip.
	get_template_part( 'template-parts/homepage/brand-showcase' );

	// 7. Recommended & Recently Viewed Products.
	get_template_part( 'template-parts/homepage/recommendations-recently-viewed' );

	// 8. Verified Customer Testimonials.
	get_template_part( 'template-parts/homepage/testimonials' );

	// 9. Latest Blog & Journal Articles.
	get_template_part( 'template-parts/homepage/blog-section' );

	// 10. Optional Gutenberg Page Content (when a static front page has blocks).
	if ( is_page() && have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$raw_content = trim( get_the_content() );
			if ( ! empty( $raw_content ) ) {
				echo '<section class="cet-section"><div class="cet-container entry-content">';
				the_content();
				echo '</div></section>';
			}
		}
	}

	// 11. Newsletter Subscription & Social Showcase.
	get_template_part( 'template-parts/homepage/newsletter-social' );
	?>
</main><!-- #primary -->

<?php
get_footer();

