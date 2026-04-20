<!-- SEO Meta -->
<!--
  Title: Panth Disable Wishlist & Compare — Remove Wishlist and Compare from Magento 2 (Hyva + Luma)
  Description: Completely disable Magento 2 Wishlist and Compare across the entire frontend. Removes every button, link, sidebar, cart row action, customer-account tab, widget link, and JS handler. 404s /wishlist/* and /catalog/product_compare/* routes. Works identically on Hyva and Luma. Admin toggle, no theme edits.
  Keywords: magento 2 disable wishlist, magento disable compare, magento hide wishlist, magento hide compare, magento 2 remove wishlist button, magento 2 remove compare products, hyva disable wishlist, luma disable wishlist, magento 2 frontend clean
  Author: Kishan Savaliya (Panth Infotech)
-->

# Panth Disable Wishlist & Compare — Remove Wishlist and Compare from Magento 2 (Hyva + Luma)

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange?logo=magento&logoColor=white)](https://magento.com)
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue?logo=php&logoColor=white)](https://php.net)
[![License: Proprietary](https://img.shields.io/badge/License-Proprietary-red)]()
[![Packagist](https://img.shields.io/badge/Packagist-mage2kishan%2Fmodule--disable--wishlist--compare-orange?logo=packagist&logoColor=white)](https://packagist.org/packages/mage2kishan/module-disable-wishlist-compare)
[![Hyva Compatible](https://img.shields.io/badge/Hyva-Compatible-14b8a6?logo=alpinedotjs&logoColor=white)](https://hyva.io)
[![Luma Compatible](https://img.shields.io/badge/Luma-Compatible-orange)]()
[![Upwork Top Rated Plus](https://img.shields.io/badge/Upwork-Top%20Rated%20Plus-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)
[![Panth Infotech Agency](https://img.shields.io/badge/Agency-Panth%20Infotech-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/agencies/1881421506131960778/)
[![Website](https://img.shields.io/badge/Website-kishansavaliya.com-0D9488)](https://kishansavaliya.com)
[![Get a Quote](https://img.shields.io/badge/Get%20a%20Quote-Free%20Estimate-DC2626)](https://kishansavaliya.com/get-quote)

> **Completely disable Magento 2's Wishlist and Compare features** across the entire frontend — every button, link, sidebar, cart row action, customer-account tab, widget link, JS handler, and direct URL route. Works identically on **Hyva** and **Luma**. One module, admin toggles, no theme edits.

Many storefronts — single-SKU brands, B2B catalogs, industrial supply, jewellery, wholesale — never want Wishlist or Compare shown. Magento's built-in config flags only cover a handful of surfaces and leave behind broken links, empty sidebars, ghost JS handlers, and 302 redirects to a login page. **Panth Disable Wishlist & Compare** is a clean, comprehensive kill-switch.

---

## Preview

### Admin Configuration

![Admin Configuration](docs/admin-configuration.png)

*Stores → Configuration → Panth Infotech → Disable Wishlist & Compare — four toggles, all default to Yes, applied immediately after cache flush.*

---

## Need Custom Magento 2 Development?

<p align="center">
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/Get%20a%20Free%20Quote%20%E2%86%92-Reply%20within%2024%20hours-DC2626?style=for-the-badge" alt="Get a Free Quote" />
  </a>
</p>

<table>
<tr>
<td width="50%" align="center">

### Kishan Savaliya
**Top Rated Plus on Upwork**

[![Hire on Upwork](https://img.shields.io/badge/Hire%20on%20Upwork-Top%20Rated%20Plus-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)

</td>
<td width="50%" align="center">

### Panth Infotech Agency

[![Visit Agency](https://img.shields.io/badge/Visit%20Agency-Panth%20Infotech-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/agencies/1881421506131960778/)

</td>
</tr>
</table>

---

## Table of Contents

- [What This Disables](#what-this-disables)
- [How It Works](#how-it-works)
- [Compatibility](#compatibility)
- [Installation](#installation)
- [Admin Toggles](#admin-toggles)
- [Restoring the UI](#restoring-the-ui)
- [Troubleshooting](#troubleshooting)
- [Support](#support)

---

## What This Disables

### Wishlist

| Surface | Where | Mechanism |
|---|---|---|
| Header wishlist icon/link | Hyva + Luma | Layout remove + header template arg override |
| Wishlist sidebar block | Luma | Layout remove |
| Product detail page "Add to Wish List" | Hyva + Luma | Layout remove (view + related + upsell) |
| Category list add-to-wishlist buttons | Hyva + Luma | Layout remove (core + JS helper) |
| Cart row "Move to Wishlist" action | Hyva + Luma | Layout remove for all 7 product types |
| Customer-account side-nav tab | Hyva + Luma | Layout remove |
| Luma widget wishlist link (`new_grid`, `new_list`, `listing`) | Luma | Block plugin empties `AbstractProduct::getAddToWishlistUrl()` |
| Magento helper `Wishlist\Helper\Data::isAllow()` | All themes | DI plugin forces false |
| Hyva `ViewModel\Wishlist` display methods | Hyva | DI plugin forces false |
| `/wishlist/*` direct URL access | All themes | Predispatch observer → 404 *before* auth-redirect |

### Compare

| Surface | Where | Mechanism |
|---|---|---|
| Hyva header compare icon | Hyva | Layout remove + `show_compare=false` arg override |
| Core compare header link / sidebar | Luma | Layout remove |
| Product detail page "Add to Compare" | Hyva + Luma | Layout remove (view + related + upsell) |
| Category list add-to-compare buttons | Hyva + Luma | Layout remove (core + JS helper) |
| Luma widget compare link (`new_grid`, `new_list`, `listing`) | Luma | Block plugin empties `AbstractProduct::getAddToCompareUrl()` |
| Hyva `ViewModel\ProductCompare` display methods | Hyva | DI plugin forces false |
| `/catalog/product_compare/*` direct URL access | All themes | Controller plugin on `Compare::execute` → 404 |

---

## How It Works

The module combines **six complementary mechanisms** so every rendering path is covered no matter how a theme or widget decides to emit wishlist/compare markup.

1. **Layout XML removes** on `default`, `catalog_product_view`, `catalog_category_view`, `catalog_list_item`, `catalogsearch_result_index`, `catalogsearch_advanced_result`, and `checkout_cart_index` — removes every named wishlist/compare block Magento core and Hyva declare.
2. **Header-content argument override** — Hyva's `header.phtml` gates the compare/wishlist icons on `show_compare` / `show_wishlist` arguments of the `header-content` block; both forced to `false`.
3. **Helper plugin** on `Magento\Wishlist\Helper\Data::isAllow()` / `isAllowInCart()` — forces false so any third-party block that checks the helper drops out silently.
4. **Block plugin** on `Magento\Catalog\Block\Product\AbstractProduct::getAddToCompareUrl()` / `getAddToWishlistUrl()` — returns empty string, which breaks the `if ($block->getAddToCompareUrl())` gate in every Luma widget template (`new_grid.phtml`, `new_list.phtml`, `listing.phtml`) without any template overrides.
5. **Hyva ViewModel plugins** on `Hyva\Theme\ViewModel\Wishlist` and `Hyva\Theme\ViewModel\ProductCompare` — `isEnabled`, `isAllowInCart`, `showInProductList`, `showOnProductPage`, `showCompareSidebar` all forced to false.
6. **Route blocking** — predispatch event for `controller_action_predispatch_wishlist` (runs *before* the customer-session auth redirect, so `/wishlist/*` returns an honest 404 instead of a 302 to login) plus a controller plugin on `Magento\Catalog\Controller\Product\Compare::execute` to 404 compare routes. AJAX / POST requests return a JSON stub so stale JS listeners fail quietly.

---

## Compatibility

| Requirement | Supported |
|---|---|
| Magento Open Source | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce | 2.4.4 — 2.4.8 |
| PHP | 8.1, 8.2, 8.3, 8.4 |
| Hyva Theme | 1.0+ (fully compatible) |
| Luma Theme | Native support |
| Panth Core | ^1.0 (installed automatically) |

---

## Installation

```bash
composer require mage2kishan/module-disable-wishlist-compare
bin/magento module:enable Panth_Core Panth_DisableWishlistCompare
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

### Verify

```bash
bin/magento module:status Panth_DisableWishlistCompare
# Module is enabled
```

Check any page — header icons, PDP buttons, category add-to links, and cart row actions for wishlist/compare should all be gone. `/wishlist/` and `/catalog/product_compare/` should return 404.

---

## Admin Toggles

Navigate to **Stores → Configuration → Panth Infotech → Disable Wishlist & Compare**. A direct link also appears under the **Panth Infotech** admin sidebar.

| Setting | Default | What it controls |
|---|---|---|
| **Module Enabled** | Yes | Master switch. If No, all runtime plugins (helper / ViewModel / controller) are disabled. |
| **Disable Wishlist** | Yes | Gates all wishlist-related runtime plugins + route blocking. |
| **Disable Compare** | Yes | Gates all compare-related runtime plugins + route blocking. |
| **Block Direct URL Access** | Yes | When Yes, `/wishlist/*` and `/catalog/product_compare/*` return 404. Set to No if you want the routes reachable without the UI. |

> **Note on layout removals:** The layout XML removes are module-level — they apply whenever the module is **enabled at the CLI**. The admin toggles above only affect *runtime* plugins and route blocking. To restore the UI while keeping the module installed, disable it via CLI (see below).

---

## Restoring the UI

### Option A — keep the module, restore UI

```bash
bin/magento module:disable Panth_DisableWishlistCompare
bin/magento cache:flush
```

### Option B — uninstall entirely

```bash
bin/magento module:disable Panth_DisableWishlistCompare
composer remove mage2kishan/module-disable-wishlist-compare
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

---

## Troubleshooting

### The compare icon still shows in the Hyva header

You probably hit the edge case where a child theme re-adds `header-compare` or passes its own `show_compare="true"` argument to `header-content`. Confirm by checking `var/log/system.log` for layout errors, and ensure your child theme's `Magento_Theme/layout/default.xml` doesn't re-declare `show_compare`.

### `/wishlist/` redirects to login (302) instead of 404

Flush the config cache — the predispatch observer reads the *"Block Direct URL Access"* flag at runtime:

```bash
bin/magento cache:flush config
```

### A third-party module re-adds an "Add to Wish List" button

This module plugs every Magento-core integration point. If a third-party module renders its own wishlist button via a custom block, add `<referenceBlock name="..." remove="true"/>` for that block in a child override or raise an issue.

### DI compile fails after `composer require`

Make sure `Panth_Core`, `Magento_Wishlist`, and `Magento_Catalog` are enabled — they are hard dependencies.

---

## Support

- **Issues:** [github.com/mage2sk/module-disable-wishlist-compare/issues](https://github.com/mage2sk/module-disable-wishlist-compare/issues)
- **Agency:** [Panth Infotech on Upwork](https://www.upwork.com/agencies/1881421506131960778/)
- **Direct:** [kishansavaliya.com](https://kishansavaliya.com) — [Get a free quote](https://kishansavaliya.com/get-quote)
