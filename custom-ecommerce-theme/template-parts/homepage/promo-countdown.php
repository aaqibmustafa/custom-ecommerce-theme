<?php
/**
 * Homepage Section: Promotional Discount Banner & Live Special Offer Countdown
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! cet_get_option( 'home_countdown_enable', true ) ) {
	return;
}

$eyebrow     = (string) cet_get_option( 'home_countdown_eyebrow' );
$title       = (string) cet_get_option( 'home_countdown_title' );
$target_date = (string) cet_get_option( 'home_countdown_date' );
$coupon_code = (string) cet_get_option( 'home_countdown_code' );
$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<section class="cet-section" id="special-offers">
	<div class="cet-container">
		<div class="cet-countdown-banner" data-countdown-target="<?php echo esc_attr( $target_date ); ?>">
			<div>
				<?php if ( $eyebrow ) : ?>
					<span class="cet-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>
				<h2><?php echo esc_html( $title ); ?></h2>
				<p style="opacity:0.88; max-width:540px;">
					<?php esc_html_e( 'Unlock privileged tier pricing on select seasonal pieces before the timer concludes.', 'custom-ecommerce-theme' ); ?>
					<?php if ( $coupon_code ) : ?>
						<?php esc_html_e( 'Apply code at checkout:', 'custom-ecommerce-theme' ); ?>
						<strong style="color:var(--color-accent); border:1px dashed var(--color-accent); padding:0.15rem 0.55rem; border-radius:4px; margin-left:0.35rem;">
							<?php echo esc_html( $coupon_code ); ?>
						</strong>
					<?php endif; ?>
				</p>
				<div style="margin-top:1.5rem;">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--gold">
						<?php esc_html_e( 'Claim Private Offer', 'custom-ecommerce-theme' ); ?>
					</a>
				</div>
			</div>

			<div class="cet-countdown-timer" id="cet-offer-countdown" aria-live="polite">
				<div class="cet-countdown-unit">
					<span class="cet-countdown-unit__num" data-unit="days">07</span>
					<span class="cet-countdown-unit__label"><?php esc_html_e( 'Days', 'custom-ecommerce-theme' ); ?></span>
				</div>
				<div class="cet-countdown-unit">
					<span class="cet-countdown-unit__num" data-unit="hours">14</span>
					<span class="cet-countdown-unit__label"><?php esc_html_e( 'Hours', 'custom-ecommerce-theme' ); ?></span>
				</div>
				<div class="cet-countdown-unit">
					<span class="cet-countdown-unit__num" data-unit="minutes">38</span>
					<span class="cet-countdown-unit__label"><?php esc_html_e( 'Minutes', 'custom-ecommerce-theme' ); ?></span>
				</div>
				<div class="cet-countdown-unit">
					<span class="cet-countdown-unit__num" data-unit="seconds">45</span>
					<span class="cet-countdown-unit__label"><?php esc_html_e( 'Seconds', 'custom-ecommerce-theme' ); ?></span>
				</div>
			</div>
		</div>
	</div>
</section>

