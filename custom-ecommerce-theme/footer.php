<?php
/**
 * The template for displaying the multi-column luxury footer
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about_text = (string) cet_get_option( 'footer_about_text' );
$phone      = (string) cet_get_option( 'footer_phone' );
$email      = (string) cet_get_option( 'footer_email' );
$address    = (string) cet_get_option( 'footer_address' );
$shop_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$acct_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
?>
	<footer id="colophon" class="site-footer cet-footer">
		<div class="cet-container">
			<div class="cet-footer__grid">
				<!-- Column 1: Brand Identity & Concierge Contact -->
				<div class="cet-footer__col">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cet-footer__brand-title">
						<?php bloginfo( 'name' ); ?>
					</a>
					<p class="cet-footer__bio"><?php echo esc_html( $about_text ); ?></p>
					<ul class="cet-footer__contact-list">
						<?php if ( $address ) : ?>
							<li><strong><?php esc_html_e( 'Atelier:', 'custom-ecommerce-theme' ); ?></strong> <?php echo esc_html( $address ); ?></li>
						<?php endif; ?>
						<?php if ( $phone ) : ?>
							<li><strong><?php esc_html_e( 'Concierge:', 'custom-ecommerce-theme' ); ?></strong> <?php echo esc_html( $phone ); ?></li>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<li><strong><?php esc_html_e( 'Inquiries:', 'custom-ecommerce-theme' ); ?></strong> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
						<?php endif; ?>
					</ul>
					<div class="cet-footer__socials">
						<?php
						$socials = array(
							'Instagram' => (string) cet_get_option( 'social_instagram' ),
							'Facebook'  => (string) cet_get_option( 'social_facebook' ),
							'X'         => (string) cet_get_option( 'social_x' ),
							'Pinterest' => (string) cet_get_option( 'social_pinterest' ),
						);
						foreach ( $socials as $label => $url ) {
							if ( ! empty( $url ) ) {
								printf(
									'<a href="%1$s" class="cet-social-pill" target="_blank" rel="noopener noreferrer">%2$s</a>',
									esc_url( $url ),
									esc_html( $label )
								);
							}
						}
						?>
					</div>
				</div>

				<!-- Column 2: Shop & Departments -->
				<div class="cet-footer__col">
					<?php if ( is_active_sidebar( 'footer-widget-2' ) ) : ?>
						<?php dynamic_sidebar( 'footer-widget-2' ); ?>
					<?php else : ?>
						<h4 class="cet-footer__heading"><?php esc_html_e( 'Shop Departments', 'custom-ecommerce-theme' ); ?></h4>
						<?php
						if ( has_nav_menu( 'footer-1' ) ) {
							wp_nav_menu(
								array(
									'theme_location' => 'footer-1',
									'menu_class'     => 'cet-footer__links',
									'container'      => false,
									'depth'          => 1,
								)
							);
						} else {
							?>
							<ul class="cet-footer__links">
								<li><a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'All Luxury Collections', 'custom-ecommerce-theme' ); ?></a></li>
								<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', $shop_url ) ); ?>"><?php esc_html_e( 'New Season Arrivals', 'custom-ecommerce-theme' ); ?></a></li>
								<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>"><?php esc_html_e( 'Best-Selling Pieces', 'custom-ecommerce-theme' ); ?></a></li>
								<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'rating', $shop_url ) ); ?>"><?php esc_html_e( 'Top Rated Curations', 'custom-ecommerce-theme' ); ?></a></li>
							</ul>
							<?php
						}
						?>
					<?php endif; ?>
				</div>

				<!-- Column 3: Client Concierge & Account -->
				<div class="cet-footer__col">
					<?php if ( is_active_sidebar( 'footer-widget-3' ) ) : ?>
						<?php dynamic_sidebar( 'footer-widget-3' ); ?>
					<?php else : ?>
						<h4 class="cet-footer__heading"><?php esc_html_e( 'Client Concierge', 'custom-ecommerce-theme' ); ?></h4>
						<?php
						if ( has_nav_menu( 'footer-2' ) ) {
							wp_nav_menu(
								array(
									'theme_location' => 'footer-2',
									'menu_class'     => 'cet-footer__links',
									'container'      => false,
									'depth'          => 1,
								)
							);
						} else {
							?>
							<ul class="cet-footer__links">
								<li><a href="<?php echo esc_url( $acct_url ); ?>"><?php esc_html_e( 'My Account Dashboard', 'custom-ecommerce-theme' ); ?></a></li>
								<li><a href="<?php echo esc_url( $cart_url ); ?>"><?php esc_html_e( 'View Shopping Bag', 'custom-ecommerce-theme' ); ?></a></li>
								<li><a href="<?php echo esc_url( $acct_url ); ?>"><?php esc_html_e( 'Order History & Tracking', 'custom-ecommerce-theme' ); ?></a></li>
								<li><a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"><?php esc_html_e( 'Catalog Search', 'custom-ecommerce-theme' ); ?></a></li>
							</ul>
							<?php
						}
						?>
					<?php endif; ?>
				</div>

				<!-- Column 4: The House & Standards -->
				<div class="cet-footer__col">
					<?php if ( is_active_sidebar( 'footer-widget-4' ) ) : ?>
						<?php dynamic_sidebar( 'footer-widget-4' ); ?>
					<?php else : ?>
						<h4 class="cet-footer__heading"><?php esc_html_e( 'The House Promise', 'custom-ecommerce-theme' ); ?></h4>
						<p class="cet-footer__bio">
							<?php esc_html_e( 'Every order is inspected by hand, presented in signature Midnight Navy & Champagne Gold packaging, and shipped with full insurance.', 'custom-ecommerce-theme' ); ?>
						</p>
						<div class="cet-footer__payments">
							<span class="cet-pay-badge">VISA</span>
							<span class="cet-pay-badge">MASTERCARD</span>
							<span class="cet-pay-badge">AMEX</span>
							<span class="cet-pay-badge">APPLE PAY</span>
							<span class="cet-pay-badge">PAYPAL</span>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="cet-footer__bottom">
				<p style="margin:0;">
					&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>.
					<?php esc_html_e( 'All rights reserved.', 'custom-ecommerce-theme' ); ?>
				</p>
				<p style="margin:0;">
					<?php
					printf(
						/* translators: %s: Theme author link. */
						esc_html__( 'Custom E-Commerce Theme Engineered by %s', 'custom-ecommerce-theme' ),
						'<a href="https://aaqibmustafa.github.io/" target="_blank" rel="noopener noreferrer">Aaqib Mustafa</a>'
					);
					?>
				</p>
			</div>
		</div>
	</footer><!-- #colophon -->

	<?php get_template_part( 'template-parts/footer/mini-cart-drawer' ); ?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
