=== Custom E-Commerce Theme ===
Contributors: aaqibmustafa
Author URI: https://aaqibmustafa.github.io/
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 8.2
WC requires at least: 8.0
WC tested up to: 9.5
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: e-commerce, custom-background, custom-colors, custom-header, custom-logo, custom-menu, featured-images, flexible-header, footer-widgets, full-width-template, grid-layout, one-column, two-columns, left-sidebar, right-sidebar, rtl-language-support, sticky-post, theme-options, threaded-comments, translation-ready, wide-blocks

A custom, luxury-grade WooCommerce WordPress hybrid classic theme engineered by Aaqib Mustafa.

== Description ==

Custom E-Commerce Theme is a commercial-grade, responsive WooCommerce WordPress theme crafted around the Midnight Navy (#112033), Champagne Gold (#C99B4A / #D8AE60), and Cobalt Royal (#2E63E6) visual identity with dual Haute Light Mode and Obsidian Dark Mode.

Key Features:
* Hybrid Classic WordPress Architecture with full `theme.json` and Gutenberg Block Pattern support.
* Dual Haute Light & Obsidian Dark Mode: Instant zero-flicker client color scheme toggle (Sun/Moon icons) with localStorage persistence, cross-tab synchronization, and Customizer default mode controls.
* Complete Homepage with 23 customizable sections (Announcement Bar, Luxury Header, Mega Menu, Live AJAX Product Search, Hero Showcase, Trust Strip, Featured Categories, Trending / Best-Selling / New Arrivals Tabs, Featured Collections, Live Offer Countdown, Brand Showcase, Recommended & Recently Viewed Products, Verified Testimonials, Journal/Blog, Newsletter & Social Gallery).
* Full WooCommerce Shop Archive with Grid/List switcher, column controls, and category/price filtering.
* Advanced Single Product layout with image gallery zoom/lightbox/slider, interactive variation swatch pills, quantity stepper, Instant "Buy Now" button, Wishlist, Compare, and Sticky Add-to-Cart bar.
* Two-Column Cart & Checkout with Free Shipping Goal Progress Bar and Off-Canvas AJAX Mini Cart Drawer.
* Luxury My Account Customer Portal.
* Companion Child Theme (`custom-ecommerce-child`) included.

== Installation ==

1. In your WordPress Admin Dashboard, navigate to **Appearance > Themes > Add New > Upload Theme**.
2. Upload `custom-ecommerce-theme.zip` and click **Install Now**.
3. Optional (Recommended for Customizations): Also upload `custom-ecommerce-child.zip` and click **Activate** on **Custom E-Commerce Child** (or activate **Custom E-Commerce Theme** directly).
4. Navigate to **Appearance > Customize > Custom E-Commerce Theme Options** to configure brand colors, header, homepage sections, shop layout, and footer details.

== Shortcodes ==

* `[cet_shop]` — Displays the filterable Shop Catalog archive with category pills, live sorting, and catalog items.
* `[cet_cart]` — Displays the interactive Shopping Bag page with quantity steppers, delivery progress goal, and totals.
* `[cet_checkout]` — Displays the encrypted Delivery & Payment Checkout portal.
* `[cet_wishlist]` — Displays the visitor's saved Wishlist products grid on any standard WordPress page.
* `[cet_account]` — Displays the branded Client Account Portal (Sign In, Create Account, and Order History).
* `[cet_track_order]` — Displays the order tracking lookup portal and recent storefront order status table.

== Changelog ==

= 1.3.0 (2026-11-13) =
* Haute Light & Obsidian Dark Mode System: Introduced a dual-palette luxury color engine based on November 2026 aesthetics. Default Light Mode delivers a radiant Cloud Porcelain (#F8F9FC) and crisp boutique white experience with deep Midnight Navy (#112033) headings and warm Champagne Gold accents. Obsidian Dark Mode delivers a velvet midnight experience (#09101A / #101B2B) with radiant gilded highlights (#D8AE60).
* Instant Zero-Flicker Mode Switcher: Added header Sun/Moon interactive toggle and a dedicated mobile drawer appearance switcher with localStorage persistence, cross-tab synchronization, and CSS smooth transitioning.
* WordPress Customizer Controls: Added Store Appearance selector (`default_color_mode`) offering Light, Dark, or System Automatic modes.
* Preserved 100% of existing functionality across WooCommerce catalog, cart drawer, wishlist, checkout modal, account portal, and child theme cascade overrides.
* Bumped parent theme (`custom-ecommerce-theme`) and child theme (`custom-ecommerce-child`) to version `1.3.0`.

= 1.2.0 (2026-11-13) =
* Mobile Header Overlap Fix: Resolved brand title and actions collision in responsive viewports (<= 768px and <= 480px). Brand title now truncates smoothly in an isolated flex cell with zero overlap on actions, and account trigger gracefully nests in the mobile navigation drawer.
* Dynamic wp-admin Products & Categories Integration:
  - Homepage Product Tabs (Trending, Best Sellers, New Arrivals) prioritize native WooCommerce products, with graceful sales/rating fallbacks so new products are never blank.
  - Featured Collections dynamically queries published WooCommerce products before falling back to showcase items.
  - Featured Categories queries live WooCommerce `product_cat` terms with automatic product image fallback if no category thumbnail is configured.
  - Mega Menu and Shop Catalog filter pills dynamically query active `product_cat` taxonomy terms.
  - Shop Catalog shortcode `[cet_shop]` features full category pills, search bar, and sorting for live WooCommerce products.
  - Quick View and Cart Drawer prioritize live WooCommerce product data (images, prices, SKUs, and permalinks).
* Bumped parent theme (`custom-ecommerce-theme`) and child theme (`custom-ecommerce-child`) to version `1.2.0`.

= 1.1.0 (2026-11-11) =
* Added automatic storefront page provisioning & dedicated Page Templates for Shop Catalog (`[cet_shop]`), Shopping Bag (`[cet_cart]`), Checkout & Delivery (`[cet_checkout]`), Saved Wishlist (`[cet_wishlist]`), Client Account (`[cet_account]`), and Track Order (`[cet_track_order]`).
* Surfaced direct storefront links right on the Homepage (Hero Quick Store Bar, category cards, collection cards, countdown offer CTA), Header navigation, Mega Menu, Mobile Drawer, and Footer.
* Rebuilt responsive mobile styling (`320px`–`1024px`) for sticky admin bar header offsets, uncrowded header controls, touch-visible quick actions, segmented collection tabs, 4-column mobile countdown timer, single-column checkout/registration forms, scrollable order tables, and full-width mobile cart/checkout layouts.
* Bumped parent theme (`custom-ecommerce-theme`) and companion child theme (`custom-ecommerce-child`) to version `1.1.0`.

= 1.0.0 (2026-11-11) =
* Complete commercial release (Phases 1–14): Parent & Child themes, Midnight Navy & Champagne Gold design system, Header & Mega Menu, 23-element Homepage, WooCommerce Shop, Single Product, Cart/Mini-Cart, Checkout, My Account, Live AJAX Search, Quick View, Wishlist, Product Comparison, Blog templates, Gutenberg patterns, RTL & Responsive support.
