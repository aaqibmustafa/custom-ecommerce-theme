# Custom E-Commerce Theme — Complete Official Documentation

**Theme Name:** Custom E-Commerce Theme  
**Theme Slug:** `custom-ecommerce-theme`  
**Child Theme Slug:** `custom-ecommerce-child`  
**Author:** Aaqib Mustafa ([https://aaqibmustafa.github.io/](https://aaqibmustafa.github.io/))  
**Version:** 1.0.0  
**Requires WordPress:** 6.4+ (Tested up to 7.1)  
**Requires PHP:** 8.2+  
**Requires WooCommerce:** 8.0+ (HPOS & Cart/Checkout Blocks Compatible)

---

## 1. Brand Identity & Color System

Custom E-Commerce Theme is built around a centralized design token architecture defined in both `style.css` (`:root`) and `theme.json`:

| Token Role | Name | HEX Code | CSS Variable |
| :--- | :--- | :--- | :--- |
| **Primary Brand Color** | Midnight Navy | `#14243A` | `--color-primary` |
| **Signature Accent** | Champagne Gold | `#D6AD65` | `--color-accent` |
| **Secondary Interactive** | Electric Blue | `#356AE6` | `--color-secondary` |
| **Main Background** | Cloud White | `#F5F7FA` | `--color-background` |
| **Surface / Cards** | Pure White | `#FFFFFF` | `--color-surface` |
| **Primary Text** | Graphite | `#202938` | `--color-text` |
| **Border & Dividers** | Soft Slate | `#E8EDF5` | `--color-border` |

---

## 2. Installation Guide

### Method A: Upload via WordPress Dashboard (Production ZIPs)
1. Log in to your WordPress Admin Dashboard (`/wp-admin`).
2. Go to **Appearance > Themes > Add New > Upload Theme**.
3. Select `custom-ecommerce-theme.zip` and click **Install Now** (do not delete this parent theme).
4. Go back to **Appearance > Themes > Add New > Upload Theme**, select `custom-ecommerce-child.zip`, click **Install Now**, and click **Activate**.

### Method B: Local XAMPP Direct Installation / Junction
Copy or link `custom-ecommerce-theme` and `custom-ecommerce-child` into your WordPress `wp-content/themes/` directory:

```powershell
New-Item -ItemType Junction -Path "C:\xampp\htdocs\E_Commerce_Project\wordpress\wp-content\themes\custom-ecommerce-theme" -Target "C:\xampp\htdocs\custom_ecommerce_theme\custom-ecommerce-theme"
New-Item -ItemType Junction -Path "C:\xampp\htdocs\E_Commerce_Project\wordpress\wp-content\themes\custom-ecommerce-child" -Target "C:\xampp\htdocs\custom_ecommerce_theme\custom-ecommerce-child"
```

---

## 3. Theme & Homepage Setup Guide

1. **Set Static Front Page & Blog Page:**
   - Create two pages in **Pages > Add New**: `Home` and `Journal` (or `Blog`).
   - Go to **Settings > Reading**, choose **A static page**, set **Homepage** to `Home` and **Posts page** to `Journal`.
   - `front-page.php` will automatically render all 23 homepage features and sections.
2. **Configure Navigation Menus:**
   - Go to **Appearance > Menus** and assign menus to:
     - `Primary Navigation`
     - `Mobile Navigation Drawer`
     - `Top Announcement Bar Menu`
     - `Footer Column 1 (Shop)`, `Footer Column 2 (Company)`, `Footer Column 3 (Customer Care)`
3. **Create a Dedicated Wishlist Page (Optional):**
   - Create a page titled `Wishlist` and insert the shortcode `[cet_wishlist]`.

---

## 4. WooCommerce Configuration Guide

1. Install and activate **WooCommerce** under **Plugins > Add New**.
2. Verify that WooCommerce pages (`Shop`, `Cart`, `Checkout`, `My account`) are assigned under **WooCommerce > Settings > Advanced**.
3. Add product categories with thumbnails under **Products > Categories** — these automatically populate the **Header Mega Menu**, **Homepage Featured Categories**, and **Shop Sidebar Filter**.
4. Mark select products with the star icon in **Products > All Products** to showcase them in **Featured Collections** with the Champagne Gold `Featured` badge.

---

## 5. Theme Customizer Reference

Navigate to **Appearance > Customize > Custom E-Commerce Theme Options**:
1. **1. Brand Colors (Champagne Gold System):** Customize primary, accent, secondary, background, text, and border colors.
2. **2. Header, Mega Menu & Announcement Bar:** Toggle the top announcement bar, edit promo text, toggle sticky header, department mega menu, live product search, and wishlist icon.
3. **3. Homepage Sections & Promotions:** Toggle any homepage section on/off and customize hero headlines, CTAs, countdown target date (`YYYY-MM-DDTHH:MM:SS`), promo coupon badge, and Instagram handle.
4. **4. WooCommerce Shop & Product Settings:** Choose Left Sidebar, Right Sidebar, or Full-Width shop archive, default grid columns (`2–5`), Free Shipping progress bar goal amount (default `$150`), and product card elements.
5. **5. Footer, Contact & Social Links:** Configure atelier address, concierge phone/email, brand bio, and social URLs.

---

## 6. Parent & Child Theme Architecture

- **Parent Theme (`custom-ecommerce-theme/`):** Contains all core PHP modules (`inc/`), templates, WooCommerce overrides (`woocommerce/`), CSS (`assets/css/`), and JavaScript (`assets/js/`).
- **Child Theme (`custom-ecommerce-child/`):** References `Template: custom-ecommerce-theme` in `style.css` and enqueues child overrides after all parent CSS modules.
  - To override any template file (e.g., `header.php` or `template-parts/product/product-card.php`), copy it into the same relative path inside `custom-ecommerce-child/`.
  - To modify default Customizer values via PHP, use the `cet_default_theme_options` filter inside `custom-ecommerce-child/functions.php`.

