<?php
/**
 * Homepage Section: Newsletter Subscription & Social Gallery Strip
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_newsletter = (bool) cet_get_option( 'home_newsletter_enable', true );
$show_social     = (bool) cet_get_option( 'home_social_enable', true );
$ig_handle       = (string) cet_get_option( 'home_instagram_handle', '@CustomEcommerceAtelier' );
$ig_url          = (string) cet_get_option( 'social_instagram', 'https://instagram.com/' );

if ( ! $show_newsletter && ! $show_social ) {
	return;
}
?>
<section class="cet-section cet-section--surface" id="newsletter-concierge">
	<div class="cet-container">
		<?php if ( $show_newsletter ) : ?>
			<div class="cet-newsletter-box" style="margin-bottom: <?php echo $show_social ? '3.5rem' : '0'; ?>;">
				<div>
					<span class="cet-eyebrow"><?php esc_html_e( 'Private Invitation', 'custom-ecommerce-theme' ); ?></span>
					<h2><?php esc_html_e( 'Join The Inner Circle', 'custom-ecommerce-theme' ); ?></h2>
					<p style="margin:0; opacity:0.86;">
						<?php esc_html_e( 'Receive priority access to limited Champagne Gold editions, private trunk shows, and complimentary concierge privileges.', 'custom-ecommerce-theme' ); ?>
					</p>
				</div>

				<div>
					<form id="cet-newsletter-form" class="cet-newsletter-form">
						<label class="screen-reader-text" for="cet-newsletter-email">
							<?php esc_html_e( 'Email Address', 'custom-ecommerce-theme' ); ?>
						</label>
						<input
							type="email"
							id="cet-newsletter-email"
							name="email"
							required
							placeholder="<?php esc_attr_e( 'Enter your email address...', 'custom-ecommerce-theme' ); ?>"
						>
						<button type="submit" class="cet-btn cet-btn--gold">
							<?php esc_html_e( 'Subscribe', 'custom-ecommerce-theme' ); ?>
						</button>
					</form>
					<p id="cet-newsletter-feedback" style="margin:0.6rem 0 0; font-size:0.82rem; color:var(--color-feature-accent);" aria-live="polite"></p>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $show_social ) : ?>
			<div class="cet-section-header cet-section-header--center">
				<span class="cet-eyebrow"><?php esc_html_e( 'Social Lookbook', 'custom-ecommerce-theme' ); ?></span>
				<h2 class="cet-section-title">
					<a href="<?php echo esc_url( $ig_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $ig_handle ); ?></a>
				</h2>
			</div>
		<?php endif; ?>
	</div>
</section>

