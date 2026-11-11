<?php
/**
 * Secure AJAX Handlers for Shopping Bag, Checkout (Delivery & Payment),
 * Wishlist Modal, Live Search, Quick View, Product Comparison & Newsletter
 *
 * Supports both live WooCommerce database products and built-in Showcase products (`9001`–`9008`).
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve the current visitor's wishlist product IDs (from user meta if logged in, or sanitized cookie).
 *
 * @return array<int, int>
 */
function cet_get_wishlist_ids(): array {
	$ids = array();

	if ( isset( $_COOKIE['cet_wishlist_ids'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_COOKIE['cet_wishlist_ids'] ) );
		$ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
	}

	if ( is_user_logged_in() ) {
		$saved = get_user_meta( get_current_user_id(), '_cet_wishlist_ids', true );
		if ( is_array( $saved ) ) {
			$ids = array_merge( $ids, array_filter( array_map( 'absint', $saved ) ) );
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Retrieve the current visitor's compare product IDs from sanitized cookie.
 *
 * @return array<int, int>
 */
function cet_get_compare_ids(): array {
	if ( ! isset( $_COOKIE['cet_compare_ids'] ) ) {
		return array();
	}

	$raw = sanitize_text_field( wp_unslash( $_COOKIE['cet_compare_ids'] ) );
	return array_slice( array_values( array_unique( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) ) ), 0, 4 );
}

/**
 * Retrieve the visitor's recently viewed product IDs from cookie.
 *
 * @return array<int, int>
 */
function cet_get_recently_viewed_ids(): array {
	if ( empty( $_COOKIE['cet_recently_viewed'] ) ) {
		return array();
	}

	$raw = sanitize_text_field( wp_unslash( $_COOKIE['cet_recently_viewed'] ) );
	return array_slice( array_values( array_unique( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) ) ), 0, 12 );
}

/**
 * Track single product visits inside cookie for the "Recently Viewed" section.
 *
 * @return void
 */
function cet_track_recently_viewed_product(): void {
	if ( ! is_singular( 'product' ) ) {
		return;
	}

	$product_id = get_queried_object_id();
	if ( ! $product_id ) {
		return;
	}

	$viewed = array_diff( cet_get_recently_viewed_ids(), array( $product_id ) );
	array_unshift( $viewed, $product_id );
	$viewed = array_slice( $viewed, 0, 12 );

	setcookie( 'cet_recently_viewed', implode( ',', $viewed ), time() + ( 30 * DAY_IN_SECONDS ), '/', '', is_ssl(), false );
}
add_action( 'template_redirect', 'cet_track_recently_viewed_product' );

/**
 * Retrieve the current visitor's Shopping Bag items `[ product_id => qty ]`.
 *
 * @return array<int, int>
 */
function cet_get_bag_items(): array {
	$bag = array();

	if ( isset( $_COOKIE['cet_bag_items'] ) ) {
		$raw   = sanitize_text_field( wp_unslash( $_COOKIE['cet_bag_items'] ) );
		$pairs = explode( ',', $raw );
		foreach ( $pairs as $pair ) {
			if ( strpos( $pair, ':' ) !== false ) {
				list( $pid, $qty ) = explode( ':', $pair, 2 );
				$pid = absint( $pid );
				$qty = absint( $qty );
				if ( $pid > 0 && $qty > 0 ) {
					$bag[ $pid ] = $qty;
				}
			}
		}
	}

	if ( empty( $bag ) && is_user_logged_in() ) {
		$saved = get_user_meta( get_current_user_id(), '_cet_bag_items', true );
		if ( is_array( $saved ) ) {
			foreach ( $saved as $pid => $qty ) {
				$pid = absint( $pid );
				$qty = absint( $qty );
				if ( $pid > 0 && $qty > 0 ) {
					$bag[ $pid ] = $qty;
				}
			}
		}
	}

	return $bag;
}

/**
 * Save Shopping Bag items for logged-in user and return cookie string `id:qty,id:qty`.
 *
 * @param array<int, int> $bag Bag items `[ product_id => qty ]`.
 * @return string Encoded cookie string.
 */
function cet_encode_and_save_bag( array $bag ): string {
	$clean = array();
	$parts = array();

	foreach ( $bag as $pid => $qty ) {
		$pid = absint( $pid );
		$qty = absint( $qty );
		if ( $pid > 0 && $qty > 0 ) {
			$clean[ $pid ] = $qty;
			$parts[]       = $pid . ':' . $qty;
		}
	}

	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), '_cet_bag_items', $clean );
	}

	$encoded = implode( ',', $parts );
	if ( ! headers_sent() ) {
		setcookie( 'cet_bag_items', $encoded, time() + ( 30 * DAY_IN_SECONDS ), '/', '', is_ssl(), false );
	}

	return $encoded;
}

/**
 * Get total count of items across the Shopping Bag (plus WooCommerce cart if active).
 *
 * @param array<int, int>|null $bag_override Optional bag array.
 * @return int
 */
function cet_get_bag_total_count( ?array $bag_override = null ): int {
	$bag   = ( null !== $bag_override ) ? $bag_override : cet_get_bag_items();
	$count = array_sum( $bag );

	if ( 0 === $count && function_exists( 'WC' ) && WC()->cart ) {
		$count = (int) WC()->cart->get_cart_contents_count();
	}

	return $count;
}

/**
 * Resolve product data by ID from either Showcase Catalog (`9001`–`9008`) or WooCommerce `wc_get_product()`.
 *
 * @param int $product_id Product ID.
 * @return array<string, mixed>|null
 */
function cet_resolve_product_data( int $product_id ): ?array {
	$showcase = function_exists( 'cet_get_showcase_products' ) ? cet_get_showcase_products() : array();

	if ( isset( $showcase[ $product_id ] ) ) {
		$s = $showcase[ $product_id ];
		return array(
			'id'         => $product_id,
			'title'      => (string) $s['title'],
			'category'   => (string) $s['category'],
			'price'      => (float) $s['price'],
			'price_html' => (string) $s['price_html'],
			'sku'        => (string) $s['sku'],
			'svg_type'   => (string) $s['svg_type'],
			'image_url'  => '',
			'url'        => home_url( '/#signature-products' ),
		);
	}

	if ( function_exists( 'wc_get_product' ) ) {
		$prod = wc_get_product( $product_id );
		if ( $prod && $prod->is_visible() ) {
			$img_id  = $prod->get_image_id();
			$img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : '';
			return array(
				'id'         => $product_id,
				'title'      => $prod->get_name(),
				'category'   => wp_strip_all_tags( wc_get_product_category_list( $product_id, ', ' ) ),
				'price'      => (float) $prod->get_price(),
				'price_html' => $prod->get_price_html(),
				'sku'        => $prod->get_sku() ?: 'WC-' . $product_id,
				'svg_type'   => 'watch',
				'image_url'  => (string) $img_url,
				'url'        => $prod->get_permalink(),
			);
		}
	}

	return null;
}

/**
 * Render the complete HTML inside `.cet-mini-cart-body` for the Shopping Bag Drawer.
 *
 * @param array<int, int>|null $bag_override Optional bag array.
 * @return string
 */
function cet_render_bag_drawer_html( ?array $bag_override = null ): string {
	$summary   = function_exists( 'cet_resolve_bag_summary' ) ? cet_resolve_bag_summary( $bag_override ) : array();
	$resolved  = $summary['items'] ?? array();
	$subtotal  = (float) ( $summary['subtotal'] ?? 0.0 );
	$remaining = (float) ( $summary['remaining'] ?? 0.0 );
	$shop_url  = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'shop' ) : home_url( '/#signature-products' );
	$cart_url  = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'cart' ) : home_url( '/#signature-products' );
	$check_url = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'checkout' ) : home_url( '/#signature-products' );

	ob_start();
	?>
	<!-- Free Shipping Progress Bar -->
	<?php
	if ( function_exists( 'cet_render_shipping_goal_markup' ) ) {
		echo cet_render_shipping_goal_markup( $summary ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>

	<?php if ( empty( $resolved ) ) : ?>
		<div class="woocommerce-mini-cart__empty-message" style="text-align:center; padding:2.5rem 1rem;">
			<p style="color:#5A6578; margin-bottom:1.25rem;"><?php esc_html_e( 'No products in your shopping bag yet.', 'custom-ecommerce-theme' ); ?></p>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="cet-btn cet-btn--gold cet-btn--sm">
				<?php esc_html_e( 'Explore Luxury Catalog', 'custom-ecommerce-theme' ); ?>
			</a>
		</div>
	<?php else : ?>
		<ul class="cet-bag-items-list" style="list-style:none; margin:0; padding:0;">
			<?php foreach ( $resolved as $item ) : ?>
				<li class="cet-bag-item" style="display:flex; align-items:center; gap:0.9rem; padding:0.95rem 0; border-bottom:1px solid #E8EDF5;">
					<div style="width:68px; height:68px; flex-shrink:0; border-radius:8px; overflow:hidden; border:1px solid #D6AD65; background:#14243A;">
						<?php if ( ! empty( $item['image_url'] ) ) : ?>
							<img src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" style="width:100%; height:100%; object-fit:cover;">
						<?php else : ?>
							<?php echo cet_get_showcase_product_svg( $item['svg_type'], $item['sku'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>

					<div style="flex:1; min-width:0;">
						<h4 style="margin:0 0 0.25rem; font-size:0.92rem; color:#14243A; font-family:var(--font-heading);">
							<?php echo esc_html( $item['title'] ); ?>
						</h4>
						<div style="font-size:0.82rem; color:#C4974C; font-weight:700; margin-bottom:0.45rem;">
							$<?php echo esc_html( number_format( (float) $item['price'], 2 ) ); ?> &times; <?php echo esc_html( (string) $item['qty'] ); ?>
							= <strong style="color:#14243A;">$<?php echo esc_html( number_format( (float) $item['line_total'], 2 ) ); ?></strong>
						</div>

						<div class="cet-bag-qty-control">
							<button type="button" class="cet-bag-qty-btn" data-product-id="<?php echo esc_attr( (string) $item['id'] ); ?>" data-delta="-1">-</button>
							<span><?php echo esc_html( (string) $item['qty'] ); ?></span>
							<button type="button" class="cet-bag-qty-btn" data-product-id="<?php echo esc_attr( (string) $item['id'] ); ?>" data-delta="1">+</button>
						</div>
					</div>

					<button type="button" class="cet-icon-action-btn cet-bag-remove-btn" data-product-id="<?php echo esc_attr( (string) $item['id'] ); ?>" style="width:32px; height:32px;" aria-label="<?php esc_attr_e( 'Remove item', 'custom-ecommerce-theme' ); ?>">
						&times;
					</button>
				</li>
			<?php endforeach; ?>
		</ul>

		<!-- Bag Totals & Delivery Summary -->
		<div style="margin-top:1.25rem; padding-top:1rem; border-top:2px solid #E8EDF5;">
			<div style="display:flex; justify-content:space-between; margin-bottom:0.45rem; font-size:0.88rem; color:#5A6578;">
				<span><?php esc_html_e( 'Estimated Delivery:', 'custom-ecommerce-theme' ); ?></span>
				<strong style="color:#16803C;">
					<?php echo $remaining <= 0 ? esc_html__( 'FREE (Insured Express)', 'custom-ecommerce-theme' ) : '$18.00'; ?>
				</strong>
			</div>
			<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; font-size:1.15rem; font-weight:800; color:#14243A;">
				<span><?php esc_html_e( 'Bag Subtotal:', 'custom-ecommerce-theme' ); ?></span>
				<span>$<?php echo esc_html( number_format( $subtotal, 2 ) ); ?></span>
			</div>

			<div style="display:flex; flex-direction:column; gap:0.65rem;">
				<button type="button" class="cet-btn cet-btn--gold" id="cet-open-checkout-modal" style="width:100%;">
					<?php esc_html_e( 'Proceed to Delivery & Payment', 'custom-ecommerce-theme' ); ?>
					<?php echo cet_get_svg_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<div class="cet-form-row-2" style="margin-bottom:0;">
					<a href="<?php echo esc_url( $cart_url ); ?>" class="cet-btn cet-btn--outline cet-btn--sm" style="width:100%;">
						<?php esc_html_e( 'View Bag Page', 'custom-ecommerce-theme' ); ?>
					</a>
					<a href="<?php echo esc_url( $check_url ); ?>" class="cet-btn cet-btn--primary cet-btn--sm" style="width:100%;">
						<?php esc_html_e( 'Checkout Page', 'custom-ecommerce-theme' ); ?>
					</a>
				</div>
				<button type="button" class="cet-btn cet-btn--outline cet-btn--sm" id="cet-clear-bag-btn" style="width:100%;">
					<?php esc_html_e( 'Clear Shopping Bag', 'custom-ecommerce-theme' ); ?>
				</button>
			</div>
		</div>
	<?php endif; ?>
	<?php
	return (string) ob_get_clean();
}

/**
 * AJAX Handler: Add, Update Quantity, Remove, or Clear Shopping Bag Items.
 *
 * @return void
 */
function cet_ajax_bag_action(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$sub_action = isset( $_POST['sub_action'] ) ? sanitize_key( wp_unslash( $_POST['sub_action'] ) ) : 'add';
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$qty        = isset( $_POST['qty'] ) ? (int) $_POST['qty'] : 1;

	$bag     = cet_get_bag_items();
	$message = '';

	if ( 'add' === $sub_action && $product_id > 0 ) {
		$current            = isset( $bag[ $product_id ] ) ? (int) $bag[ $product_id ] : 0;
		$bag[ $product_id ] = $current + max( 1, $qty );
		$pdata              = cet_resolve_product_data( $product_id );
		$message            = $pdata
			/* translators: %s: Product name. */
			? sprintf( esc_html__( 'Added "%s" to your Shopping Bag.', 'custom-ecommerce-theme' ), $pdata['title'] )
			: esc_html__( 'Added to your Shopping Bag.', 'custom-ecommerce-theme' );
	} elseif ( 'update_qty' === $sub_action && $product_id > 0 ) {
		$current = isset( $bag[ $product_id ] ) ? (int) $bag[ $product_id ] : 0;
		$new_qty = $current + $qty;
		if ( $new_qty <= 0 ) {
			unset( $bag[ $product_id ] );
			$message = esc_html__( 'Item removed from your Shopping Bag.', 'custom-ecommerce-theme' );
		} else {
			$bag[ $product_id ] = $new_qty;
			$message            = esc_html__( 'Shopping Bag quantity updated.', 'custom-ecommerce-theme' );
		}
	} elseif ( 'remove' === $sub_action && $product_id > 0 ) {
		unset( $bag[ $product_id ] );
		$message = esc_html__( 'Item removed from your Shopping Bag.', 'custom-ecommerce-theme' );
	} elseif ( 'clear' === $sub_action ) {
		$bag     = array();
		$message = esc_html__( 'Shopping Bag cleared.', 'custom-ecommerce-theme' );
	}

	$cookie_val    = cet_encode_and_save_bag( $bag );
	$total_cnt     = cet_get_bag_total_count( $bag );
	$drawer_htm    = cet_render_bag_drawer_html( $bag );
	$cart_page_htm = function_exists( 'cet_render_cart_page_html' ) ? cet_render_cart_page_html( $bag ) : '';

	wp_send_json_success(
		array(
			'count'        => $total_cnt,
			'cookieValue'  => $cookie_val,
			'drawerHtml'   => $drawer_htm,
			'cartPageHtml' => $cart_page_htm,
			'message'      => $message,
		)
	);
}
add_action( 'wp_ajax_cet_bag_action', 'cet_ajax_bag_action' );
add_action( 'wp_ajax_nopriv_cet_bag_action', 'cet_ajax_bag_action' );

/**
 * AJAX Handler: Open Saved Wishlist Modal (Displays all saved Wishlist products with Move to Bag & Remove).
 *
 * @return void
 */
function cet_ajax_get_wishlist_modal(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$raw_ids = isset( $_POST['wishlist_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_ids'] ) ) : '';
	if ( ! empty( $raw_ids ) ) {
		$ids = array_values( array_unique( array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) ) ) );
	} else {
		$ids = cet_get_wishlist_ids();
	}

	$wishlist_page_url = function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'wishlist' ) : home_url( '/#signature-products' );

	ob_start();
	?>
	<div class="cet-wishlist-modal-wrap">
		<div class="cet-section-header" style="margin-bottom:1.5rem;">
			<div>
				<span class="cet-eyebrow"><?php esc_html_e( 'Personal Curation', 'custom-ecommerce-theme' ); ?></span>
				<h2 class="cet-section-title" style="margin:0;"><?php esc_html_e( 'Your Saved Wishlist', 'custom-ecommerce-theme' ); ?></h2>
			</div>
			<a href="<?php echo esc_url( $wishlist_page_url ); ?>" class="cet-btn cet-btn--outline cet-btn--sm">
				<?php esc_html_e( 'Open Wishlist Page', 'custom-ecommerce-theme' ); ?>
			</a>
		</div>

		<?php if ( empty( $ids ) ) : ?>
			<div style="text-align:center; padding:2.5rem 1rem;">
				<p style="color:#5A6578; margin-bottom:1.25rem;"><?php esc_html_e( 'Your Wishlist is currently empty. Click the heart icon on any product to save it here.', 'custom-ecommerce-theme' ); ?></p>
				<button type="button" class="cet-btn cet-btn--gold cet-btn--sm" onclick="window.CET.closeModal();">
					<?php esc_html_e( 'Continue Browsing', 'custom-ecommerce-theme' ); ?>
				</button>
			</div>
		<?php else : ?>
			<div class="cet-wishlist-grid">
				<?php foreach ( $ids as $pid ) : ?>
					<?php
					$pdata = cet_resolve_product_data( (int) $pid );
					if ( ! $pdata ) {
						continue;
					}
					?>
					<div class="cet-card" style="padding:1rem; display:flex; flex-direction:column; gap:0.75rem; border:1px solid #E8EDF5;">
						<div style="height:160px; border-radius:8px; overflow:hidden; background:#14243A;">
							<?php if ( ! empty( $pdata['image_url'] ) ) : ?>
								<img src="<?php echo esc_url( $pdata['image_url'] ); ?>" alt="<?php echo esc_attr( $pdata['title'] ); ?>" style="width:100%; height:100%; object-fit:cover;">
							<?php else : ?>
								<?php echo cet_get_showcase_product_svg( $pdata['svg_type'], $pdata['sku'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endif; ?>
						</div>
						<div>
							<div style="font-size:0.72rem; color:#C4974C; font-weight:700; text-transform:uppercase;"><?php echo esc_html( $pdata['category'] ); ?></div>
							<h4 style="margin:0.2rem 0 0.4rem; font-size:0.95rem; color:#14243A;"><?php echo esc_html( $pdata['title'] ); ?></h4>
							<div style="font-weight:800; color:#14243A;"><?php echo wp_kses_post( $pdata['price_html'] ); ?></div>
						</div>
						<div style="margin-top:auto; display:flex; gap:0.5rem;">
							<button type="button" class="cet-btn cet-btn--gold cet-btn--sm cet-add-to-bag-btn" data-product-id="<?php echo esc_attr( (string) $pdata['id'] ); ?>" data-qty="1" style="flex:1;">
								<?php esc_html_e( 'Move to Bag', 'custom-ecommerce-theme' ); ?>
							</button>
							<button type="button" class="cet-btn cet-btn--outline cet-btn--sm cet-wishlist-toggle is-active" data-product-id="<?php echo esc_attr( (string) $pdata['id'] ); ?>" data-refresh-wishlist-modal="1">
								<?php esc_html_e( 'Remove', 'custom-ecommerce-theme' ); ?>
							</button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	wp_send_json_success( array( 'html' => (string) ob_get_clean() ) );
}
add_action( 'wp_ajax_cet_get_wishlist_modal', 'cet_ajax_get_wishlist_modal' );
add_action( 'wp_ajax_nopriv_cet_get_wishlist_modal', 'cet_ajax_get_wishlist_modal' );

/**
 * AJAX Handler: Render Complete Delivery, Shipping & Payment Checkout Modal.
 *
 * @return void
 */
function cet_ajax_get_checkout_modal(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$summary = function_exists( 'cet_resolve_bag_summary' ) ? cet_resolve_bag_summary() : array( 'items' => array() );
	if ( empty( $summary['items'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Your shopping bag is empty.', 'custom-ecommerce-theme' ) ) );
	}

	if ( function_exists( 'cet_render_checkout_portal_markup' ) ) {
		wp_send_json_success( array( 'html' => cet_render_checkout_portal_markup( false ) ) );
	}

	wp_send_json_error( array( 'message' => esc_html__( 'Checkout portal unavailable.', 'custom-ecommerce-theme' ) ) );
}
add_action( 'wp_ajax_cet_get_checkout_modal', 'cet_ajax_get_checkout_modal' );
add_action( 'wp_ajax_nopriv_cet_get_checkout_modal', 'cet_ajax_get_checkout_modal' );

/**
 * AJAX Handler: Process & Place Customer Order (Delivery & Payment Confirmation).
 *
 * @return void
 */
function cet_ajax_place_order(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$name           = isset( $_POST['customer_name'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_name'] ) ) : '';
	$email          = isset( $_POST['customer_email'] ) ? sanitize_email( wp_unslash( $_POST['customer_email'] ) ) : '';
	$phone          = isset( $_POST['customer_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_phone'] ) ) : '';
	$address        = isset( $_POST['customer_address'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_address'] ) ) : '';
	$city           = isset( $_POST['customer_city'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_city'] ) ) : '';
	$zip            = isset( $_POST['customer_zip'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_zip'] ) ) : '';
	$delivery       = isset( $_POST['delivery_method'] ) ? sanitize_text_field( wp_unslash( $_POST['delivery_method'] ) ) : 'standard';
	$payment_method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : 'Cash on Delivery (COD)';

	if ( empty( $name ) || ! is_email( $email ) || empty( $address ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Please fill in all required delivery fields.', 'custom-ecommerce-theme' ) ) );
	}

	$bag         = cet_get_bag_items();
	$subtotal    = 0.0;
	$order_items = array();

	foreach ( $bag as $pid => $qty ) {
		$pdata = cet_resolve_product_data( (int) $pid );
		if ( $pdata ) {
			$line_total = $pdata['price'] * (int) $qty;
			$subtotal  += $line_total;
			$order_items[] = array(
				'id'    => (int) $pid,
				'title' => $pdata['title'],
				'qty'   => (int) $qty,
				'total' => $line_total,
			);
		}
	}

	if ( empty( $order_items ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Your shopping bag is empty.', 'custom-ecommerce-theme' ) ) );
	}

	$free_threshold = (float) cet_get_option( 'free_shipping_threshold', 150 );
	$delivery_cost  = ( 'priority' === $delivery ) ? 25.0 : ( $subtotal >= $free_threshold ? 0.0 : 18.0 );
	$grand_total    = $subtotal + $delivery_cost;
	$order_number   = 'CET-' . gmdate( 'Y' ) . '-' . wp_rand( 1000, 9999 );
	$eta_date       = ( 'priority' === $delivery )
		? gmdate( 'F j, Y', strtotime( '+1 day' ) )
		: gmdate( 'F j, Y', strtotime( '+3 days' ) );

	// Save order record in WordPress option log.
	$orders = get_option( 'cet_store_orders', array() );
	if ( ! is_array( $orders ) ) {
		$orders = array();
	}
	$orders[] = array(
		'order_number'    => $order_number,
		'user_id'         => get_current_user_id(),
		'name'            => $name,
		'email'           => $email,
		'phone'           => $phone,
		'address'         => $address . ', ' . $city . ' ' . $zip,
		'delivery_method' => ( 'priority' === $delivery ) ? __( 'White-Glove Next-Day Courier', 'custom-ecommerce-theme' ) : __( 'Insured Express Delivery', 'custom-ecommerce-theme' ),
		'eta'             => $eta_date,
		'payment'         => $payment_method,
		'items'           => $order_items,
		'total'           => $grand_total,
		'status'          => __( 'Confirmed & Preparing', 'custom-ecommerce-theme' ),
		'date'            => current_time( 'mysql' ),
	);
	update_option( 'cet_store_orders', $orders, false );

	// Remember order reference for this browser so Account Portal can show it even for guest shoppers.
	$ref_cookie = array();
	if ( isset( $_COOKIE['cet_my_orders'] ) ) {
		$ref_cookie = array_filter( array_map( 'sanitize_text_field', explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['cet_my_orders'] ) ) ) ) );
	}
	$ref_cookie[] = $order_number;
	$ref_cookie   = array_slice( array_unique( $ref_cookie ), -10 );
	if ( ! headers_sent() ) {
		setcookie( 'cet_my_orders', implode( ',', $ref_cookie ), time() + ( 90 * DAY_IN_SECONDS ), '/', '', is_ssl(), false );
	}

	// Clear the visitor's shopping bag.
	cet_encode_and_save_bag( array() );

	ob_start();
	?>
	<div style="text-align:center; padding:1.5rem;">
		<div style="width:64px; height:64px; border-radius:50%; background:#14243A; color:#D6AD65; border:2px solid #D6AD65; display:inline-flex; align-items:center; justify-content:center; margin-bottom:1rem;">
			<?php echo cet_get_svg_icon( 'check', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<span class="cet-eyebrow" style="display:block;"><?php esc_html_e( 'Order Confirmed & Scheduled', 'custom-ecommerce-theme' ); ?></span>
		<h2 style="margin-bottom:0.5rem;"><?php esc_html_e( 'Thank You for Your Order!', 'custom-ecommerce-theme' ); ?></h2>
		<p style="color:#5A6578; max-width:520px; margin:0 auto 1.5rem;">
			<?php
			printf(
				/* translators: 1: Customer name, 2: Order number. */
				esc_html__( 'Dear %1$s, your luxury order %2$s has been registered with our fulfillment concierge.', 'custom-ecommerce-theme' ),
				esc_html( $name ),
				'<strong>#' . esc_html( $order_number ) . '</strong>'
			);
			?>
		</p>

		<div style="background:#F5F7FA; border:1px solid #D6AD65; border-radius:10px; padding:1.5rem; max-width:560px; margin:0 auto 1.5rem; text-align:left;">
			<div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
				<span><?php esc_html_e( 'Order Reference:', 'custom-ecommerce-theme' ); ?></span>
				<strong style="color:#14243A;">#<?php echo esc_html( $order_number ); ?></strong>
			</div>
			<div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
				<span><?php esc_html_e( 'Delivery Address:', 'custom-ecommerce-theme' ); ?></span>
				<strong><?php echo esc_html( $address . ', ' . $city ); ?></strong>
			</div>
			<div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
				<span><?php esc_html_e( 'Estimated Delivery Date:', 'custom-ecommerce-theme' ); ?></span>
				<strong style="color:#16803C;"><?php echo esc_html( $eta_date ); ?></strong>
			</div>
			<div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
				<span><?php esc_html_e( 'Payment Method:', 'custom-ecommerce-theme' ); ?></span>
				<strong><?php echo esc_html( $payment_method ); ?></strong>
			</div>
			<div style="display:flex; justify-content:space-between; padding-top:0.5rem; border-top:1px solid #E8EDF5; font-size:1.1rem;">
				<span><?php esc_html_e( 'Total Amount:', 'custom-ecommerce-theme' ); ?></span>
				<strong style="color:#14243A;">$<?php echo esc_html( number_format( $grand_total, 2 ) ); ?></strong>
			</div>
		</div>

		<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:0.75rem;">
			<a href="<?php echo esc_url( function_exists( 'cet_get_store_page_url' ) ? add_query_arg( 'order_ref', $order_number, cet_get_store_page_url( 'track-order' ) ) : home_url( '/' ) ); ?>" class="cet-btn cet-btn--primary">
				<?php esc_html_e( 'Track This Order', 'custom-ecommerce-theme' ); ?>
			</a>
			<a href="<?php echo esc_url( function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'shop' ) : home_url( '/#signature-products' ) ); ?>" class="cet-btn cet-btn--gold" onclick="if(window.CET){window.CET.closeModal();}">
				<?php esc_html_e( 'Continue Shopping', 'custom-ecommerce-theme' ); ?>
			</a>
		</div>
	</div>
	<?php
	wp_send_json_success(
		array(
			'html'         => (string) ob_get_clean(),
			'drawerHtml'   => cet_render_bag_drawer_html( array() ),
			'cartPageHtml' => function_exists( 'cet_render_cart_page_html' ) ? cet_render_cart_page_html( array() ) : '',
			'count'        => 0,
		)
	);
}
add_action( 'wp_ajax_cet_place_order', 'cet_ajax_place_order' );
add_action( 'wp_ajax_nopriv_cet_place_order', 'cet_ajax_place_order' );

/**
 * AJAX Handler: Live Product Search with Category Filter.
 *
 * @return void
 */
function cet_ajax_live_search(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$search_term = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
	$category    = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( $_POST['category'] ) ) : '';

	if ( strlen( $search_term ) < 2 ) {
		wp_send_json_success( array( 'results' => array() ) );
	}

	$post_type = class_exists( 'WooCommerce' ) ? 'product' : 'post';

	$query_args = array(
		'post_type'           => $post_type,
		'post_status'         => 'publish',
		's'                   => $search_term,
		'posts_per_page'      => 6,
		'ignore_sticky_posts' => true,
	);

	if ( 'product' === $post_type && ! empty( $category ) ) {
		$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$search_query = new WP_Query( $query_args );
	$results      = array();

	if ( $search_query->have_posts() ) {
		while ( $search_query->have_posts() ) {
			$search_query->the_post();
			$post_id = get_the_ID();

			if ( 'product' === $post_type && function_exists( 'wc_get_product' ) ) {
				$product = wc_get_product( $post_id );
				if ( ! $product || ! $product->is_visible() ) {
					continue;
				}

				$thumb_url = get_the_post_thumbnail_url( $post_id, 'woocommerce_gallery_thumbnail' );
				if ( ! $thumb_url && function_exists( 'wc_placeholder_img_src' ) ) {
					$thumb_url = wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' );
				}

				$results[] = array(
					'id'         => $post_id,
					'title'      => wp_strip_all_tags( $product->get_name() ),
					'url'        => esc_url( get_permalink( $post_id ) ),
					'price_html' => wp_kses_post( $product->get_price_html() ),
					'sku'        => esc_html( $product->get_sku() ),
					'image'      => esc_url( (string) $thumb_url ),
					'in_stock'   => $product->is_in_stock(),
				);
			} else {
				$results[] = array(
					'id'         => $post_id,
					'title'      => wp_strip_all_tags( get_the_title() ),
					'url'        => esc_url( get_permalink( $post_id ) ),
					'price_html' => esc_html__( 'Article', 'custom-ecommerce-theme' ),
					'sku'        => '',
					'image'      => esc_url( (string) get_the_post_thumbnail_url( $post_id, 'thumbnail' ) ),
					'in_stock'   => true,
				);
			}
		}
		wp_reset_postdata();
	}

	// Match showcase products when no database products matched.
	if ( empty( $results ) && function_exists( 'cet_get_showcase_products' ) ) {
		foreach ( cet_get_showcase_products() as $s_prod ) {
			if ( false !== stripos( $s_prod['title'], $search_term ) || false !== stripos( $s_prod['category'], $search_term ) || false !== stripos( $s_prod['sku'], $search_term ) ) {
				$results[] = array(
					'id'         => $s_prod['id'],
					'title'      => $s_prod['title'],
					'url'        => esc_url( home_url( '/#signature-products' ) ),
					'price_html' => wp_kses_post( $s_prod['price_html'] ),
					'sku'        => $s_prod['sku'],
					'image'      => '',
					'in_stock'   => true,
				);
			}
		}
	}

	wp_send_json_success(
		array(
			'results'  => $results,
			'view_all' => esc_url( add_query_arg( array( 's' => $search_term ), home_url( '/' ) ) ),
		)
	);
}
add_action( 'wp_ajax_cet_live_search', 'cet_ajax_live_search' );
add_action( 'wp_ajax_nopriv_cet_live_search', 'cet_ajax_live_search' );

/**
 * AJAX Handler: Product Quick View Modal Content (Supports both WC_Product and Showcase items).
 *
 * @return void
 */
function cet_ajax_quick_view(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$product_id    = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$showcase_list = function_exists( 'cet_get_showcase_products' ) ? cet_get_showcase_products() : array();

	// 1. Built-in Showcase products (9001–9008).
	if ( isset( $showcase_list[ $product_id ] ) ) {
		$s_item = $showcase_list[ $product_id ];
		ob_start();
		?>
		<div class="cet-quickview-grid">
			<div class="cet-quickview__media" style="border-radius:var(--radius-md); overflow:hidden;">
				<?php echo cet_get_showcase_product_svg( (string) $s_item['svg_type'], (string) $s_item['sku'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="cet-quickview__details">
				<span class="cet-eyebrow"><?php echo esc_html( (string) $s_item['category'] ); ?></span>
				<h2 class="cet-quickview__title" style="margin-bottom:0.5rem;"><?php echo esc_html( (string) $s_item['title'] ); ?></h2>
				<div class="cet-quickview__meta" style="display:flex; align-items:center; gap:0.85rem; margin-bottom:1rem; font-size:0.85rem;">
					<span class="cet-stars">★★★★★</span>
					<span><?php esc_html_e( 'SKU:', 'custom-ecommerce-theme' ); ?> <strong><?php echo esc_html( (string) $s_item['sku'] ); ?></strong></span>
					<span class="cet-badge cet-badge--instock"><?php esc_html_e( 'In Stock', 'custom-ecommerce-theme' ); ?></span>
				</div>
				<div class="cet-product-card__price" style="font-size:1.5rem; margin-bottom:1rem;">
					<?php echo wp_kses_post( (string) $s_item['price_html'] ); ?>
				</div>
				<p style="color:#5A6578; margin-bottom:1.25rem;">
					<?php echo esc_html( (string) $s_item['description'] ); ?>
				</p>
				<div class="cet-product-trust-box" style="margin-bottom:1.25rem;">
					<div class="cet-trust-row">
						<?php echo cet_get_svg_icon( 'truck', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php esc_html_e( 'Complimentary Insured Express Delivery on orders over $150', 'custom-ecommerce-theme' ); ?></span>
					</div>
					<div class="cet-trust-row">
						<?php echo cet_get_svg_icon( 'shield', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php esc_html_e( 'Cash on Delivery (COD), Visa, Mastercard & Apple Pay Accepted', 'custom-ecommerce-theme' ); ?></span>
					</div>
				</div>
				<div style="display:flex; flex-wrap:wrap; gap:0.85rem; align-items:center;">
					<div class="cet-qty-stepper">
						<button type="button" class="cet-qty-btn cet-qty-minus">-</button>
						<input type="number" class="qty" id="cet-qv-qty-input" value="1" min="1" max="10">
						<button type="button" class="cet-qty-btn cet-qty-plus">+</button>
					</div>
					<button type="button" class="cet-btn cet-btn--gold cet-add-to-bag-btn" data-product-id="<?php echo esc_attr( (string) $product_id ); ?>" data-qty-selector="#cet-qv-qty-input">
						<?php esc_html_e( 'Add to Shopping Bag', 'custom-ecommerce-theme' ); ?>
					</button>
					<button type="button" class="cet-btn cet-btn--primary cet-buy-now-instant-btn" data-product-id="<?php echo esc_attr( (string) $product_id ); ?>" data-qty-selector="#cet-qv-qty-input">
						<?php esc_html_e( 'Buy Now — Instant Checkout', 'custom-ecommerce-theme' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
		wp_send_json_success( array( 'html' => (string) ob_get_clean() ) );
	}

	// 2. Standard WooCommerce Product Lookup.
	if ( ! function_exists( 'wc_get_product' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Product not found.', 'custom-ecommerce-theme' ) ) );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product || ! $product->is_visible() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Product not found.', 'custom-ecommerce-theme' ) ) );
	}

	$image_id  = $product->get_image_id();
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_single' ) : wc_placeholder_img_src( 'woocommerce_single' );
	$cats      = wc_get_product_category_list( $product_id, ', ' );

	ob_start();
	?>
	<div class="cet-quickview-grid">
		<div class="cet-quickview__media">
			<img src="<?php echo esc_url( (string) $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="cet-quickview__img">
		</div>
		<div class="cet-quickview__details">
			<?php if ( $cats ) : ?>
				<div class="cet-quickview__cats"><?php echo wp_kses_post( $cats ); ?></div>
			<?php endif; ?>
			<h2 class="cet-quickview__title">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
			</h2>
			<div class="cet-quickview__price">
				<?php echo wp_kses_post( $product->get_price_html() ); ?>
			</div>
			<div class="cet-quickview__desc">
				<?php echo wp_kses_post( wpautop( $product->get_short_description() ?: wp_trim_words( $product->get_description(), 28 ) ) ); ?>
			</div>
			<div class="cet-quickview__actions" style="display:flex; gap:0.75rem; flex-wrap:wrap;">
				<button type="button" class="cet-btn cet-btn--gold cet-add-to-bag-btn" data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>" data-qty="1">
					<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
				</button>
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cet-btn cet-btn--outline">
					<?php esc_html_e( 'View Full Details', 'custom-ecommerce-theme' ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
	wp_send_json_success( array( 'html' => (string) ob_get_clean() ) );
}
add_action( 'wp_ajax_cet_quick_view', 'cet_ajax_quick_view' );
add_action( 'wp_ajax_nopriv_cet_quick_view', 'cet_ajax_quick_view' );

/**
 * AJAX Handler: Toggle Wishlist Item for Logged-in Users & Guests.
 *
 * @return void
 */
function cet_ajax_toggle_wishlist(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid product.', 'custom-ecommerce-theme' ) ) );
	}

	$wishlist = cet_get_wishlist_ids();
	$added    = false;

	$index = array_search( $product_id, $wishlist, true );
	if ( false !== $index ) {
		unset( $wishlist[ $index ] );
		$wishlist = array_values( $wishlist );
	} else {
		$wishlist[] = $product_id;
		$added      = true;
	}

	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), '_cet_wishlist_ids', $wishlist );
	}

	if ( ! headers_sent() ) {
		setcookie( 'cet_wishlist_ids', implode( ',', $wishlist ), time() + ( 30 * DAY_IN_SECONDS ), '/', '', is_ssl(), false );
	}

	wp_send_json_success(
		array(
			'added'    => $added,
			'count'    => count( $wishlist ),
			'ids'      => $wishlist,
			'message'  => $added
				? esc_html__( 'Saved to your Wishlist! Click the Heart icon in the header to view.', 'custom-ecommerce-theme' )
				: esc_html__( 'Removed from your Wishlist.', 'custom-ecommerce-theme' ),
		)
	);
}
add_action( 'wp_ajax_cet_toggle_wishlist', 'cet_ajax_toggle_wishlist' );
add_action( 'wp_ajax_nopriv_cet_toggle_wishlist', 'cet_ajax_toggle_wishlist' );

/**
 * AJAX Handler: Render Product Comparison Table Modal.
 *
 * @return void
 */
function cet_ajax_compare_products(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$raw_ids = isset( $_POST['product_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['product_ids'] ) ) : '';
	$ids     = array_slice( array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) ), 0, 4 );

	if ( empty( $ids ) ) {
		wp_send_json_success(
			array(
				'html' => '<div class="cet-empty-state"><p>' . esc_html__( 'No products selected for comparison yet.', 'custom-ecommerce-theme' ) . '</p></div>',
			)
		);
	}

	$rows = array();
	foreach ( $ids as $id ) {
		$pdata = cet_resolve_product_data( (int) $id );
		if ( $pdata ) {
			$rows[] = $pdata;
		}
	}

	ob_start();
	?>
	<div class="cet-compare-table-wrap">
		<table class="cet-compare-table shop_table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Product', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $rows as $r ) : ?>
						<td><strong><?php echo esc_html( (string) $r['title'] ); ?></strong></td>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Price', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $rows as $r ) : ?>
						<td class="cet-compare-price"><?php echo wp_kses_post( (string) $r['price_html'] ); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'SKU', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $rows as $r ) : ?>
						<td><?php echo esc_html( (string) $r['sku'] ); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Action', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $rows as $r ) : ?>
						<td>
							<button type="button" class="cet-btn cet-btn--gold cet-btn--sm cet-add-to-bag-btn" data-product-id="<?php echo esc_attr( (string) $r['id'] ); ?>" data-qty="1">
								<?php esc_html_e( 'Add to Bag', 'custom-ecommerce-theme' ); ?>
							</button>
						</td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
	wp_send_json_success( array( 'html' => (string) ob_get_clean() ) );
}
add_action( 'wp_ajax_cet_compare_products', 'cet_ajax_compare_products' );
add_action( 'wp_ajax_nopriv_cet_compare_products', 'cet_ajax_compare_products' );

/**
 * AJAX Handler: Newsletter Subscription Form.
 *
 * @return void
 */
function cet_ajax_newsletter_subscribe(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Please enter a valid email address.', 'custom-ecommerce-theme' ),
			)
		);
	}

	$subscribers = get_option( 'cet_newsletter_subscribers', array() );
	if ( ! is_array( $subscribers ) ) {
		$subscribers = array();
	}

	if ( ! in_array( $email, $subscribers, true ) ) {
		$subscribers[] = $email;
		update_option( 'cet_newsletter_subscribers', $subscribers, false );
		do_action( 'cet_newsletter_subscribed', $email );
	}

	wp_send_json_success(
		array(
			'message' => esc_html__( 'Thank you for subscribing to our private concierge list.', 'custom-ecommerce-theme' ),
		)
	);
}
add_action( 'wp_ajax_cet_newsletter_subscribe', 'cet_ajax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_cet_newsletter_subscribe', 'cet_ajax_newsletter_subscribe' );

/**
 * Retrieve orders belonging to the current customer (logged-in user or current browser order refs).
 *
 * @return array<int, array<string, mixed>>
 */
function cet_get_customer_orders(): array {
	$all_orders = get_option( 'cet_store_orders', array() );
	if ( ! is_array( $all_orders ) ) {
		return array();
	}

	$current_user = is_user_logged_in() ? wp_get_current_user() : null;
	$user_id      = $current_user ? (int) $current_user->ID : 0;
	$user_email   = $current_user ? strtolower( (string) $current_user->user_email ) : '';

	$cookie_refs = array();
	if ( isset( $_COOKIE['cet_my_orders'] ) ) {
		$cookie_refs = array_filter( array_map( 'sanitize_text_field', explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['cet_my_orders'] ) ) ) ) );
	}

	$matched = array();
	foreach ( array_reverse( $all_orders ) as $order ) {
		if ( ! is_array( $order ) ) {
			continue;
		}
		$order_uid   = isset( $order['user_id'] ) ? (int) $order['user_id'] : 0;
		$order_email = isset( $order['email'] ) ? strtolower( (string) $order['email'] ) : '';
		$order_num   = isset( $order['order_number'] ) ? (string) $order['order_number'] : '';

		if (
			( $user_id > 0 && $order_uid === $user_id ) ||
			( '' !== $user_email && $order_email === $user_email ) ||
			( '' !== $order_num && in_array( $order_num, $cookie_refs, true ) )
		) {
			$matched[] = $order;
		}
	}

	return $matched;
}

/**
 * Render the Theme's Custom Client Account Portal HTML (Sign In, Create Account, or Client Dashboard).
 *
 * @param string $active_tab Optional initial tab ('login' or 'register').
 * @return string
 */
function cet_render_account_portal_html( string $active_tab = 'login' ): string {
	ob_start();

	if ( is_user_logged_in() ) {
		$user          = wp_get_current_user();
		$orders        = cet_get_customer_orders();
		$wishlist_cnt  = count( cet_get_wishlist_ids() );
		$bag_cnt       = cet_get_bag_total_count();
		$initials      = strtoupper( substr( (string) ( $user->display_name ?: $user->user_login ), 0, 2 ) );
		?>
		<div class="cet-account-portal cet-account-portal--dashboard">
			<div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; padding-bottom:1.25rem; margin-bottom:1.5rem; border-bottom:1px solid #E8EDF5;">
				<div style="display:flex; align-items:center; gap:1rem;">
					<div style="width:54px; height:54px; border-radius:50%; background:#14243A; color:#D6AD65; border:2px solid #D6AD65; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem;">
						<?php echo esc_html( $initials ); ?>
					</div>
					<div>
						<span class="cet-eyebrow" style="display:block; margin-bottom:0.1rem;"><?php esc_html_e( 'Private Client Account', 'custom-ecommerce-theme' ); ?></span>
						<h2 style="margin:0; font-size:1.4rem; color:#14243A;"><?php echo esc_html( $user->display_name ?: $user->user_login ); ?></h2>
						<div style="font-size:0.84rem; color:#5A6578;"><?php echo esc_html( $user->user_email ); ?></div>
					</div>
				</div>

				<div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.65rem;">
					<a href="<?php echo esc_url( function_exists( 'cet_get_store_page_url' ) ? cet_get_store_page_url( 'track-order' ) : home_url( '/' ) ); ?>" class="cet-btn cet-btn--outline cet-btn--sm">
						<?php esc_html_e( 'Track Order', 'custom-ecommerce-theme' ); ?>
					</a>
					<?php if ( current_user_can( 'manage_options' ) ) : ?>
						<a href="<?php echo esc_url( admin_url() ); ?>" class="cet-btn cet-btn--outline cet-btn--sm">
							<?php esc_html_e( 'WP Dashboard', 'custom-ecommerce-theme' ); ?>
						</a>
					<?php endif; ?>
					<button type="button" id="cet-account-logout-btn" class="cet-btn cet-btn--primary cet-btn--sm">
						<?php esc_html_e( 'Sign Out', 'custom-ecommerce-theme' ); ?>
					</button>
				</div>
			</div>

			<!-- Quick Client Summary Cards -->
			<div class="cet-account-summary-grid">
				<div style="background:#F5F7FA; border:1px solid #E8EDF5; border-radius:8px; padding:1rem 1.25rem;">
					<div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:#5A6578;"><?php esc_html_e( 'Placed Orders', 'custom-ecommerce-theme' ); ?></div>
					<div style="font-size:1.65rem; font-weight:800; color:#14243A; margin:0.2rem 0;"><?php echo esc_html( (string) count( $orders ) ); ?></div>
					<div style="font-size:0.78rem; color:#16803C;"><?php esc_html_e( 'Concierge fulfillment tracked', 'custom-ecommerce-theme' ); ?></div>
				</div>

				<div style="background:#F5F7FA; border:1px solid #E8EDF5; border-radius:8px; padding:1rem 1.25rem;">
					<div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:#5A6578;"><?php esc_html_e( 'Saved Wishlist', 'custom-ecommerce-theme' ); ?></div>
					<div style="font-size:1.65rem; font-weight:800; color:#14243A; margin:0.2rem 0;"><?php echo esc_html( (string) $wishlist_cnt ); ?></div>
					<button type="button" class="cet-btn cet-btn--outline cet-btn--sm" onclick="document.getElementById('cet-open-wishlist-info')?.click();">
						<?php esc_html_e( 'View Wishlist', 'custom-ecommerce-theme' ); ?>
					</button>
				</div>

				<div style="background:#F5F7FA; border:1px solid #E8EDF5; border-radius:8px; padding:1rem 1.25rem;">
					<div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:#5A6578;"><?php esc_html_e( 'Shopping Bag', 'custom-ecommerce-theme' ); ?></div>
					<div style="font-size:1.65rem; font-weight:800; color:#14243A; margin:0.2rem 0;"><?php echo esc_html( (string) $bag_cnt ); ?></div>
					<button type="button" class="cet-btn cet-btn--gold cet-btn--sm" onclick="window.CET.closeModal(); document.getElementById('cet-cart-drawer-trigger')?.click();">
						<?php esc_html_e( 'Open Bag', 'custom-ecommerce-theme' ); ?>
					</button>
				</div>
			</div>

			<!-- Recent Orders Table -->
			<h3 style="font-size:1.05rem; margin-bottom:0.85rem; color:#14243A;">
				<?php esc_html_e( 'Recent Orders & Delivery Tracking', 'custom-ecommerce-theme' ); ?>
			</h3>
			<?php if ( empty( $orders ) ) : ?>
				<div style="background:#F5F7FA; border:1px solid #E8EDF5; border-radius:8px; padding:1.5rem; text-align:center;">
					<p style="margin:0; color:#5A6578;"><?php esc_html_e( 'You have not placed any orders yet. Add an item to your bag to experience express insured checkout.', 'custom-ecommerce-theme' ); ?></p>
				</div>
			<?php else : ?>
				<div class="cet-table-scroll">
					<table class="shop_table" style="width:100%; margin:0; border-collapse:collapse; font-size:0.86rem;">
						<thead>
							<tr style="background:#14243A; color:#FFFFFF;">
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Order #', 'custom-ecommerce-theme' ); ?></th>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Items', 'custom-ecommerce-theme' ); ?></th>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Payment & Delivery', 'custom-ecommerce-theme' ); ?></th>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Total', 'custom-ecommerce-theme' ); ?></th>
								<th style="padding:0.75rem 1rem; text-align:left;"><?php esc_html_e( 'Status', 'custom-ecommerce-theme' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $orders as $ord ) : ?>
								<tr style="border-bottom:1px solid #E8EDF5;">
									<td style="padding:0.85rem 1rem; font-weight:700; color:#14243A;">
										#<?php echo esc_html( (string) ( $ord['order_number'] ?? '' ) ); ?>
										<div style="font-size:0.74rem; color:#5A6578; font-weight:400;"><?php echo esc_html( (string) ( $ord['date'] ?? '' ) ); ?></div>
									</td>
									<td style="padding:0.85rem 1rem;">
										<?php
										if ( ! empty( $ord['items'] ) && is_array( $ord['items'] ) ) {
											foreach ( $ord['items'] as $oit ) {
												echo '<div style="margin-bottom:0.2rem;">' . esc_html( (string) ( $oit['title'] ?? 'Item' ) ) . ' &times; ' . esc_html( (string) ( $oit['qty'] ?? 1 ) ) . '</div>';
											}
										} else {
											esc_html_e( 'Concierge Order', 'custom-ecommerce-theme' );
										}
										?>
									</td>
									<td style="padding:0.85rem 1rem;">
										<div style="font-weight:600; color:#14243A;"><?php echo esc_html( (string) ( $ord['payment'] ?? '' ) ); ?></div>
										<div style="font-size:0.76rem; color:#5A6578;">
											<?php echo esc_html( (string) ( $ord['delivery_method'] ?? __( 'Insured Express', 'custom-ecommerce-theme' ) ) ); ?>
											<?php if ( ! empty( $ord['eta'] ) ) : ?>
												&bull; <?php echo esc_html( sprintf( /* translators: %s: delivery ETA */ __( 'ETA: %s', 'custom-ecommerce-theme' ), (string) $ord['eta'] ) ); ?>
											<?php endif; ?>
										</div>
									</td>
									<td style="padding:0.85rem 1rem; font-weight:800; color:#14243A;">
										$<?php echo esc_html( number_format( (float) ( $ord['total'] ?? 0 ), 2 ) ); ?>
									</td>
									<td style="padding:0.85rem 1rem;">
										<span class="cet-badge cet-badge--instock"><?php echo esc_html( (string) ( $ord['status'] ?? __( 'Confirmed', 'custom-ecommerce-theme' ) ) ); ?></span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
		<?php
	} else {
		$is_reg = ( 'register' === $active_tab );
		$orders = cet_get_customer_orders();
		?>
		<div class="cet-account-portal cet-account-portal--auth">
			<div style="text-align:center; margin-bottom:1.5rem;">
				<span class="cet-eyebrow"><?php esc_html_e( 'Client Access', 'custom-ecommerce-theme' ); ?></span>
				<h2 class="cet-section-title" style="margin:0.25rem 0 0.35rem;"><?php esc_html_e( 'Sign In or Create Account', 'custom-ecommerce-theme' ); ?></h2>
				<p style="margin:0; font-size:0.9rem; color:#5A6578;"><?php esc_html_e( 'Access your saved wishlist, faster express delivery checkout, and order tracking.', 'custom-ecommerce-theme' ); ?></p>
			</div>

			<!-- Tab Switcher -->
			<div class="cet-account-tabs" style="display:flex; justify-content:center; gap:0.75rem; margin-bottom:1.5rem;">
				<button type="button" class="cet-account-tab-btn cet-btn cet-btn--sm <?php echo $is_reg ? 'cet-btn--outline' : 'cet-btn--primary'; ?>" data-target="login">
					<?php esc_html_e( 'Sign In', 'custom-ecommerce-theme' ); ?>
				</button>
				<button type="button" class="cet-account-tab-btn cet-btn cet-btn--sm <?php echo $is_reg ? 'cet-btn--primary' : 'cet-btn--outline'; ?>" data-target="register">
					<?php esc_html_e( 'Create Account', 'custom-ecommerce-theme' ); ?>
				</button>
			</div>

			<div class="cet-account-feedback" style="display:none; max-width:480px; margin:0 auto 1rem; padding:0.75rem 1rem; border-radius:6px; font-size:0.86rem; font-weight:600;"></div>

			<!-- Sign In Form -->
			<form id="cet-account-login-form" style="<?php echo $is_reg ? 'display:none;' : ''; ?> max-width:480px; margin:0 auto;">
				<div style="margin-bottom:0.95rem;">
					<label style="font-size:0.82rem; font-weight:700; display:block; margin-bottom:0.3rem;"><?php esc_html_e( 'Username or Email Address *', 'custom-ecommerce-theme' ); ?></label>
					<input type="text" name="log" required autocomplete="username" placeholder="you@example.com">
				</div>
				<div style="margin-bottom:0.95rem;">
					<label style="font-size:0.82rem; font-weight:700; display:block; margin-bottom:0.3rem;"><?php esc_html_e( 'Password *', 'custom-ecommerce-theme' ); ?></label>
					<input type="password" name="pwd" required autocomplete="current-password" placeholder="••••••••">
				</div>
				<label style="display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; margin-bottom:1.25rem; cursor:pointer;">
					<input type="checkbox" name="remember" value="1" checked style="width:auto;">
					<span><?php esc_html_e( 'Keep me signed in on this browser', 'custom-ecommerce-theme' ); ?></span>
				</label>
				<button type="submit" class="cet-btn cet-btn--gold cet-btn--lg" style="width:100%;">
					<?php esc_html_e( 'Sign In to Account', 'custom-ecommerce-theme' ); ?>
				</button>
			</form>

			<!-- Create Account Form -->
			<form id="cet-account-register-form" style="<?php echo $is_reg ? '' : 'display:none;'; ?> max-width:480px; margin:0 auto;">
				<div class="cet-form-row-2">
					<div>
						<label class="cet-field-label"><?php esc_html_e( 'Full Name *', 'custom-ecommerce-theme' ); ?></label>
						<input type="text" name="full_name" required placeholder="Aaqib Mustafa">
					</div>
					<div>
						<label class="cet-field-label"><?php esc_html_e( 'Phone (Optional)', 'custom-ecommerce-theme' ); ?></label>
						<input type="tel" name="phone" placeholder="+1 (800) 555-0199">
					</div>
				</div>
				<div class="cet-form-field">
					<label class="cet-field-label"><?php esc_html_e( 'Email Address *', 'custom-ecommerce-theme' ); ?></label>
					<input type="email" name="email" required autocomplete="email" placeholder="you@example.com">
				</div>
				<div class="cet-form-row-2" style="margin-bottom:1.25rem;">
					<div>
						<label class="cet-field-label"><?php esc_html_e( 'Choose Username *', 'custom-ecommerce-theme' ); ?></label>
						<input type="text" name="username" required autocomplete="username" placeholder="aaqib_client">
					</div>
					<div>
						<label class="cet-field-label"><?php esc_html_e( 'Create Password *', 'custom-ecommerce-theme' ); ?></label>
						<input type="password" name="password" required minlength="6" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Min 6 characters', 'custom-ecommerce-theme' ); ?>">
					</div>
				</div>
				<button type="submit" class="cet-btn cet-btn--gold cet-btn--lg" style="width:100%;">
					<?php esc_html_e( 'Create Client Account', 'custom-ecommerce-theme' ); ?>
				</button>
			</form>

			<?php if ( ! empty( $orders ) ) : ?>
				<div style="max-width:480px; margin:1.75rem auto 0; padding-top:1.25rem; border-top:1px solid #E8EDF5;">
					<h4 style="font-size:0.88rem; margin-bottom:0.65rem; color:#14243A;"><?php esc_html_e( 'Recent Guest Orders on This Browser:', 'custom-ecommerce-theme' ); ?></h4>
					<?php foreach ( $orders as $ord ) : ?>
						<div style="display:flex; justify-content:space-between; font-size:0.82rem; padding:0.5rem 0.75rem; background:#F5F7FA; border-radius:6px; margin-bottom:0.4rem;">
							<strong>#<?php echo esc_html( (string) ( $ord['order_number'] ?? '' ) ); ?></strong>
							<span><?php echo esc_html( (string) ( $ord['status'] ?? __( 'Confirmed', 'custom-ecommerce-theme' ) ) ); ?></span>
							<strong style="color:#14243A;">$<?php echo esc_html( number_format( (float) ( $ord['total'] ?? 0 ), 2 ) ); ?></strong>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	return (string) ob_get_clean();
}

/**
 * AJAX Handler: Open Client Account Portal Modal (never exposes wp-login.php to shoppers).
 *
 * @return void
 */
function cet_ajax_get_account_modal(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$tab = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : 'login';
	wp_send_json_success(
		array(
			'html'       => cet_render_account_portal_html( $tab ),
			'isLoggedIn' => is_user_logged_in(),
		)
	);
}
add_action( 'wp_ajax_cet_get_account_modal', 'cet_ajax_get_account_modal' );
add_action( 'wp_ajax_nopriv_cet_get_account_modal', 'cet_ajax_get_account_modal' );

/**
 * AJAX Handler: Authenticate Shopper inside Theme Account Modal.
 *
 * @return void
 */
function cet_ajax_account_login(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$login    = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
	$password = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$remember = ! empty( $_POST['remember'] );

	if ( '' === $login || '' === $password ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Please enter both username/email and password.', 'custom-ecommerce-theme' ) ) );
	}

	$user = wp_signon(
		array(
			'user_login'    => $login,
			'user_password' => $password,
			'remember'      => $remember,
		),
		is_ssl()
	);

	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid login credentials. Please try again or create a new account.', 'custom-ecommerce-theme' ) ) );
	}

	wp_set_current_user( $user->ID );

	// Sync any guest Wishlist & Shopping Bag items cleanly into user meta on sign-in.
	$merged_wish = cet_get_wishlist_ids();
	update_user_meta( $user->ID, '_cet_wishlist_ids', $merged_wish );

	$merged_bag = cet_get_bag_items();
	update_user_meta( $user->ID, '_cet_bag_items', $merged_bag );

	wp_send_json_success(
		array(
			'html'          => cet_render_account_portal_html(),
			'isLoggedIn'    => true,
			'label'         => esc_html__( 'Account', 'custom-ecommerce-theme' ),
			'wishlistCount' => count( $merged_wish ),
			'bagCount'      => cet_get_bag_total_count( $merged_bag ),
			'bagDrawerHtml' => cet_render_bag_drawer_html( $merged_bag ),
			'message'       => sprintf(
				/* translators: %s: User display name. */
				esc_html__( 'Welcome back, %s!', 'custom-ecommerce-theme' ),
				$user->display_name ?: $user->user_login
			),
		)
	);
}
add_action( 'wp_ajax_cet_account_login', 'cet_ajax_account_login' );
add_action( 'wp_ajax_nopriv_cet_account_login', 'cet_ajax_account_login' );

/**
 * AJAX Handler: Create New Customer Account directly inside Theme Modal (Works with or without WooCommerce).
 *
 * @return void
 */
function cet_ajax_account_register(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	$full_name = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
	$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$username  = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ), true ) : '';
	$password  = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$phone     = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

	if ( '' === $full_name || ! is_email( $email ) || '' === $username || strlen( $password ) < 6 ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Please complete all required fields (password minimum 6 characters).', 'custom-ecommerce-theme' ) ) );
	}

	if ( username_exists( $username ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'That username is already registered. Please choose another or Sign In.', 'custom-ecommerce-theme' ) ) );
	}

	if ( email_exists( $email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'An account with this email address already exists. Please Sign In.', 'custom-ecommerce-theme' ) ) );
	}

	$role    = wp_roles()->is_role( 'customer' ) ? 'customer' : get_option( 'default_role', 'subscriber' );
	$parts   = explode( ' ', $full_name, 2 );
	$user_id = wp_insert_user(
		array(
			'user_login'   => $username,
			'user_email'   => $email,
			'user_pass'    => $password,
			'display_name' => $full_name,
			'first_name'   => $parts[0] ?? $full_name,
			'last_name'    => $parts[1] ?? '',
			'role'         => $role,
		)
	);

	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => esc_html( $user_id->get_error_message() ) ) );
	}

	if ( '' !== $phone ) {
		update_user_meta( $user_id, '_cet_phone', $phone );
		update_user_meta( $user_id, 'billing_phone', $phone );
	}

	// Sync existing guest Wishlist & Shopping Bag to newly created account.
	$guest_wish = cet_get_wishlist_ids();
	if ( ! empty( $guest_wish ) ) {
		update_user_meta( $user_id, '_cet_wishlist_ids', $guest_wish );
	}
	$guest_bag = cet_get_bag_items();
	if ( ! empty( $guest_bag ) ) {
		update_user_meta( $user_id, '_cet_bag_items', $guest_bag );
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true, is_ssl() );

	wp_send_json_success(
		array(
			'html'          => cet_render_account_portal_html(),
			'isLoggedIn'    => true,
			'label'         => esc_html__( 'Account', 'custom-ecommerce-theme' ),
			'wishlistCount' => count( $guest_wish ),
			'bagCount'      => cet_get_bag_total_count( $guest_bag ),
			'bagDrawerHtml' => cet_render_bag_drawer_html( $guest_bag ),
			'message'       => esc_html__( 'Your account has been created and you are now signed in!', 'custom-ecommerce-theme' ),
		)
	);
}
add_action( 'wp_ajax_cet_account_register', 'cet_ajax_account_register' );
add_action( 'wp_ajax_nopriv_cet_account_register', 'cet_ajax_account_register' );

/**
 * AJAX Handler: Sign Out Shopper without leaving the current page.
 *
 * @return void
 */
function cet_ajax_account_logout(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	wp_logout();
	wp_set_current_user( 0 );

	wp_send_json_success(
		array(
			'html'       => cet_render_account_portal_html( 'login' ),
			'isLoggedIn' => false,
			'label'      => esc_html__( 'Sign In', 'custom-ecommerce-theme' ),
			'message'    => esc_html__( 'You have been signed out cleanly.', 'custom-ecommerce-theme' ),
		)
	);
}
add_action( 'wp_ajax_cet_account_logout', 'cet_ajax_account_logout' );
add_action( 'wp_ajax_nopriv_cet_account_logout', 'cet_ajax_account_logout' );
