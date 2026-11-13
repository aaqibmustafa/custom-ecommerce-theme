<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section, Announcement Bar, Luxury Header, Mega Menu,
 * Live Product Search, Account/Wishlist/Cart triggers, and Mobile Drawer.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wishlist_count = function_exists( 'cet_get_wishlist_ids' ) ? count( cet_get_wishlist_ids() ) : 0;
$wc_cart_count  = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$bag_count      = function_exists( 'cet_get_bag_total_count' ) ? cet_get_bag_total_count() : 0;
$cart_count     = $wc_cart_count > 0 ? $wc_cart_count : $bag_count;
$has_wc_account = function_exists( 'cet_has_wc_account_page' ) && cet_has_wc_account_page();
$account_url    = function_exists( 'cet_get_account_url' ) ? cet_get_account_url() : '#cet-account-portal';
$shop_url       = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'shop' ) : home_url( '/#signature-products' );
$cart_url       = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'cart' ) : '#cet-mini-cart-drawer';
$checkout_url   = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'checkout' ) : $shop_url;
$wishlist_url   = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'wishlist' ) : $shop_url;
$track_url      = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'track-order' ) : $shop_url;
$acct_page_url  = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'my-account' ) : $account_url;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<script>
		(function() {
			try {
				var saved = localStorage.getItem('cet_theme_mode');
				var def = <?php echo wp_json_encode( cet_get_option( 'default_color_mode', 'light' ) ); ?>;
				var mode = saved;
				if (!mode || (mode !== 'dark' && mode !== 'light')) {
					if (def === 'dark' || (def === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
						mode = 'dark';
					} else {
						mode = 'light';
					}
				}
				document.documentElement.setAttribute('data-theme', mode);
				if (mode === 'dark') {
					document.documentElement.classList.add('cet-dark-mode');
				} else {
					document.documentElement.classList.remove('cet-dark-mode');
				}
			} catch (e) {}
		})();
	</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site cet-site-wrapper">
	<a class="skip-link screen-reader-text" href="#primary">
		<?php esc_html_e( 'Skip to content', 'custom-ecommerce-theme' ); ?>
	</a>

	<?php get_template_part( 'template-parts/header/announcement-bar' ); ?>

	<header id="masthead" class="site-header cet-header <?php echo cet_get_option( 'sticky_header_enable', true ) ? 'cet-header--sticky-enabled' : ''; ?>">
		<div class="cet-container cet-header__main">
			<!-- 1. Brand Identity & Mobile Menu Button -->
			<div class="site-branding cet-header__branding">
				<button type="button" class="cet-mobile-toggle" id="cet-mobile-menu-trigger" aria-controls="cet-mobile-drawer" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open Navigation Menu', 'custom-ecommerce-theme' ); ?>">
					<?php echo cet_get_svg_icon( 'menu', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>

				<div class="cet-header__brand-wrap">
					<?php
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cet-brand-link" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
						<?php
						$cet_description = get_bloginfo( 'description', 'display' );
						if ( $cet_description || is_customize_preview() ) :
							?>
							<p class="site-description cet-brand-tagline"><?php echo esc_html( $cet_description ); ?></p>
						<?php endif; ?>
					<?php } ?>
				</div>
			</div><!-- .site-branding -->

			<!-- 2. Live Product Search Bar with Category Selector -->
			<?php if ( cet_get_option( 'enable_header_search', true ) ) : ?>
				<div class="cet-header-search">
					<form role="search" method="get" class="cet-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" autocomplete="off">
						<?php if ( taxonomy_exists( 'product_cat' ) ) : ?>
							<select name="product_cat" class="cet-search-form__cat" aria-label="<?php esc_attr_e( 'Filter search by category', 'custom-ecommerce-theme' ); ?>">
								<option value=""><?php esc_html_e( 'All Categories', 'custom-ecommerce-theme' ); ?></option>
								<?php
								$search_cats = get_terms(
									array(
										'taxonomy'   => 'product_cat',
										'hide_empty' => true,
										'parent'     => 0,
									)
								);
								if ( ! is_wp_error( $search_cats ) && ! empty( $search_cats ) ) {
									foreach ( $search_cats as $s_cat ) {
										printf(
											'<option value="%1$s">%2$s</option>',
											esc_attr( $s_cat->slug ),
											esc_html( $s_cat->name )
										);
									}
								}
								?>
							</select>
							<input type="hidden" name="post_type" value="product">
						<?php endif; ?>

						<label class="screen-reader-text" for="cet-header-search-input">
							<?php esc_html_e( 'Search for products:', 'custom-ecommerce-theme' ); ?>
						</label>
						<input
							type="search"
							id="cet-header-search-input"
							class="cet-search-form__input"
							placeholder="<?php esc_attr_e( 'Search luxury products, collections, SKU...', 'custom-ecommerce-theme' ); ?>"
							value="<?php echo get_search_query(); ?>"
							name="s"
						>
						<button type="submit" class="cet-search-form__submit" aria-label="<?php esc_attr_e( 'Search', 'custom-ecommerce-theme' ); ?>">
							<?php echo cet_get_svg_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					</form>
					<div id="cet-live-search-results" class="cet-search-results" role="region" aria-live="polite"></div>
				</div>
			<?php endif; ?>

			<!-- 3. Account, Wishlist & Shopping Bag Actions -->
			<div class="cet-header__actions">
				<button
					type="button"
					class="cet-header-action cet-theme-toggle-btn"
					id="cet-theme-toggle"
					aria-label="<?php esc_attr_e( 'Toggle Color Scheme (Light / Dark)', 'custom-ecommerce-theme' ); ?>"
					title="<?php esc_attr_e( 'Switch Color Mode', 'custom-ecommerce-theme' ); ?>"
				>
					<span class="cet-theme-toggle__icon cet-theme-toggle__icon--moon" aria-hidden="true">
						<?php echo cet_get_svg_icon( 'moon', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="cet-theme-toggle__icon cet-theme-toggle__icon--sun" aria-hidden="true">
						<?php echo cet_get_svg_icon( 'sun', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</button>

				<a
					href="<?php echo esc_url( $account_url ); ?>"
					class="cet-header-action <?php echo $has_wc_account ? '' : 'cet-account-modal-trigger'; ?>"
					id="cet-account-trigger"
					title="<?php esc_attr_e( 'Client Account & Sign In', 'custom-ecommerce-theme' ); ?>"
				>
					<?php echo cet_get_svg_icon( 'user', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="cet-action-label" id="cet-account-action-label">
						<?php echo is_user_logged_in() ? esc_html__( 'Account', 'custom-ecommerce-theme' ) : esc_html__( 'Sign In', 'custom-ecommerce-theme' ); ?>
					</span>
				</a>

				<?php if ( cet_get_option( 'enable_header_wishlist', true ) ) : ?>
					<button type="button" class="cet-header-action cet-wishlist-Header-btn" id="cet-open-wishlist-info" title="<?php esc_attr_e( 'Saved Wishlist Items', 'custom-ecommerce-theme' ); ?>">
						<?php echo cet_get_svg_icon( 'heart', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="cet-action-badge" id="cet-wishlist-count"><?php echo esc_html( (string) $wishlist_count ); ?></span>
					</button>
				<?php endif; ?>

				<a href="<?php echo esc_url( $cart_url ); ?>" class="cet-header-action cet-cart-drawer-trigger" id="cet-cart-drawer-trigger" aria-controls="cet-mini-cart-drawer" aria-expanded="false">
					<?php echo cet_get_svg_icon( 'bag', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="cet-action-label"><?php esc_html_e( 'Bag', 'custom-ecommerce-theme' ); ?></span>
					<span class="cet-action-badge cet-cart-count"><?php echo esc_html( (string) $cart_count ); ?></span>
				</a>
			</div>
		</div>

		<!-- 4. Primary Navigation & Mega Menu Bar -->
		<div class="cet-header__nav-bar">
			<div class="cet-container cet-header__nav-inner">
				<?php get_template_part( 'template-parts/header/mega-menu' ); ?>

				<nav id="site-navigation" class="main-navigation cet-nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'custom-ecommerce-theme' ); ?>">
					<?php
					if ( has_nav_menu( 'primary' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'cet-nav__list',
								'container'      => false,
								'fallback_cb'    => false,
							)
						);
					} else {
						?>
						<ul id="primary-menu" class="cet-nav__list">
							<li class="<?php echo is_front_page() ? 'current-menu-item' : ''; ?>">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'custom-ecommerce-theme' ); ?></a>
							</li>
							<li class="<?php echo ( ( function_exists( 'is_shop' ) && is_shop() ) || is_page_template( 'page-templates/template-shop.php' ) ) ? 'current-menu-item' : ''; ?>">
								<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop Catalog', 'custom-ecommerce-theme' ); ?></a>
							</li>
							<li class="<?php echo is_page_template( 'page-templates/template-cart.php' ) ? 'current-menu-item' : ''; ?>">
								<a href="<?php echo esc_url( $cart_url ); ?>"><?php esc_html_e( 'Shopping Bag', 'custom-ecommerce-theme' ); ?></a>
							</li>
							<li class="<?php echo is_page_template( 'page-templates/template-checkout.php' ) ? 'current-menu-item' : ''; ?>">
								<a href="<?php echo esc_url( $checkout_url ); ?>"><?php esc_html_e( 'Checkout', 'custom-ecommerce-theme' ); ?></a>
							</li>
							<li class="<?php echo is_page_template( 'page-templates/template-wishlist.php' ) ? 'current-menu-item' : ''; ?>">
								<a href="<?php echo esc_url( $wishlist_url ); ?>"><?php esc_html_e( 'Wishlist', 'custom-ecommerce-theme' ); ?></a>
							</li>
							<li class="<?php echo is_page_template( 'page-templates/template-track-order.php' ) ? 'current-menu-item' : ''; ?>">
								<a href="<?php echo esc_url( $track_url ); ?>"><?php esc_html_e( 'Track Order', 'custom-ecommerce-theme' ); ?></a>
							</li>
							<li class="<?php echo is_page_template( 'page-templates/template-account.php' ) ? 'current-menu-item' : ''; ?>">
								<a href="<?php echo esc_url( $acct_page_url ); ?>"><?php esc_html_e( 'Account', 'custom-ecommerce-theme' ); ?></a>
							</li>
						</ul>
						<?php
					}
					?>
				</nav><!-- #site-navigation -->

				<div class="cet-header__concierge">
					<?php echo cet_get_svg_icon( 'shield', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php esc_html_e( 'Authentic Luxury Guarantee', 'custom-ecommerce-theme' ); ?></span>
				</div>
			</div>
		</div>
	</header><!-- #masthead -->

	<?php get_template_part( 'template-parts/header/mobile-drawer' ); ?>
