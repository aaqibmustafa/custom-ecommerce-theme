<?php
/**
 * Secure AJAX Handlers for Live Search, Quick View, Wishlist, Product Comparison & Newsletter
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
	if ( is_user_logged_in() ) {
		$saved = get_user_meta( get_current_user_id(), '_cet_wishlist_ids', true );
		if ( is_array( $saved ) ) {
			return array_values( array_unique( array_filter( array_map( 'absint', $saved ) ) ) );
		}
	}

	if ( isset( $_COOKIE['cet_wishlist_ids'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_COOKIE['cet_wishlist_ids'] ) );
		$ids = explode( ',', $raw );
		return array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
	}

	return array();
}

/**
 * Retrieve the current visitor's compare product IDs from cookie.
 *
 * @return array<int, int>
 */
function cet_get_compare_ids(): array {
	if ( isset( $_COOKIE['cet_compare_ids'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_COOKIE['cet_compare_ids'] ) );
		$ids = explode( ',', $raw );
		return array_slice( array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) ), 0, 4 );
	}
	return array();
}

/**
 * Retrieve recently viewed product IDs from WooCommerce / theme cookie.
 *
 * @return array<int, int>
 */
function cet_get_recently_viewed_ids(): array {
	if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
		return array();
	}
	$raw = sanitize_text_field( wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) );
	$ids = explode( '|', $raw );
	return array_reverse( array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) ) );
}

/**
 * Track recently viewed products on single product pages.
 *
 * @return void
 */
function cet_track_recently_viewed_product(): void {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	global $post;
	if ( ! $post || empty( $post->ID ) ) {
		return;
	}

	$viewed_products = array();
	if ( ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
		$raw             = sanitize_text_field( wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) );
		$viewed_products = array_filter( array_map( 'absint', explode( '|', $raw ) ) );
	}

	$current_id = absint( $post->ID );
	$key        = array_search( $current_id, $viewed_products, true );
	if ( false !== $key ) {
		unset( $viewed_products[ $key ] );
	}

	$viewed_products[] = $current_id;

	if ( count( $viewed_products ) > 12 ) {
		array_shift( $viewed_products );
	}

	if ( function_exists( 'wc_setcookie' ) ) {
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
	}
}
add_action( 'template_redirect', 'cet_track_recently_viewed_product', 20 );

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
					'title'      => wp_strip_all_tags( get_the_title( $post_id ) ),
					'url'        => esc_url( get_permalink( $post_id ) ),
					'price_html' => esc_html( get_the_date( '', $post_id ) ),
					'sku'        => '',
					'image'      => esc_url( (string) get_the_post_thumbnail_url( $post_id, 'thumbnail' ) ),
					'in_stock'   => true,
				);
			}
		}
		wp_reset_postdata();
	}

	wp_send_json_success(
		array(
			'results'  => $results,
			'view_all' => esc_url( add_query_arg( array( 's' => $search_term, 'post_type' => $post_type ), home_url( '/' ) ) ),
		)
	);
}
add_action( 'wp_ajax_cet_live_search', 'cet_ajax_live_search' );
add_action( 'wp_ajax_nopriv_cet_live_search', 'cet_ajax_live_search' );

/**
 * AJAX Handler: Product Quick View Modal Content.
 *
 * @return void
 */
function cet_ajax_quick_view(): void {
	check_ajax_referer( 'cet_public_nonce', 'nonce' );

	if ( ! function_exists( 'wc_get_product' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'WooCommerce is not active.', 'custom-ecommerce-theme' ) ) );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = wc_get_product( $product_id );

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
			<?php if ( $product->is_on_sale() ) : ?>
				<span class="cet-badge cet-badge--sale"><?php esc_html_e( 'Sale', 'custom-ecommerce-theme' ); ?></span>
			<?php endif; ?>
			<img src="<?php echo esc_url( (string) $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="cet-quickview__img">
		</div>
		<div class="cet-quickview__details">
			<?php if ( $cats ) : ?>
				<div class="cet-quickview__cats"><?php echo wp_kses_post( $cats ); ?></div>
			<?php endif; ?>
			<h2 class="cet-quickview__title">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
			</h2>
			<div class="cet-quickview__meta">
				<?php if ( $product->get_rating_count() > 0 ) : ?>
					<div class="cet-quickview__rating">
						<?php echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $product->get_sku() ) : ?>
					<span class="cet-quickview__sku">
						<?php esc_html_e( 'SKU:', 'custom-ecommerce-theme' ); ?> <strong><?php echo esc_html( $product->get_sku() ); ?></strong>
					</span>
				<?php endif; ?>
				<span class="cet-badge <?php echo $product->is_in_stock() ? 'cet-badge--instock' : 'cet-badge--outofstock'; ?>">
					<?php echo $product->is_in_stock() ? esc_html__( 'In Stock', 'custom-ecommerce-theme' ) : esc_html__( 'Out of Stock', 'custom-ecommerce-theme' ); ?>
				</span>
			</div>
			<div class="cet-quickview__price">
				<?php echo wp_kses_post( $product->get_price_html() ); ?>
			</div>
			<div class="cet-quickview__desc">
				<?php echo wp_kses_post( wpautop( $product->get_short_description() ?: wp_trim_words( $product->get_description(), 28 ) ) ); ?>
			</div>
			<div class="cet-quickview__actions">
				<?php if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>
					<form class="cart cet-quickview__cart-form" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post" enctype="multipart/form-data">
						<div class="cet-qty-stepper">
							<button type="button" class="cet-qty-btn cet-qty-minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'custom-ecommerce-theme' ); ?>">-</button>
							<input type="number" name="quantity" value="1" min="1" max="<?php echo esc_attr( (string) ( $product->get_max_purchase_quantity() > 0 ? $product->get_max_purchase_quantity() : 99 ) ); ?>" class="input-text qty text">
							<button type="button" class="cet-qty-btn cet-qty-plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'custom-ecommerce-theme' ); ?>">+</button>
						</div>
						<button type="submit" name="add-to-cart" value="<?php echo esc_attr( (string) $product->get_id() ); ?>" class="cet-btn cet-btn--gold single_add_to_cart_button button alt">
							<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
						</button>
					</form>
				<?php else : ?>
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cet-btn cet-btn--gold">
						<?php echo esc_html( $product->add_to_cart_text() ); ?>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="cet-btn cet-btn--outline">
					<?php esc_html_e( 'View Full Details', 'custom-ecommerce-theme' ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
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

	wp_send_json_success(
		array(
			'added'    => $added,
			'count'    => count( $wishlist ),
			'ids'      => $wishlist,
			'message'  => $added
				? esc_html__( 'Added to your wishlist.', 'custom-ecommerce-theme' )
				: esc_html__( 'Removed from your wishlist.', 'custom-ecommerce-theme' ),
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

	if ( ! function_exists( 'wc_get_product' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'WooCommerce is required for product comparison.', 'custom-ecommerce-theme' ) ) );
	}

	$raw_ids = isset( $_POST['product_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['product_ids'] ) ) : '';
	$ids     = array_slice( array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) ), 0, 4 );

	if ( empty( $ids ) ) {
		wp_send_json_success(
			array(
				'html' => '<div class="cet-empty-state"><p>' . esc_html__( 'No products selected for comparison yet.', 'custom-ecommerce-theme' ) . '</p></div>',
			)
		);
	}

	$products = array();
	foreach ( $ids as $id ) {
		$prod = wc_get_product( $id );
		if ( $prod && $prod->is_visible() ) {
			$products[] = $prod;
		}
	}

	ob_start();
	?>
	<div class="cet-compare-table-wrap">
		<table class="cet-compare-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Product', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $products as $prod ) : ?>
						<td>
							<div class="cet-compare-prod-header">
								<button type="button" class="cet-compare-remove-btn" data-product-id="<?php echo esc_attr( (string) $prod->get_id() ); ?>" aria-label="<?php esc_attr_e( 'Remove from comparison', 'custom-ecommerce-theme' ); ?>">&times;</button>
								<a href="<?php echo esc_url( $prod->get_permalink() ); ?>">
									<?php echo wp_kses_post( $prod->get_image( 'woocommerce_thumbnail' ) ); ?>
									<h4><?php echo esc_html( $prod->get_name() ); ?></h4>
								</a>
							</div>
						</td>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Price', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $products as $prod ) : ?>
						<td class="cet-compare-price"><?php echo wp_kses_post( $prod->get_price_html() ); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Rating', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $products as $prod ) : ?>
						<td>
							<?php
							echo $prod->get_rating_count() > 0
								? wp_kses_post( wc_get_rating_html( $prod->get_average_rating() ) )
								: esc_html__( 'No ratings yet', 'custom-ecommerce-theme' );
							?>
						</td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Availability', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $products as $prod ) : ?>
						<td>
							<span class="cet-badge <?php echo $prod->is_in_stock() ? 'cet-badge--instock' : 'cet-badge--outofstock'; ?>">
								<?php echo $prod->is_in_stock() ? esc_html__( 'In Stock', 'custom-ecommerce-theme' ) : esc_html__( 'Out of Stock', 'custom-ecommerce-theme' ); ?>
							</span>
						</td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'SKU', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $products as $prod ) : ?>
						<td><?php echo esc_html( $prod->get_sku() ?: '—' ); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Summary', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $products as $prod ) : ?>
						<td><?php echo wp_kses_post( wp_trim_words( $prod->get_short_description() ?: $prod->get_description(), 20 ) ); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Action', 'custom-ecommerce-theme' ); ?></th>
					<?php foreach ( $products as $prod ) : ?>
						<td>
							<a href="<?php echo esc_url( $prod->add_to_cart_url() ); ?>" class="cet-btn cet-btn--gold cet-btn--sm">
								<?php echo esc_html( $prod->add_to_cart_text() ); ?>
							</a>
						</td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
	$html = ob_get_clean();
	wp_send_json_success( array( 'html' => $html ) );
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

