=== Custom E-Commerce Theme ===
Contributors: aaqibmustafa
Author URI: https://aaqibmustafa.github.io/
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 8.2
WC requires at least: 8.0
WC tested up to: 9.5
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: e-commerce, custom-background, custom-colors, custom-header, custom-logo, custom-menu, featured-images, flexible-header, footer-widgets, full-width-template, grid-layout, one-column, two-columns, left-sidebar, right-sidebar, rtl-language-support, sticky-post, theme-options, threaded-comments, translation-ready, wide-blocks

A custom, luxury-grade WooCommerce WordPress hybrid classic theme engineered by Aaqib Mustafa.

== Description ==

Custom E-Commerce Theme is a commercial-grade, responsive WooCommerce WordPress theme crafted around the Midnight Navy (#14243A), Champagne Gold (#D6AD65), and Electric Blue (#356AE6) visual identity.

Key Features:
* Hybrid Classic WordPress Architecture with full `theme.json` and Gutenberg Block Pattern support.
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

= 1.1.0 (2026-11-11) =
* Added automatic storefront page provisioning & dedicated Page Templates for Shop Catalog (`[cet_shop]`), Shopping Bag (`[cet_cart]`), Checkout & Delivery (`[cet_checkout]`), Saved Wishlist (`[cet_wishlist]`), Client Account (`[cet_account]`), and Track Order (`[cet_track_order]`).
* Surfaced direct storefront links right on the Homepage (Hero Quick Store Bar, category cards, collection cards, countdown offer CTA), Header navigation, Mega Menu, Mobile Drawer, and Footer.
* Rebuilt responsive mobile styling (`320px`–`1024px`) for sticky admin bar header offsets, uncrowded header controls, touch-visible quick actions, segmented collection tabs, 4-column mobile countdown timer, single-column checkout/registration forms, scrollable order tables, and full-width mobile cart/checkout layouts.
* Bumped parent theme (`custom-ecommerce-theme`) and companion child theme (`custom-ecommerce-child`) to version `1.1.0`.

= 1.0.0 (2026-11-11) =
* Complete commercial release (Phases 1–14): Parent & Child themes, Midnight Navy & Champagne Gold design system, Header & Mega Menu, 23-element Homepage, WooCommerce Shop, Single Product, Cart/Mini-Cart, Checkout, My Account, Live AJAX Search, Quick View, Wishlist, Product Comparison, Blog templates, Gutenberg patterns, RTL & Responsive support.
