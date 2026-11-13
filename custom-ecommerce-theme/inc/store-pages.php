<?php
/**
 * Automatic Storefront E-Commerce Page Provisioner, URL Helpers & Store Shortcodes
 *
 * Ensures Shop Catalog, Shopping Bag, Checkout & Delivery, Saved Wishlist,
 * Client Account, and Order Tracking pages always exist and function seamlessly
 * whether WooCommerce is active or not.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define essential e-commerce pages for the storefront.
 *
 * @return array<string, array<string, string>>
 */
function cet_get_store_page_definitions(): array {
	return array(
		'shop'        => array(
			'title'    => __( 'Shop Catalog', 'custom-ecommerce-theme' ),
			'slug'     => 'shop',
			'template' => 'page-templates/template-shop.php',
			'content'  => '[cet_shop]',
			'wc_key'   => 'shop',
		),
		'cart'        => array(
			'title'    => __( 'Shopping Bag', 'custom-ecommerce-theme' ),
			'slug'     => 'cart',
			'template' => 'page-templates/template-cart.php',
			'content'  => '[cet_cart]',
			'wc_key'   => 'cart',
		),
		'checkout'    => array(
			'title'    => __( 'Checkout & Delivery', 'custom-ecommerce-theme' ),
			'slug'     => 'checkout',
			'template' => 'page-templates/template-checkout.php',
			'content'  => '[cet_checkout]',
			'wc_key'   => 'checkout',
		),
		'wishlist'    => array(
			'title'    => __( 'Saved Wishlist', 'custom-ecommerce-theme' ),
			'slug'     => 'wishlist',
			'template' => 'page-templates/template-wishlist.php',
			'content'  => '[cet_wishlist]',
			'wc_key'   => '',
		),
		'my-account'  => array(
			'title'    => __( 'Client Account', 'custom-ecommerce-theme' ),
			'slug'     => 'my-account',
			'template' => 'page-templates/template-account.php',
			'content'  => '[cet_account]',
			'wc_key'   => 'myaccount',
		),
		'track-order' => array(
			'title'    => __( 'Track Order', 'custom-ecommerce-theme' ),
			'slug'     => 'track-order',
			'template' => 'page-templates/template-track-order.php',
			'content'  => '[cet_track_order]',
			'wc_key'   => '',
		),
	);
}

/**
 * Idempotently ensure published WordPress pages exist for all core shopping routes.
 *
 * Reuses active WooCommerce pages when installed, never overwrites existing published pages,
 * and avoids duplicate creation across reloads.
 *
 * @param bool $force Force re-verification even if version option matches.
 * @return void
 */
function cet_ensure_store_pages( bool $force = false ): void {
	if ( ! $force && get_option( 'cet_store_pages_version' ) === CET_VERSION ) {
		return;
	}

	if ( get_transient( 'cet_creating_store_pages_lock' ) ) {
		return;
	}
	set_transient( 'cet_creating_store_pages_lock', 1, 30 );

	$saved = get_option( 'cet_store_pages', array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	$definitions = cet_get_store_page_definitions();

	foreach ( $definitions as $key => $def ) {
		// 1. Respect an existing published WooCommerce page if WooCommerce is active.
		if ( ! empty( $def['wc_key'] ) && function_exists( 'wc_get_page_id' ) ) {
			$wc_page_id = (int) wc_get_page_id( $def['wc_key'] );
			if ( $wc_page_id > 0 && 'publish' === get_post_status( $wc_page_id ) ) {
				$saved[ $key ] = $wc_page_id;
				continue;
			}
		}

		// 2. Keep previously provisioned published page.
		$existing_id = isset( $saved[ $key ] ) ? absint( $saved[ $key ] ) : 0;
		if ( $existing_id > 0 && 'publish' === get_post_status( $existing_id ) ) {
			continue;
		}

		// 3. Look up by slug in case page already exists.
		$by_path = get_page_by_path( $def['slug'] );
		if ( $by_path instanceof WP_Post && 'publish' === $by_path->post_status ) {
			$saved[ $key ] = $by_path->ID;
			$current_tpl   = get_post_meta( $by_path->ID, '_wp_page_template', true );
			if ( empty( $current_tpl ) || 'default' === $current_tpl ) {
				update_post_meta( $by_path->ID, '_wp_page_template', $def['template'] );
			}
			continue;
		}

		// 4. Create the page cleanly with its dedicated Page Template & shortcode.
		$new_id = wp_insert_post(
			array(
				'post_title'     => $def['title'],
				'post_name'      => $def['slug'],
				'post_content'   => $def['content'],
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			),
			true
		);

		if ( ! is_wp_error( $new_id ) && $new_id > 0 ) {
			update_post_meta( $new_id, '_wp_page_template', $def['template'] );
			$saved[ $key ] = (int) $new_id;
		}
	}

	update_option( 'cet_store_pages', $saved, true );
	update_option( 'cet_store_pages_version', CET_VERSION, false );
	delete_transient( 'cet_creating_store_pages_lock' );
}
add_action( 'after_switch_theme', static fn() => cet_ensure_store_pages( true ) );
add_action( 'init', static fn() => cet_ensure_store_pages( false ), 20 );

/**
 * Resolve a guaranteed working permalink for a storefront page (`shop`, `cart`, `checkout`, `wishlist`, `my-account`, `track-order`).
 *
 * @param string $key Store page key.
 * @return string
 */
function cet_get_store_page_url( string $key ): string {
	$defs = cet_get_store_page_definitions();
	if ( ! isset( $defs[ $key ] ) ) {
		return home_url( '/' );
	}

	$def = $defs[ $key ];

	// Prefer active published WooCommerce page permalink when applicable.
	if ( ! empty( $def['wc_key'] ) && class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) ) {
		$wc_id = (int) wc_get_page_id( $def['wc_key'] );
		if ( $wc_id > 0 && 'publish' === get_post_status( $wc_id ) ) {
			$wc_link = get_permalink( $wc_id );
			if ( $wc_link ) {
				return (string) $wc_link;
			}
		}
	}

	$saved = get_option( 'cet_store_pages', array() );
	if ( is_array( $saved ) && ! empty( $saved[ $key ] ) ) {
		$page_id = absint( $saved[ $key ] );
		if ( $page_id > 0 && 'publish' === get_post_status( $page_id ) ) {
			$link = get_permalink( $page_id );
			if ( $link ) {
				return (string) $link;
			}
		}
	}

	$by_path = get_page_by_path( $def['slug'] );
	if ( $by_path instanceof WP_Post && 'publish' === $by_path->post_status ) {
		$link = get_permalink( $by_path->ID );
		if ( $link ) {
			return (string) $link;
		}
	}

	return home_url( '/#signature-products' );
}

/**
 * Compute resolved bag items, subtotal, free-shipping progress, and shipping cost in one shared place.
 *
 * @param array<int, int>|null $bag_override Optional bag array `[ product_id => qty ]`.
 * @return array<string, mixed>
 */
function cet_resolve_bag_summary( ?array $bag_override = null ): array {
	$bag       = ( null !== $bag_override )
		? $bag_override
		: ( function_exists( 'cet_get_bag_items' ) ? cet_get_bag_items() : array() );
	$threshold = (float) cet_get_option( 'free_shipping_threshold', 150 );
	$subtotal  = 0.0;
	$resolved  = array();

	if ( function_exists( 'cet_resolve_product_data' ) ) {
		foreach ( $bag as $pid => $qty ) {
			$pdata = cet_resolve_product_data( (int) $pid );
			if ( $pdata ) {
				$pdata['qty']        = (int) $qty;
				$pdata['line_total'] = $pdata['price'] * (int) $qty;
				$subtotal           += $pdata['line_total'];
				$resolved[]          = $pdata;
			}
		}
	}

	$percentage    = $threshold > 0 ? min( 100, (int) round( ( $subtotal / $threshold ) * 100 ) ) : 100;
	$remaining     = max( 0.0, $threshold - $subtotal );
	$shipping_cost = ( $subtotal >= $threshold || 0.0 === $subtotal ) ? 0.0 : 18.0;

	return array(
		'items'         => $resolved,
		'subtotal'      => $subtotal,
		'threshold'     => $threshold,
		'percentage'    => $percentage,
		'remaining'     => $remaining,
		'shipping_cost' => $shipping_cost,
	);
}

/**
 * Render the Free Shipping Progress Goal markup consistently for both Drawer & Cart Page.
 *
 * @param array<string, mixed> $summary Result of `cet_resolve_bag_summary()`.
 * @return string
 */
function cet_render_shipping_goal_markup( array $summary ): string {
	$remaining  = (float) ( $summary['remaining'] ?? 0.0 );
	$percentage = (int) ( $summary['percentage'] ?? 0 );

	ob_start();
	?>
	<div class="cet-shipping-goal">
		<div class="cet-shipping-goal__text">
			<?php if ( $remaining > 0 ) : ?>
				<?php
				printf(
					/* translators: %s: Remaining amount formatted as currency. */
					esc_html__( 'Add $%s more to unlock Complimentary Insured Express Delivery!', 'custom-ecommerce-theme' ),
					esc_html( number_format( $remaining, 2 ) )
				);
				?>
			<?php else : ?>
				<span class="cet-shipping-goal__unlocked">
					<?php echo cet_get_svg_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Unlocked: Complimentary Insured Express Delivery!', 'custom-ecommerce-theme' ); ?>
				</span>
			<?php endif; ?>
		</div>
		<div class="cet-shipping-goal__track" role="progressbar" aria-valuenow="<?php echo esc_attr( (string) $percentage ); ?>" aria-valuemin="0" aria-valuemax="100">
			<div class="cet-shipping-goal__fill" style="width: <?php echo esc_attr( (string) $percentage ); ?>%;"></div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render Checkout Portal HTML consistently for both Checkout Modal and dedicated `[cet_checkout]` page.
 *
 * @param bool $is_page Whether rendering inline inside a page (`true`) or inside modal (`false`).
 * @return string
 */
function cet_render_checkout_portal_markup( bool $is_page = false ): string {
	$summary       = cet_resolve_bag_summary();
	$items         = $summary['items'];
	$subtotal      = (float) $summary['subtotal'];
	$shipping_cost = (float) $summary['shipping_cost'];

	if ( empty( $items ) ) {
		ob_start();
		?>
		<div class="cet-empty-state">
			<h3><?php esc_html_e( 'Your Shopping Bag is Empty', 'custom-ecommerce-theme' ); ?></h3>
			<p>
				<?php esc_html_e( 'Add a timepiece, fragrance, leather piece, or jewelry item to your bag before checking out.', 'custom-ecommerce-theme' ); ?>
			</p>
			<a href="<?php echo esc_url( cet_get_store_page_url( 'shop' ) ); ?>" class="cet-btn cet-btn--gold">
				<?php esc_html_e( 'Browse Shop Catalog', 'custom-ecommerce-theme' ); ?>
			</a>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	$grand_total   = $subtotal + $shipping_cost;
	$current_user  = is_user_logged_in() ? wp_get_current_user() : null;
	$prefill_name  = $current_user ? $current_user->display_name : '';
	$prefill_email = $current_user ? $current_user->user_email : '';
	$prefill_phone = $current_user ? (string) get_user_meta( $current_user->ID, '_cet_phone', true ) : '';

	ob_start();
	?>
	<div class="cet-checkout-portal <?php echo $is_page ? 'cet-checkout-portal--page' : ''; ?>" data-subtotal="<?php echo esc_attr( (string) $subtotal ); ?>" data-standard-fee="<?php echo esc_attr( (string) $shipping_cost ); ?>" data-priority-fee="25">
		<?php if ( ! $is_page ) : ?>
			<div class="cet-section-header" style="margin-bottom:1.5rem;">
				<div>
					<span class="cet-eyebrow"><?php esc_html_e( 'Encrypted Client Checkout', 'custom-ecommerce-theme' ); ?></span>
					<h2 class="cet-section-title" style="margin:0;"><?php esc_html_e( 'Delivery & Payment Confirmation', 'custom-ecommerce-theme' ); ?></h2>
				</div>
			</div>
		<?php endif; ?>

		<form id="cet-direct-checkout-form">
			<div class="cet-checkout-layout">
				<!-- Column 1: Delivery & Shipping Address -->
				<div class="<?php echo $is_page ? 'cet-checkout-main-panel' : ''; ?>">
					<h3 style="font-size:1.1rem; margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px solid var(--color-border);">
						<?php esc_html_e( '1. Delivery Destination & Courier', 'custom-ecommerce-theme' ); ?>
					</h3>

					<div class="cet-form-row-2">
						<div>
							<label class="cet-field-label"><?php esc_html_e( 'Full Name *', 'custom-ecommerce-theme' ); ?></label>
							<input type="text" name="customer_name" required value="<?php echo esc_attr( $prefill_name ); ?>" placeholder="<?php esc_attr_e( 'Aaqib Mustafa', 'custom-ecommerce-theme' ); ?>">
						</div>
						<div>
							<label class="cet-field-label"><?php esc_html_e( 'Phone / WhatsApp *', 'custom-ecommerce-theme' ); ?></label>
							<input type="tel" name="customer_phone" required value="<?php echo esc_attr( $prefill_phone ); ?>" placeholder="+1 (800) 555-0199">
						</div>
					</div>

					<div class="cet-form-field">
						<label class="cet-field-label"><?php esc_html_e( 'Email Address for Order Receipt *', 'custom-ecommerce-theme' ); ?></label>
						<input type="email" name="customer_email" required value="<?php echo esc_attr( $prefill_email ); ?>" placeholder="client@example.com">
					</div>

					<div class="cet-form-field">
						<label class="cet-field-label"><?php esc_html_e( 'Delivery Street Address *', 'custom-ecommerce-theme' ); ?></label>
						<input type="text" name="customer_address" required placeholder="<?php esc_attr_e( 'House / Suite, Street, Area', 'custom-ecommerce-theme' ); ?>">
					</div>

					<div class="cet-form-row-2" style="margin-bottom:1.25rem;">
						<div>
							<label class="cet-field-label"><?php esc_html_e( 'City *', 'custom-ecommerce-theme' ); ?></label>
							<input type="text" name="customer_city" required placeholder="<?php esc_attr_e( 'City', 'custom-ecommerce-theme' ); ?>">
						</div>
						<div>
							<label class="cet-field-label"><?php esc_html_e( 'Postal Code *', 'custom-ecommerce-theme' ); ?></label>
							<input type="text" name="customer_zip" required placeholder="10001">
						</div>
					</div>

					<!-- Delivery Speed Options -->
					<h4 style="font-size:0.95rem; margin-bottom:0.65rem;"><?php esc_html_e( 'Select Delivery Service:', 'custom-ecommerce-theme' ); ?></h4>
					<div class="cet-radio-stack" style="margin-bottom:1.25rem;">
						<label class="cet-radio-card is-selected">
							<span class="cet-radio-card__main">
								<input type="radio" name="delivery_method" value="standard" checked>
								<strong><?php esc_html_e( 'Insured Express Delivery (2–4 Business Days)', 'custom-ecommerce-theme' ); ?></strong>
							</span>
							<span style="font-weight:700; color:var(--color-success);">
								<?php echo 0.0 === $shipping_cost ? esc_html__( 'FREE', 'custom-ecommerce-theme' ) : '$18.00'; ?>
							</span>
						</label>

						<label class="cet-radio-card">
							<span class="cet-radio-card__main">
								<input type="radio" name="delivery_method" value="priority">
								<strong><?php esc_html_e( 'White-Glove Next-Day Courier', 'custom-ecommerce-theme' ); ?></strong>
							</span>
							<span style="font-weight:700; color:var(--color-primary);">+$25.00</span>
						</label>
					</div>

					<h3 style="font-size:1.1rem; margin-bottom:0.85rem; padding-bottom:0.5rem; border-bottom:1px solid var(--color-border);">
						<?php esc_html_e( '2. Payment Method', 'custom-ecommerce-theme' ); ?>
					</h3>

					<div class="cet-radio-stack">
						<label class="cet-radio-card is-selected">
							<span class="cet-radio-card__main">
								<input type="radio" name="payment_method" value="Cash on Delivery (COD)" checked>
								<span>
									<strong style="display:block; color:var(--color-primary);"><?php esc_html_e( 'Cash on Delivery (COD)', 'custom-ecommerce-theme' ); ?></strong>
									<span style="font-size:0.78rem; color:var(--color-text-muted);"><?php esc_html_e( 'Pay securely in cash or card upon insured courier arrival.', 'custom-ecommerce-theme' ); ?></span>
								</span>
							</span>
						</label>

						<label class="cet-radio-card">
							<span class="cet-radio-card__main">
								<input type="radio" name="payment_method" value="Credit / Debit Card (Visa / Mastercard / Amex)">
								<span>
									<strong style="display:block; color:var(--color-primary);"><?php esc_html_e( 'Credit / Debit Card (Visa, Mastercard, Amex)', 'custom-ecommerce-theme' ); ?></strong>
									<span style="font-size:0.78rem; color:var(--color-text-muted);"><?php esc_html_e( '256-Bit SSL Encrypted instant card authorization.', 'custom-ecommerce-theme' ); ?></span>
								</span>
							</span>
						</label>

						<label class="cet-radio-card">
							<span class="cet-radio-card__main">
								<input type="radio" name="payment_method" value="Direct Bank Wire / Apple Pay">
								<span>
									<strong style="display:block; color:var(--color-primary);"><?php esc_html_e( 'Bank Transfer / Digital Wallet', 'custom-ecommerce-theme' ); ?></strong>
									<span style="font-size:0.78rem; color:var(--color-text-muted);"><?php esc_html_e( 'Instant bank transfer or Apple Pay / PayPal.', 'custom-ecommerce-theme' ); ?></span>
								</span>
							</span>
						</label>
					</div>
				</div>

				<!-- Column 2: Order Summary -->
				<div class="<?php echo $is_page ? 'cet-checkout-summary-panel' : ''; ?>">
					<div class="cet-order-summary-navy">
						<h4 style="color:var(--color-feature-accent) !important; margin:0 0 0.85rem; font-size:1rem;"><?php esc_html_e( 'Order Breakdown', 'custom-ecommerce-theme' ); ?></h4>
						<?php foreach ( $items as $it ) : ?>
							<div class="cet-order-summary-row">
								<span><?php echo esc_html( $it['title'] . ' × ' . $it['qty'] ); ?></span>
								<strong>$<?php echo esc_html( number_format( (float) $it['line_total'], 2 ) ); ?></strong>
							</div>
						<?php endforeach; ?>

						<div class="cet-order-summary-row" style="border-top:1px solid rgba(214,173,101,0.3); margin-top:0.75rem; padding-top:0.75rem;">
							<span><?php esc_html_e( 'Delivery:', 'custom-ecommerce-theme' ); ?></span>
							<span id="cet-checkout-delivery-display" style="color:var(--color-feature-accent); font-weight:700;">
								<?php echo 0.0 === $shipping_cost ? esc_html__( 'FREE Insured Express', 'custom-ecommerce-theme' ) : '$' . esc_html( number_format( $shipping_cost, 2 ) ); ?>
							</span>
						</div>

						<div class="cet-order-summary-total">
							<span><?php esc_html_e( 'Total Payable:', 'custom-ecommerce-theme' ); ?></span>
							<span id="cet-checkout-total-display" style="color:var(--color-feature-accent);">$<?php echo esc_html( number_format( $grand_total, 2 ) ); ?></span>
						</div>

						<div style="margin-top:1.25rem;">
							<button type="submit" class="cet-btn cet-btn--gold cet-btn--lg" style="width:100%;">
								<?php esc_html_e( 'Place Order & Confirm Delivery', 'custom-ecommerce-theme' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render Full Shopping Bag Page HTML `[cet_cart]` (synchronized dynamically with `#cet-mini-cart-drawer`).
 *
 * @param array<int, int>|null $bag_override Optional updated bag map for live AJAX refreshes.
 * @return string
 */
function cet_render_cart_page_html( ?array $bag_override = null ): string {
	$summary       = cet_resolve_bag_summary( $bag_override );
	$items         = $summary['items'];
	$subtotal      = (float) $summary['subtotal'];
	$shipping_cost = (float) $summary['shipping_cost'];
	$grand_total   = $subtotal + $shipping_cost;
	$shop_url      = cet_get_store_page_url( 'shop' );
	$checkout_url  = cet_get_store_page_url( 'checkout' );

	ob_start();
	?>
	<div id="cet-cart-page-container">
		<?php echo cet_render_shipping_goal_markup( $summary ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php if ( empty( $items ) ) : ?>
			<div class="cet-empty-state">
				<h3><?php esc_html_e( 'Your Shopping Bag is Empty', 'custom-ecommerce-theme' ); ?></h3>
				<p>
					<?php esc_html_e( 'Discover timeless luxury essentials in our curated boutique catalog.', 'custom-ecommerce-theme' ); ?>
				</p>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--gold">
					<?php esc_html_e( 'Continue Shopping', 'custom-ecommerce-theme' ); ?>
				</a>
			</div>
		<?php else : ?>
			<div class="cet-cart-layout">
				<div class="cet-cart-main-panel">
					<div class="cet-cart-page-list">
						<?php foreach ( $items as $item ) : ?>
							<div class="cet-cart-page-item">
								<div class="cet-cart-page-item__thumb">
									<?php if ( ! empty( $item['image_url'] ) ) : ?>
										<img src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>">
									<?php else : ?>
										<?php echo cet_get_showcase_product_svg( $item['svg_type'], $item['sku'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php endif; ?>
								</div>

								<div class="cet-cart-page-item__info">
									<div style="font-size:0.72rem; font-weight:700; color:var(--color-accent); text-transform:uppercase;">
										<?php echo esc_html( $item['category'] ?: __( 'Signature Edition', 'custom-ecommerce-theme' ) ); ?>
									</div>
									<h3 style="margin:0.15rem 0 0.35rem; font-size:1.05rem; color:var(--color-primary);"><?php echo esc_html( $item['title'] ); ?></h3>
									<div style="font-size:0.84rem; color:var(--color-text-muted);">
										<?php esc_html_e( 'SKU:', 'custom-ecommerce-theme' ); ?> <?php echo esc_html( $item['sku'] ); ?> &bull;
										$<?php echo esc_html( number_format( (float) $item['price'], 2 ) ); ?> <?php esc_html_e( 'each', 'custom-ecommerce-theme' ); ?>
									</div>
								</div>

								<div class="cet-cart-page-item__controls">
									<div class="cet-bag-qty-control">
										<button type="button" class="cet-bag-qty-btn" data-product-id="<?php echo esc_attr( (string) $item['id'] ); ?>" data-delta="-1">-</button>
										<span><?php echo esc_html( (string) $item['qty'] ); ?></span>
										<button type="button" class="cet-bag-qty-btn" data-product-id="<?php echo esc_attr( (string) $item['id'] ); ?>" data-delta="1">+</button>
									</div>

									<strong style="font-size:1.05rem; color:var(--color-primary);">
										$<?php echo esc_html( number_format( (float) $item['line_total'], 2 ) ); ?>
									</strong>

									<button type="button" class="cet-icon-action-btn cet-bag-remove-btn" data-product-id="<?php echo esc_attr( (string) $item['id'] ); ?>" aria-label="<?php esc_attr_e( 'Remove item', 'custom-ecommerce-theme' ); ?>">
										&times;
									</button>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<div style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:0.85rem; margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid var(--color-border);">
						<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--outline cet-btn--sm">
							<?php esc_html_e( 'Continue Shopping', 'custom-ecommerce-theme' ); ?>
						</a>
						<button type="button" class="cet-btn cet-btn--outline cet-btn--sm" id="cet-clear-bag-btn">
							<?php esc_html_e( 'Clear Shopping Bag', 'custom-ecommerce-theme' ); ?>
						</button>
					</div>
				</div>

				<aside class="cet-cart-summary-panel">
					<h3 style="margin-bottom:1rem; padding-bottom:0.75rem; border-bottom:1px solid var(--color-border);">
						<?php esc_html_e( 'Bag Summary', 'custom-ecommerce-theme' ); ?>
					</h3>

					<div style="display:flex; justify-content:space-between; margin-bottom:0.75rem; font-size:0.94rem;">
						<span style="color:var(--color-text-muted);"><?php esc_html_e( 'Subtotal', 'custom-ecommerce-theme' ); ?></span>
						<strong style="color:var(--color-primary);">$<?php echo esc_html( number_format( $subtotal, 2 ) ); ?></strong>
					</div>

					<div style="display:flex; justify-content:space-between; margin-bottom:0.75rem; font-size:0.94rem;">
						<span style="color:var(--color-text-muted);"><?php esc_html_e( 'Estimated Insured Delivery', 'custom-ecommerce-theme' ); ?></span>
						<strong style="color:var(--color-success);">
							<?php echo 0.0 === $shipping_cost ? esc_html__( 'FREE', 'custom-ecommerce-theme' ) : '$' . esc_html( number_format( $shipping_cost, 2 ) ); ?>
						</strong>
					</div>

					<div style="display:flex; justify-content:space-between; padding-top:1rem; margin:1rem 0 1.35rem; border-top:2px solid var(--color-border); font-size:1.2rem; font-weight:800; color:var(--color-primary);">
						<span><?php esc_html_e( 'Estimated Total', 'custom-ecommerce-theme' ); ?></span>
						<span>$<?php echo esc_html( number_format( $grand_total, 2 ) ); ?></span>
					</div>

					<div style="display:flex; flex-direction:column; gap:0.75rem;">
						<a href="<?php echo esc_url( $checkout_url ); ?>" class="cet-btn cet-btn--gold cet-btn--lg" style="width:100%;">
							<?php esc_html_e( 'Proceed to Checkout & Delivery', 'custom-ecommerce-theme' ); ?>
						</a>
					</div>
				</aside>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Shortcode `[cet_shop]` — Interactive Boutique Catalog Page with Category Filters & Sorting.
 *
 * @return string
 */
function cet_shop_shortcode(): string {
	$shop_url    = cet_get_store_page_url( 'shop' );
	$active_cat  = isset( $_GET['cet_cat'] ) ? sanitize_title( wp_unslash( $_GET['cet_cat'] ) ) : '';
	$active_sort = isset( $_GET['cet_sort'] ) ? sanitize_key( wp_unslash( $_GET['cet_sort'] ) ) : 'featured';
	$search_q    = isset( $_GET['cet_q'] ) ? sanitize_text_field( wp_unslash( $_GET['cet_q'] ) ) : '';
	$page_id_val = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : 0;

	// Build dynamic product categories from taxonomy product_cat when available.
	$categories = array();
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
			)
		);
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$categories[] = array(
					'name' => $term->name,
					'slug' => $term->slug,
				);
			}
		}
	}
	if ( empty( $categories ) && function_exists( 'cet_get_showcase_categories' ) ) {
		$showcase_cats = cet_get_showcase_categories();
		foreach ( $showcase_cats as $sc ) {
			$categories[] = array(
				'name' => (string) $sc['name'],
				'slug' => sanitize_title( (string) $sc['name'] ),
			);
		}
	}

	// If WooCommerce products exist, render live WooCommerce products with full toolbar & category pills.
	if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_products' ) ) {
		$wc_check = wc_get_products(
			array(
				'status' => 'publish',
				'limit'  => 1,
			)
		);
		if ( ! empty( $wc_check ) ) {
			$args = array(
				'status' => 'publish',
				'limit'  => 48,
			);
			if ( $active_cat ) {
				$args['category'] = array( $active_cat );
			}
			if ( $search_q ) {
				$args['s'] = $search_q;
			}
			if ( 'price_asc' === $active_sort ) {
				$args['orderby'] = 'price';
				$args['order']   = 'ASC';
			} elseif ( 'price_desc' === $active_sort ) {
				$args['orderby'] = 'price';
				$args['order']   = 'DESC';
			} elseif ( 'rated' === $active_sort ) {
				$args['orderby'] = 'rating';
				$args['order']   = 'DESC';
			} else {
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
			}
			$wc_prods = wc_get_products( $args );

			ob_start();
			?>
			<div class="cet-shop-page-wrap">
				<!-- Category Filter Pills -->
				<?php if ( ! empty( $categories ) ) : ?>
					<div class="cet-shop-cat-pills">
						<a href="<?php echo esc_url( remove_query_arg( 'cet_cat', $shop_url ) ); ?>" class="cet-btn cet-btn--sm <?php echo '' === $active_cat ? 'cet-btn--primary' : 'cet-btn--outline'; ?>">
							<?php esc_html_e( 'All Departments', 'custom-ecommerce-theme' ); ?>
						</a>
						<?php foreach ( $categories as $cat ) : ?>
							<?php
							$slug     = sanitize_title( (string) $cat['slug'] );
							$cat_link = add_query_arg( 'cet_cat', $slug, $shop_url );
							?>
							<a href="<?php echo esc_url( $cat_link ); ?>" class="cet-btn cet-btn--sm <?php echo $active_cat === $slug ? 'cet-btn--primary' : 'cet-btn--outline'; ?>">
								<?php echo esc_html( (string) $cat['name'] ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<!-- Toolbar: Filter Count, Search & Sort -->
				<form method="get" action="<?php echo esc_url( $shop_url ); ?>" class="cet-shop-toolbar">
					<?php if ( $page_id_val > 0 ) : ?>
						<input type="hidden" name="page_id" value="<?php echo esc_attr( (string) $page_id_val ); ?>">
					<?php endif; ?>
					<?php if ( $active_cat ) : ?>
						<input type="hidden" name="cet_cat" value="<?php echo esc_attr( $active_cat ); ?>">
					<?php endif; ?>

					<div class="cet-shop-toolbar__left">
						<span class="woocommerce-result-count">
							<?php
							printf(
								/* translators: %d: number of matching products */
								esc_html( _n( 'Showing %d product', 'Showing %d products', count( $wc_prods ), 'custom-ecommerce-theme' ) ),
								count( $wc_prods )
							);
							?>
						</span>
					</div>

					<div class="cet-shop-toolbar__right" style="flex:1; justify-content:flex-end;">
						<input
							type="search"
							name="cet_q"
							value="<?php echo esc_attr( $search_q ); ?>"
							placeholder="<?php esc_attr_e( 'Filter by name or SKU...', 'custom-ecommerce-theme' ); ?>"
							style="max-width:230px;"
						>
						<select name="cet_sort" onchange="this.form.submit();" aria-label="<?php esc_attr_e( 'Sort Products', 'custom-ecommerce-theme' ); ?>" style="max-width:210px;">
							<option value="featured" <?php selected( $active_sort, 'featured' ); ?>><?php esc_html_e( 'Sort: Newest First', 'custom-ecommerce-theme' ); ?></option>
							<option value="price_asc" <?php selected( $active_sort, 'price_asc' ); ?>><?php esc_html_e( 'Price: Low to High', 'custom-ecommerce-theme' ); ?></option>
							<option value="price_desc" <?php selected( $active_sort, 'price_desc' ); ?>><?php esc_html_e( 'Price: High to Low', 'custom-ecommerce-theme' ); ?></option>
							<option value="rated" <?php selected( $active_sort, 'rated' ); ?>><?php esc_html_e( 'Top Rated', 'custom-ecommerce-theme' ); ?></option>
						</select>
						<button type="submit" class="cet-btn cet-btn--gold cet-btn--sm">
							<?php esc_html_e( 'Filter', 'custom-ecommerce-theme' ); ?>
						</button>
					</div>
				</form>

				<?php if ( empty( $wc_prods ) ) : ?>
					<div class="cet-empty-state">
						<h3><?php esc_html_e( 'No Matching Products Found', 'custom-ecommerce-theme' ); ?></h3>
						<p><?php esc_html_e( 'Try clearing your filter or searching for a different term.', 'custom-ecommerce-theme' ); ?></p>
						<a href="<?php echo esc_url( remove_query_arg( array( 'cet_cat', 'cet_q', 'cet_sort' ), $shop_url ) ); ?>" class="cet-btn cet-btn--gold">
							<?php esc_html_e( 'Show All Products', 'custom-ecommerce-theme' ); ?>
						</a>
					</div>
				<?php else : ?>
					<div class="cet-products-grid">
						<?php
						foreach ( $wc_prods as $prod ) {
							set_query_var( 'cet_card_product', $prod );
							get_template_part( 'template-parts/product/product-card' );
						}
						?>
					</div>
				<?php endif; ?>
			</div>
			<?php
			return (string) ob_get_clean();
		}
	}

	$categories = function_exists( 'cet_get_showcase_categories' ) ? cet_get_showcase_categories() : array();
	$products   = function_exists( 'cet_get_showcase_products' ) ? cet_get_showcase_products() : array();

	$filtered = array();
	foreach ( $products as $item ) {
		$cat_slug = sanitize_title( (string) ( $item['category'] ?? '' ) );
		if ( $active_cat && $cat_slug !== $active_cat ) {
			continue;
		}
		if ( $search_q ) {
			$haystack = strtolower( $item['title'] . ' ' . $item['category'] . ' ' . $item['sku'] );
			if ( false === strpos( $haystack, strtolower( $search_q ) ) ) {
				continue;
			}
		}
		$filtered[] = $item;
	}

	if ( 'price_asc' === $active_sort ) {
		usort( $filtered, static fn( $a, $b ) => (float) $a['price'] <=> (float) $b['price'] );
	} elseif ( 'price_desc' === $active_sort ) {
		usort( $filtered, static fn( $a, $b ) => (float) $b['price'] <=> (float) $a['price'] );
	} elseif ( 'rated' === $active_sort ) {
		usort( $filtered, static fn( $a, $b ) => (int) $b['reviews'] <=> (int) $a['reviews'] );
	}

	ob_start();
	?>
	<div class="cet-shop-page-wrap">
		<!-- Category Filter Pills -->
		<div class="cet-shop-cat-pills">
			<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--sm <?php echo '' === $active_cat ? 'cet-btn--primary' : 'cet-btn--outline'; ?>">
				<?php esc_html_e( 'All Departments', 'custom-ecommerce-theme' ); ?>
			</a>
			<?php foreach ( $categories as $cat ) : ?>
				<?php
				$slug     = sanitize_title( (string) $cat['name'] );
				$cat_link = add_query_arg( 'cet_cat', $slug, $shop_url );
				?>
				<a href="<?php echo esc_url( $cat_link ); ?>" class="cet-btn cet-btn--sm <?php echo $active_cat === $slug ? 'cet-btn--primary' : 'cet-btn--outline'; ?>">
					<?php echo esc_html( (string) $cat['name'] ); ?>
				</a>
			<?php endforeach; ?>
		</div>

		<!-- Toolbar: Filter Count, Search & Sort -->
		<form method="get" action="<?php echo esc_url( $shop_url ); ?>" class="cet-shop-toolbar">
			<?php if ( $page_id_val > 0 ) : ?>
				<input type="hidden" name="page_id" value="<?php echo esc_attr( (string) $page_id_val ); ?>">
			<?php endif; ?>
			<?php if ( $active_cat ) : ?>
				<input type="hidden" name="cet_cat" value="<?php echo esc_attr( $active_cat ); ?>">
			<?php endif; ?>

			<div class="cet-shop-toolbar__left">
				<span class="woocommerce-result-count">
					<?php
					printf(
						/* translators: %d: number of matching products */
						esc_html( _n( 'Showing %d luxury piece', 'Showing %d luxury pieces', count( $filtered ), 'custom-ecommerce-theme' ) ),
						count( $filtered )
					);
					?>
				</span>
			</div>

			<div class="cet-shop-toolbar__right" style="flex:1; justify-content:flex-end;">
				<input
					type="search"
					name="cet_q"
					value="<?php echo esc_attr( $search_q ); ?>"
					placeholder="<?php esc_attr_e( 'Filter by name or SKU...', 'custom-ecommerce-theme' ); ?>"
					style="max-width:230px;"
				>
				<select name="cet_sort" onchange="this.form.submit();" aria-label="<?php esc_attr_e( 'Sort Products', 'custom-ecommerce-theme' ); ?>" style="max-width:210px;">
					<option value="featured" <?php selected( $active_sort, 'featured' ); ?>><?php esc_html_e( 'Sort: Featured Collection', 'custom-ecommerce-theme' ); ?></option>
					<option value="price_asc" <?php selected( $active_sort, 'price_asc' ); ?>><?php esc_html_e( 'Price: Low to High', 'custom-ecommerce-theme' ); ?></option>
					<option value="price_desc" <?php selected( $active_sort, 'price_desc' ); ?>><?php esc_html_e( 'Price: High to Low', 'custom-ecommerce-theme' ); ?></option>
					<option value="rated" <?php selected( $active_sort, 'rated' ); ?>><?php esc_html_e( 'Most Reviewed', 'custom-ecommerce-theme' ); ?></option>
				</select>
				<button type="submit" class="cet-btn cet-btn--gold cet-btn--sm">
					<?php esc_html_e( 'Filter', 'custom-ecommerce-theme' ); ?>
				</button>
			</div>
		</form>

		<?php if ( empty( $filtered ) ) : ?>
			<div class="cet-empty-state">
				<h3><?php esc_html_e( 'No Matching Products Found', 'custom-ecommerce-theme' ); ?></h3>
				<p><?php esc_html_e( 'Try clearing your filter or browsing all signature pieces.', 'custom-ecommerce-theme' ); ?></p>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--gold">
					<?php esc_html_e( 'Show All Products', 'custom-ecommerce-theme' ); ?>
				</a>
			</div>
		<?php else : ?>
			<div class="cet-products-grid">
				<?php
				foreach ( $filtered as $item ) {
					if ( function_exists( 'cet_render_showcase_product_card' ) ) {
						cet_render_showcase_product_card( $item );
					}
				}
				?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'cet_shop', 'cet_shop_shortcode' );

/**
 * Shortcode `[cet_cart]` — Full Shopping Bag Page (uses WooCommerce cart when populated, else theme bag).
 *
 * @return string
 */
function cet_cart_shortcode(): string {
	if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty() ) {
		return do_shortcode( '[woocommerce_cart]' );
	}

	return cet_render_cart_page_html();
}
add_shortcode( 'cet_cart', 'cet_cart_shortcode' );

/**
 * Shortcode `[cet_checkout]` — Full Delivery & Payment Checkout Page.
 *
 * @return string
 */
function cet_checkout_shortcode(): string {
	if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty() ) {
		return do_shortcode( '[woocommerce_checkout]' );
	}

	return cet_render_checkout_portal_markup( true );
}
add_shortcode( 'cet_checkout', 'cet_checkout_shortcode' );

/**
 * Shortcode `[cet_track_order]` — Interactive Order Status & Insured Delivery Tracker Page.
 *
 * @return string
 */
function cet_track_order_shortcode(): string {
	$lookup_ref   = isset( $_GET['order_ref'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_GET['order_ref'] ) ) ) : '';
	$lookup_email = isset( $_GET['order_email'] ) ? sanitize_email( wp_unslash( $_GET['order_email'] ) ) : '';
	$track_url    = cet_get_store_page_url( 'track-order' );
	$page_id_val  = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : 0;

	$all_orders = get_option( 'cet_store_orders', array() );
	if ( ! is_array( $all_orders ) ) {
		$all_orders = array();
	}

	$found_order = null;
	if ( '' !== $lookup_ref ) {
		$clean_ref = ltrim( $lookup_ref, '#' );
		foreach ( array_reverse( $all_orders ) as $ord ) {
			if ( ! is_array( $ord ) ) {
				continue;
			}
			$ord_num = strtoupper( (string) ( $ord['order_number'] ?? '' ) );
			$ord_em  = strtolower( (string) ( $ord['email'] ?? '' ) );
			if ( $ord_num === $clean_ref && ( '' === $lookup_email || $ord_em === strtolower( $lookup_email ) ) ) {
				$found_order = $ord;
				break;
			}
		}
	}

	$recent_orders = function_exists( 'cet_get_customer_orders' ) ? cet_get_customer_orders() : array();

	ob_start();
	?>
	<div class="cet-track-order-wrap">
		<div class="cet-card" style="padding:1.75rem; max-width:720px; margin:0 auto 2rem; border-top:3px solid var(--color-accent);">
			<span class="cet-eyebrow"><?php esc_html_e( 'Concierge Logistics', 'custom-ecommerce-theme' ); ?></span>
			<h2 style="margin-bottom:0.5rem;"><?php esc_html_e( 'Track Your Delivery & Order Status', 'custom-ecommerce-theme' ); ?></h2>
			<p style="color:var(--color-text-muted); margin-bottom:1.25rem;">
				<?php esc_html_e( 'Enter your Order Reference (for example CET-2026-1234) to inspect fulfillment milestones and courier arrival ETA.', 'custom-ecommerce-theme' ); ?>
			</p>

			<form method="get" action="<?php echo esc_url( $track_url ); ?>">
				<?php if ( $page_id_val > 0 ) : ?>
					<input type="hidden" name="page_id" value="<?php echo esc_attr( (string) $page_id_val ); ?>">
				<?php endif; ?>

				<div class="cet-form-row-2" style="margin-bottom:1rem;">
					<div>
						<label class="cet-field-label"><?php esc_html_e( 'Order Number *', 'custom-ecommerce-theme' ); ?></label>
						<input type="text" name="order_ref" required value="<?php echo esc_attr( $lookup_ref ); ?>" placeholder="CET-2026-1234">
					</div>
					<div>
						<label class="cet-field-label"><?php esc_html_e( 'Billing Email (Optional)', 'custom-ecommerce-theme' ); ?></label>
						<input type="email" name="order_email" value="<?php echo esc_attr( $lookup_email ); ?>" placeholder="client@example.com">
					</div>
				</div>

				<button type="submit" class="cet-btn cet-btn--gold">
					<?php esc_html_e( 'Track Order Now', 'custom-ecommerce-theme' ); ?>
				</button>
			</form>
		</div>

		<?php if ( '' !== $lookup_ref ) : ?>
			<?php if ( $found_order ) : ?>
				<div class="cet-card" style="padding:1.75rem; max-width:720px; margin:0 auto 2rem; border:1px solid var(--color-accent);">
					<div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:0.75rem; margin-bottom:1.25rem; padding-bottom:1rem; border-bottom:1px solid var(--color-border);">
						<div>
							<span class="cet-eyebrow"><?php esc_html_e( 'Shipment Found', 'custom-ecommerce-theme' ); ?></span>
							<h3 style="margin:0;">#<?php echo esc_html( (string) $found_order['order_number'] ); ?></h3>
						</div>
						<span class="cet-badge cet-badge--instock"><?php echo esc_html( (string) ( $found_order['status'] ?? __( 'Confirmed & Preparing', 'custom-ecommerce-theme' ) ) ); ?></span>
					</div>

					<div class="cet-form-row-2" style="font-size:0.92rem; margin-bottom:1rem;">
						<div>
							<strong><?php esc_html_e( 'Courier Service:', 'custom-ecommerce-theme' ); ?></strong>
							<div><?php echo esc_html( (string) ( $found_order['delivery_method'] ?? __( 'Insured Express Delivery', 'custom-ecommerce-theme' ) ) ); ?></div>
						</div>
						<div>
							<strong><?php esc_html_e( 'Estimated Delivery:', 'custom-ecommerce-theme' ); ?></strong>
							<div style="color:var(--color-success); font-weight:700;"><?php echo esc_html( (string) ( $found_order['eta'] ?? '' ) ); ?></div>
						</div>
						<div>
							<strong><?php esc_html_e( 'Payment Method:', 'custom-ecommerce-theme' ); ?></strong>
							<div><?php echo esc_html( (string) ( $found_order['payment'] ?? '' ) ); ?></div>
						</div>
						<div>
							<strong><?php esc_html_e( 'Order Total:', 'custom-ecommerce-theme' ); ?></strong>
							<div style="font-weight:800; color:var(--color-primary);">$<?php echo esc_html( number_format( (float) ( $found_order['total'] ?? 0 ), 2 ) ); ?></div>
						</div>
					</div>
				</div>
			<?php else : ?>
				<div class="woocommerce-error" style="max-width:720px; margin:0 auto 2rem;">
					<?php esc_html_e( 'No order matched that reference number. Please double-check your confirmation code.', 'custom-ecommerce-theme' ); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( ! empty( $recent_orders ) ) : ?>
			<div style="max-width:860px; margin:0 auto;">
				<h3 style="margin-bottom:0.9rem;"><?php esc_html_e( 'Your Recent Storefront Orders', 'custom-ecommerce-theme' ); ?></h3>
				<div class="cet-table-scroll">
					<table class="shop_table" style="width:100%; border-collapse:collapse; font-size:0.86rem;">
						<thead>
							<tr>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Order #', 'custom-ecommerce-theme' ); ?></th>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Delivery ETA', 'custom-ecommerce-theme' ); ?></th>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Payment', 'custom-ecommerce-theme' ); ?></th>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Total', 'custom-ecommerce-theme' ); ?></th>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Status', 'custom-ecommerce-theme' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $recent_orders as $ord ) : ?>
								<tr>
									<td style="padding:0.8rem 1rem; font-weight:700;">
										<a href="<?php echo esc_url( add_query_arg( 'order_ref', (string) ( $ord['order_number'] ?? '' ), $track_url ) ); ?>">
											#<?php echo esc_html( (string) ( $ord['order_number'] ?? '' ) ); ?>
										</a>
									</td>
									<td style="padding:0.8rem 1rem;"><?php echo esc_html( (string) ( $ord['eta'] ?? '' ) ); ?></td>
									<td style="padding:0.8rem 1rem;"><?php echo esc_html( (string) ( $ord['payment'] ?? '' ) ); ?></td>
									<td style="padding:0.8rem 1rem; font-weight:800;">$<?php echo esc_html( number_format( (float) ( $ord['total'] ?? 0 ), 2 ) ); ?></td>
									<td style="padding:0.8rem 1rem;"><span class="cet-badge cet-badge--instock"><?php echo esc_html( (string) ( $ord['status'] ?? __( 'Confirmed', 'custom-ecommerce-theme' ) ) ); ?></span></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'cet_track_order', 'cet_track_order_shortcode' );
