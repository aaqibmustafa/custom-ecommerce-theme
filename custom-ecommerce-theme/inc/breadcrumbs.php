<?php
/**
 * Accessible Schema.org Breadcrumb Navigation Engine
 *
 * @package Custom_Ecommerce_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cet_breadcrumbs' ) ) {
	/**
	 * Render semantic, Schema.org-compliant breadcrumbs for WordPress & WooCommerce.
	 *
	 * @return void
	 */
	function cet_breadcrumbs(): void {
		if ( is_front_page() ) {
			return;
		}

		// Use native WooCommerce breadcrumbs when on a WooCommerce view and function exists.
		if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) && function_exists( 'woocommerce_breadcrumb' ) ) {
			echo '<nav class="cet-breadcrumbs-bar" aria-label="' . esc_attr__( 'Breadcrumb', 'custom-ecommerce-theme' ) . '">';
			echo '<div class="cet-container">';
			woocommerce_breadcrumb(
				array(
					'delimiter'   => '<span class="cet-breadcrumb__sep" aria-hidden="true">/</span>',
					'wrap_before' => '<ol class="cet-breadcrumbs">',
					'wrap_after'  => '</ol>',
					'before'      => '<li class="cet-breadcrumb__item">',
					'after'       => '</li>',
					'home'        => esc_html__( 'Home', 'custom-ecommerce-theme' ),
				)
			);
			echo '</div>';
			echo '</nav>';
			return;
		}

		$items = array();

		$items[] = array(
			'label' => esc_html__( 'Home', 'custom-ecommerce-theme' ),
			'url'   => home_url( '/' ),
		);

		if ( is_home() ) {
			$blog_page_id = (int) get_option( 'page_for_posts' );
			$items[]      = array(
				'label' => $blog_page_id ? get_the_title( $blog_page_id ) : esc_html__( 'Journal', 'custom-ecommerce-theme' ),
				'url'   => '',
			);
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$items[] = array(
				'label' => single_term_title( '', false ),
				'url'   => '',
			);
		} elseif ( is_singular( 'post' ) ) {
			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				$primary_cat = $categories[0];
				$items[]     = array(
					'label' => $primary_cat->name,
					'url'   => get_category_link( $primary_cat->term_id ),
				);
			}
			$items[] = array(
				'label' => get_the_title(),
				'url'   => '',
			);
		} elseif ( is_page() ) {
			global $post;
			if ( $post && $post->post_parent ) {
				$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
				foreach ( $ancestors as $ancestor_id ) {
					$items[] = array(
						'label' => get_the_title( $ancestor_id ),
						'url'   => get_permalink( $ancestor_id ),
					);
				}
			}
			$items[] = array(
				'label' => get_the_title(),
				'url'   => '',
			);
		} elseif ( is_search() ) {
			$items[] = array(
				/* translators: %s: Search query. */
				'label' => sprintf( esc_html__( 'Search Results for "%s"', 'custom-ecommerce-theme' ), get_search_query() ),
				'url'   => '',
			);
		} elseif ( is_404() ) {
			$items[] = array(
				'label' => esc_html__( 'Page Not Found (404)', 'custom-ecommerce-theme' ),
				'url'   => '',
			);
		} elseif ( is_archive() ) {
			$items[] = array(
				'label' => wp_strip_all_tags( get_the_archive_title() ),
				'url'   => '',
			);
		}

		?>
		<nav class="cet-breadcrumbs-bar" aria-label="<?php esc_attr_e( 'Breadcrumb', 'custom-ecommerce-theme' ); ?>">
			<div class="cet-container">
				<ol class="cet-breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">
					<?php foreach ( $items as $index => $item ) : ?>
						<?php $position = $index + 1; ?>
						<li class="cet-breadcrumb__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
							<?php if ( ! empty( $item['url'] ) && $position < count( $items ) ) : ?>
								<a href="<?php echo esc_url( $item['url'] ); ?>" itemprop="item">
									<span itemprop="name"><?php echo esc_html( $item['label'] ); ?></span>
								</a>
								<span class="cet-breadcrumb__sep" aria-hidden="true">/</span>
							<?php else : ?>
								<span class="cet-breadcrumb__current" itemprop="name" aria-current="page">
									<?php echo esc_html( $item['label'] ); ?>
								</span>
							<?php endif; ?>
							<meta itemprop="position" content="<?php echo esc_attr( (string) $position ); ?>">
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</nav>
		<?php
	}
}

