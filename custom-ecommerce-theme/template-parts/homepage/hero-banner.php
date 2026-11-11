<?php
/**
 * Homepage Section: Hero Banner Showcase & Trust Strip
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'hero_enable', true ) ) {
	return;
}

$shop_url       = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$eyebrow        = (string) cet_get_option( 'hero_eyebrow' );
$title          = (string) cet_get_option( 'hero_title' );
$subtitle       = (string) cet_get_option( 'hero_subtitle' );
$primary_text   = (string) cet_get_option( 'hero_primary_cta_text' );
$primary_url    = (string) cet_get_option( 'hero_primary_cta_url' ) ?: $shop_url;
$secondary_text = (string) cet_get_option( 'hero_secondary_cta_text' );
$secondary_url  = (string) cet_get_option( 'hero_secondary_cta_url' );
$side_1_title   = (string) cet_get_option( 'hero_side_1_title' );
$side_1_sub     = (string) cet_get_option( 'hero_side_1_subtitle' );
$side_2_title   = (string) cet_get_option( 'hero_side_2_title' );
$side_2_sub     = (string) cet_get_option( 'hero_side_2_subtitle' );
?>
<section class="cet-hero" aria-label="<?php esc_attr_e( 'Featured Store Showcase', 'custom-ecommerce-theme' ); ?>">
	<div class="cet-container">
		<div class="cet-hero__grid">
			<!-- Main Hero Showcase -->
			<div class="cet-hero__main-card">
				<?php if ( $eyebrow ) : ?>
					<span class="cet-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>
				<h1 class="cet-hero__title"><?php echo esc_html( $title ); ?></h1>
				<p class="cet-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
				<div class="cet-hero__actions">
					<a href="<?php echo esc_url( $primary_url ); ?>" class="cet-btn cet-btn--gold cet-btn--lg">
						<span><?php echo esc_html( $primary_text ); ?></span>
						<?php echo cet_get_svg_icon( 'arrow-right', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<?php if ( $secondary_text ) : ?>
						<a href="<?php echo esc_url( $secondary_url ); ?>" class="cet-btn cet-btn--outline-gold cet-btn--lg">
							<?php echo esc_html( $secondary_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<!-- Dual Promotional Side Banners -->
			<div class="cet-hero__side-stack">
				<div class="cet-promo-card cet-promo-card--dark">
					<span class="cet-eyebrow"><?php esc_html_e( 'Exclusive Release', 'custom-ecommerce-theme' ); ?></span>
					<h3><?php echo esc_html( $side_1_title ); ?></h3>
					<p style="margin-bottom:1.1rem; opacity:0.85;"><?php echo esc_html( $side_1_sub ); ?></p>
					<div>
						<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>" class="cet-btn cet-btn--gold cet-btn--sm">
							<?php esc_html_e( 'Shop Edition', 'custom-ecommerce-theme' ); ?>
						</a>
					</div>
				</div>

				<div class="cet-promo-card">
					<span class="cet-eyebrow"><?php esc_html_e( 'Curated Curation', 'custom-ecommerce-theme' ); ?></span>
					<h3><?php echo esc_html( $side_2_title ); ?></h3>
					<p style="margin-bottom:1.1rem; color:var(--color-text-muted);"><?php echo esc_html( $side_2_sub ); ?></p>
					<div>
						<a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', $shop_url ) ); ?>" class="cet-btn cet-btn--primary cet-btn--sm">
							<?php esc_html_e( 'Discover Now', 'custom-ecommerce-theme' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Luxury Service Guarantee Strip -->
		<div class="cet-trust-strip">
			<div class="cet-trust-item">
				<span class="cet-trust-item__icon"><?php echo cet_get_svg_icon( 'truck', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<h4 class="cet-trust-item__title"><?php esc_html_e( 'Complimentary Express Delivery', 'custom-ecommerce-theme' ); ?></h4>
					<p class="cet-trust-item__desc"><?php esc_html_e( 'Insured worldwide shipping on orders over $150', 'custom-ecommerce-theme' ); ?></p>
				</div>
			</div>
			<div class="cet-trust-item">
				<span class="cet-trust-item__icon"><?php echo cet_get_svg_icon( 'shield', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<h4 class="cet-trust-item__title"><?php esc_html_e( 'Authenticity Guaranteed', 'custom-ecommerce-theme' ); ?></h4>
					<p class="cet-trust-item__desc"><?php esc_html_e( 'Every item certified with serialized provenance', 'custom-ecommerce-theme' ); ?></p>
				</div>
			</div>
			<div class="cet-trust-item">
				<span class="cet-trust-item__icon"><?php echo cet_get_svg_icon( 'refresh', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<h4 class="cet-trust-item__title"><?php esc_html_e( '30-Day Effortless Returns', 'custom-ecommerce-theme' ); ?></h4>
					<p class="cet-trust-item__desc"><?php esc_html_e( 'Complimentary courier pickup & exchanges', 'custom-ecommerce-theme' ); ?></p>
				</div>
			</div>
			<div class="cet-trust-item">
				<span class="cet-trust-item__icon"><?php echo cet_get_svg_icon( 'star', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<h4 class="cet-trust-item__title"><?php esc_html_e( 'Private Client Concierge', 'custom-ecommerce-theme' ); ?></h4>
					<p class="cet-trust-item__desc"><?php esc_html_e( 'Dedicated styling & priority order assistance', 'custom-ecommerce-theme' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>

