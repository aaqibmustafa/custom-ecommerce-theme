<?php
/**
 * Luxury Curated Showcase Catalog & Card Renderer
 *
 * Provides rich visual product cards and department categories immediately upon theme activation
 * when a fresh WordPress / WooCommerce store has not yet added products to the database,
 * while seamlessly switching to live WooCommerce database queries once products exist.
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cet_get_showcase_categories' ) ) {
	/**
	 * Return 6 curated luxury department categories for initial storefront display.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function cet_get_showcase_categories(): array {
		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/#signature-products' );

		return array(
			array(
				'name'    => __( 'Haute Horology', 'custom-ecommerce-theme' ),
				'count'   => 18,
				'initial' => '⌚',
				'url'     => $shop_url,
			),
			array(
				'name'    => __( 'Fine Leather Goods', 'custom-ecommerce-theme' ),
				'count'   => 24,
				'initial' => '👜',
				'url'     => $shop_url,
			),
			array(
				'name'    => __( 'Champagne Gold Editions', 'custom-ecommerce-theme' ),
				'count'   => 12,
				'initial' => '✦',
				'url'     => $shop_url,
			),
			array(
				'name'    => __( 'Executive Travel', 'custom-ecommerce-theme' ),
				'count'   => 15,
				'initial' => '🧳',
				'url'     => $shop_url,
			),
			array(
				'name'    => __( 'Bespoke Eyewear', 'custom-ecommerce-theme' ),
				'count'   => 10,
				'initial' => '🕶',
				'url'     => $shop_url,
			),
			array(
				'name'    => __( 'Private Fragrances', 'custom-ecommerce-theme' ),
				'count'   => 9,
				'initial' => '❖',
				'url'     => $shop_url,
			),
		);
	}
}

if ( ! function_exists( 'cet_get_showcase_products' ) ) {
	/**
	 * Return 8 curated luxury showcase products with bespoke SVG artwork.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function cet_get_showcase_products(): array {
		return array(
			9001 => array(
				'id'            => 9001,
				'title'         => __( 'Sovereign Chronograph 41mm — Champagne Gold', 'custom-ecommerce-theme' ),
				'category'      => __( 'Haute Horology', 'custom-ecommerce-theme' ),
				'price_html'    => '<del>$1,450.00</del> <ins>$1,190.00</ins>',
				'badge'         => '-18%',
				'badge_type'    => 'sale',
				'badge_extra'   => __( 'Best Seller', 'custom-ecommerce-theme' ),
				'rating'        => 5,
				'reviews'       => 42,
				'sku'           => 'CET-CHR-9001',
				'tab'           => array( 'trending', 'bestsellers', 'featured' ),
				'description'   => __( 'Swiss-calibrated automatic chronograph featuring a sunray Midnight Navy dial, 18k Champagne Gold bezel indices, and sapphire crystal caseback.', 'custom-ecommerce-theme' ),
				'svg_type'      => 'watch',
			),
			9002 => array(
				'id'            => 9002,
				'title'         => __( 'Madison Quilted Calfskin Weekender Tote', 'custom-ecommerce-theme' ),
				'category'      => __( 'Fine Leather Goods', 'custom-ecommerce-theme' ),
				'price_html'    => '<ins>$890.00</ins>',
				'badge'         => __( 'Featured', 'custom-ecommerce-theme' ),
				'badge_type'    => 'featured',
				'badge_extra'   => '',
				'rating'        => 5,
				'reviews'       => 29,
				'sku'           => 'CET-BAG-9002',
				'tab'           => array( 'trending', 'bestsellers', 'featured' ),
				'description'   => __( 'Hand-stitched full-grain Italian calfskin leather finished with brushed Champagne Gold clasps and suede-lined laptop compartment.', 'custom-ecommerce-theme' ),
				'svg_type'      => 'bag',
			),
			9003 => array(
				'id'            => 9003,
				'title'         => __( 'Lumière Titanium Aviator Sunglasses', 'custom-ecommerce-theme' ),
				'category'      => __( 'Bespoke Eyewear', 'custom-ecommerce-theme' ),
				'price_html'    => '<del>$520.00</del> <ins>$430.00</ins>',
				'badge'         => '-17%',
				'badge_type'    => 'sale',
				'badge_extra'   => '',
				'rating'        => 5,
				'reviews'       => 19,
				'sku'           => 'CET-EYE-9003',
				'tab'           => array( 'trending', 'new' ),
				'description'   => __( 'Featherweight Japanese beta-titanium frames plated in Champagne Gold with polarized anti-reflective Midnight Navy lenses.', 'custom-ecommerce-theme' ),
				'svg_type'      => 'eyewear',
			),
			9004 => array(
				'id'            => 9004,
				'title'         => __( 'Velvet Oud & Ambergris Extrait de Parfum 100ml', 'custom-ecommerce-theme' ),
				'category'      => __( 'Private Fragrances', 'custom-ecommerce-theme' ),
				'price_html'    => '<ins>$340.00</ins>',
				'badge'         => __( 'Best Seller', 'custom-ecommerce-theme' ),
				'badge_type'    => 'bestseller',
				'badge_extra'   => '',
				'rating'        => 5,
				'reviews'       => 64,
				'sku'           => 'CET-PRF-9004',
				'tab'           => array( 'trending', 'bestsellers', 'featured' ),
				'description'   => __( 'An opulent olfactory signature blending rare aged oud, Calabrian bergamot, warm ambergris, and smoked Madagascar vanilla.', 'custom-ecommerce-theme' ),
				'svg_type'      => 'parfum',
			),
			9005 => array(
				'id'            => 9005,
				'title'         => __( 'Aurum Guilloché Cufflinks & Tie Bar Set', 'custom-ecommerce-theme' ),
				'category'      => __( 'Champagne Gold Editions', 'custom-ecommerce-theme' ),
				'price_html'    => '<ins>$275.00</ins>',
				'badge'         => __( 'New Arrival', 'custom-ecommerce-theme' ),
				'badge_type'    => 'new',
				'badge_extra'   => '',
				'rating'        => 5,
				'reviews'       => 15,
				'sku'           => 'CET-ACC-9005',
				'tab'           => array( 'trending', 'new', 'featured' ),
				'description'   => __( 'Engine-turned geometric guilloché cufflinks with Midnight Navy enamel inlay and matching weighted tie bar.', 'custom-ecommerce-theme' ),
				'svg_type'      => 'gem',
			),
			9006 => array(
				'id'            => 9006,
				'title'         => __( 'Geneva Diplomat Hardside Cabin Trolley', 'custom-ecommerce-theme' ),
				'category'      => __( 'Executive Travel', 'custom-ecommerce-theme' ),
				'price_html'    => '<del>$1,150.00</del> <ins>$960.00</ins>',
				'badge'         => '-16%',
				'badge_type'    => 'sale',
				'badge_extra'   => '',
				'rating'        => 5,
				'reviews'       => 31,
				'sku'           => 'CET-TRV-9006',
				'tab'           => array( 'trending', 'bestsellers', 'new' ),
				'description'   => __( 'Anodized Midnight Navy aluminum shell reinforced with Champagne Gold corner guards and silent 360-degree dual spinner wheels.', 'custom-ecommerce-theme' ),
				'svg_type'      => 'case',
			),
			9007 => array(
				'id'            => 9007,
				'title'         => __( 'Valentin Double-Faced Cashmere Overcoat', 'custom-ecommerce-theme' ),
				'category'      => __( 'Champagne Gold Editions', 'custom-ecommerce-theme' ),
				'price_html'    => '<ins>$1,680.00</ins>',
				'badge'         => __( 'Limited', 'custom-ecommerce-theme' ),
				'badge_type'    => 'featured',
				'badge_extra'   => '',
				'rating'        => 5,
				'reviews'       => 22,
				'sku'           => 'CET-APP-9007',
				'tab'           => array( 'trending', 'bestsellers', 'new' ),
				'description'   => __( 'Tailored in pure Mongolian cashmere with horn buttons encircled by Champagne Gold rings and a silk jacquard lining.', 'custom-ecommerce-theme' ),
				'svg_type'      => 'coat',
			),
			9008 => array(
				'id'            => 9008,
				'title'         => __( 'Atelier Sovereign 18k Nib Fountain Pen', 'custom-ecommerce-theme' ),
				'category'      => __( 'Executive Travel', 'custom-ecommerce-theme' ),
				'price_html'    => '<ins>$495.00</ins>',
				'badge'         => __( 'New Arrival', 'custom-ecommerce-theme' ),
				'badge_type'    => 'new',
				'badge_extra'   => '',
				'rating'        => 5,
				'reviews'       => 18,
				'sku'           => 'CET-PEN-9008',
				'tab'           => array( 'trending', 'new' ),
				'description'   => __( 'Balanced lacquer barrel in deep Midnight Navy crowned with a handcrafted 18k Champagne Gold nib for effortless precision.', 'custom-ecommerce-theme' ),
				'svg_type'      => 'pen',
			),
		);
	}
}

if ( ! function_exists( 'cet_get_showcase_product_svg' ) ) {
	/**
	 * Render bespoke luxury SVG product artwork in Midnight Navy & Champagne Gold.
	 *
	 * @param string $type Artwork type identifier.
	 * @param string $sku  SKU code to display on the luxury plate.
	 * @return string
	 */
	function cet_get_showcase_product_svg( string $type, string $sku = '' ): string {
		$graphic = '';
		switch ( $type ) {
			case 'watch':
				$graphic = '<circle cx="200" cy="195" r="78" fill="#0C1726" stroke="#D6AD65" stroke-width="5"/><circle cx="200" cy="195" r="66" fill="none" stroke="#D6AD65" stroke-width="1.5" stroke-dasharray="4 6"/><rect x="176" y="75" width="48" height="42" rx="6" fill="none" stroke="#D6AD65" stroke-width="3"/><rect x="176" y="273" width="48" height="42" rx="6" fill="none" stroke="#D6AD65" stroke-width="3"/><line x1="200" y1="195" x2="200" y2="148" stroke="#D6AD65" stroke-width="4" stroke-linecap="round"/><line x1="200" y1="195" x2="236" y2="195" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/><circle cx="200" cy="195" r="6" fill="#D6AD65"/>';
				break;
			case 'bag':
				$graphic = '<rect x="115" y="150" width="170" height="135" rx="14" fill="#0C1726" stroke="#D6AD65" stroke-width="4"/><path d="M155 150 V122 A45 45 0 0 1 245 122 V150" fill="none" stroke="#D6AD65" stroke-width="4"/><line x1="115" y1="195" x2="285" y2="195" stroke="#D6AD65" stroke-width="2"/><rect x="184" y="182" width="32" height="26" rx="4" fill="#D6AD65"/>';
				break;
			case 'eyewear':
				$graphic = '<rect x="95" y="165" width="90" height="62" rx="22" fill="#0C1726" stroke="#D6AD65" stroke-width="4"/><rect x="215" y="165" width="90" height="62" rx="22" fill="#0C1726" stroke="#D6AD65" stroke-width="4"/><path d="M185 182 Q200 168 215 182" fill="none" stroke="#D6AD65" stroke-width="4"/><line x1="95" y1="175" x2="185" y2="175" stroke="#D6AD65" stroke-width="2" opacity="0.6"/>';
				break;
			case 'parfum':
				$graphic = '<rect x="140" y="155" width="120" height="135" rx="12" fill="#0C1726" stroke="#D6AD65" stroke-width="4"/><rect x="175" y="122" width="50" height="33" rx="4" fill="#D6AD65"/><rect x="160" y="190" width="80" height="65" rx="4" fill="none" stroke="#D6AD65" stroke-width="2"/><circle cx="200" cy="222" r="14" fill="#D6AD65" opacity="0.85"/>';
				break;
			case 'gem':
				$graphic = '<polygon points="200,115 275,180 200,285 125,180" fill="#0C1726" stroke="#D6AD65" stroke-width="4"/><polygon points="200,138 250,180 200,260 150,180" fill="none" stroke="#D6AD65" stroke-width="2"/><circle cx="200" cy="185" r="10" fill="#D6AD65"/>';
				break;
			default:
				$graphic = '<rect x="130" y="125" width="140" height="160" rx="14" fill="#0C1726" stroke="#D6AD65" stroke-width="4"/><path d="M170 125 V105 H230 V125" fill="none" stroke="#D6AD65" stroke-width="4"/><line x1="160" y1="165" x2="240" y2="165" stroke="#D6AD65" stroke-width="2"/><circle cx="200" cy="215" r="22" fill="none" stroke="#D6AD65" stroke-width="3"/>';
				break;
		}

		return sprintf(
			'<svg class="cet-product-card__svg-art" viewBox="0 0 400 380" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="%1$s">
				<defs>
					<radialGradient id="cetGlow-%2$s" cx="50%%" cy="48%%" r="55%%">
						<stop offset="0%%" stop-color="#243E63"/>
						<stop offset="100%%" stop-color="#101D30"/>
					</radialGradient>
				</defs>
				<rect width="400" height="380" fill="url(#cetGlow-%2$s)"/>
				<rect x="22" y="22" width="356" height="336" rx="12" fill="none" stroke="#D6AD65" stroke-opacity="0.28" stroke-width="1.5"/>
				%3$s
				<text x="200" y="336" text-anchor="middle" fill="#D6AD65" font-family="Georgia, serif" font-size="11" letter-spacing="3" opacity="0.9">%1$s</text>
			</svg>',
			esc_attr( $sku ?: 'ATELIER EDITION' ),
			esc_attr( sanitize_key( $type . '-' . $sku ) ),
			$graphic
		);
	}
}

if ( ! function_exists( 'cet_render_showcase_product_card' ) ) {
	/**
	 * Render a complete luxury product card from a showcase item array.
	 *
	 * @param array<string, mixed> $item Showcase product array.
	 * @return void
	 */
	function cet_render_showcase_product_card( array $item ): void {
		$product_id   = absint( $item['id'] ?? 9001 );
		$title        = (string) ( $item['title'] ?? '' );
		$category     = (string) ( $item['category'] ?? '' );
		$price_html   = (string) ( $item['price_html'] ?? '' );
		$badge        = (string) ( $item['badge'] ?? '' );
		$badge_type   = (string) ( $item['badge_type'] ?? 'featured' );
		$badge_extra  = (string) ( $item['badge_extra'] ?? '' );
		$reviews      = absint( $item['reviews'] ?? 24 );
		$sku          = (string) ( $item['sku'] ?? '' );
		$svg_type     = (string) ( $item['svg_type'] ?? 'watch' );
		$wishlist_ids = function_exists( 'cet_get_wishlist_ids' ) ? cet_get_wishlist_ids() : array();
		$in_wishlist  = in_array( $product_id, $wishlist_ids, true );
		?>
		<article class="product cet-product-card cet-product-card--showcase" data-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
			<div class="cet-product-card__media">
				<div class="cet-product-card__badges">
					<?php if ( $badge ) : ?>
						<span class="cet-badge cet-badge--<?php echo esc_attr( $badge_type ); ?>">
							<?php echo esc_html( $badge ); ?>
						</span>
					<?php endif; ?>
					<?php if ( $badge_extra ) : ?>
						<span class="cet-badge cet-badge--bestseller">
							<?php echo esc_html( $badge_extra ); ?>
						</span>
					<?php endif; ?>
				</div>

				<div class="cet-product-card__quick-actions">
					<button
						type="button"
						class="cet-icon-action-btn cet-wishlist-toggle <?php echo $in_wishlist ? 'is-active' : ''; ?>"
						data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
						aria-label="<?php esc_attr_e( 'Toggle Wishlist', 'custom-ecommerce-theme' ); ?>"
						title="<?php esc_attr_e( 'Wishlist', 'custom-ecommerce-theme' ); ?>"
					>
						<?php echo cet_get_svg_icon( 'heart', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>

					<button
						type="button"
						class="cet-icon-action-btn cet-quickview-trigger"
						data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
						aria-label="<?php esc_attr_e( 'Quick View Product', 'custom-ecommerce-theme' ); ?>"
						title="<?php esc_attr_e( 'Quick View', 'custom-ecommerce-theme' ); ?>"
					>
						<?php echo cet_get_svg_icon( 'eye', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>

					<button
						type="button"
						class="cet-icon-action-btn cet-compare-toggle"
						data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
						data-product-title="<?php echo esc_attr( $title ); ?>"
						aria-label="<?php esc_attr_e( 'Compare Product', 'custom-ecommerce-theme' ); ?>"
						title="<?php esc_attr_e( 'Compare', 'custom-ecommerce-theme' ); ?>"
					>
						<?php echo cet_get_svg_icon( 'compare', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>

				<a href="#signature-products" class="cet-product-card__img-link cet-quickview-trigger" data-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
					<?php echo cet_get_showcase_product_svg( $svg_type, $sku ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>

			<div class="cet-product-card__body">
				<?php if ( $category ) : ?>
					<div class="cet-product-card__category"><?php echo esc_html( $category ); ?></div>
				<?php endif; ?>

				<h3 class="cet-product-card__title">
					<a href="#signature-products" class="cet-quickview-trigger" data-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
						<?php echo esc_html( $title ); ?>
					</a>
				</h3>

				<div class="cet-product-card__rating" aria-label="<?php esc_attr_e( 'Rated 5.0 out of 5', 'custom-ecommerce-theme' ); ?>">
					<span class="cet-stars" aria-hidden="true">★★★★★</span>
					<span>(<?php echo esc_html( (string) $reviews ); ?>)</span>
				</div>

				<div class="cet-product-card__footer">
					<div class="cet-product-card__price">
						<?php echo wp_kses_post( $price_html ); ?>
					</div>

					<div class="cet-product-card__cart">
						<button type="button" class="cet-add-cart-btn cet-quickview-trigger" data-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
							<?php esc_html_e( 'Add to Bag', 'custom-ecommerce-theme' ); ?>
						</button>
					</div>
				</div>
			</div>
		</article>
		<?php
	}
}
