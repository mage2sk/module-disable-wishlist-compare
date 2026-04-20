# Disable Wishlist & Compare for Magento 2 | Hyva + Luma

Removes every Wishlist and Compare UI element from a Magento 2 storefront and
returns 404 on their routes. Works identically on **Hyva** and **Luma** — no
theme-specific code beyond the one Hyva header block that Luma doesn't use
anyway.

## What it disables

| Area | Wishlist | Compare |
| --- | --- | --- |
| Header (Hyva icon, Luma top-link) | ✅ | ✅ |
| Sidebar (`wishlist_sidebar`, `catalog.compare.sidebar`) | ✅ | ✅ |
| Product detail page (add-to buttons, related / upsell sections) | ✅ | ✅ |
| Category product list items | ✅ | ✅ |
| Cart "Move to Wishlist" row action | ✅ | — |
| Customer-account side navigation tab | ✅ | — |
| Direct URL access (`/wishlist/*`, `/catalog/product_compare/*`) | ✅ 404 | ✅ 404 |
| Helper `Magento\Wishlist\Helper\Data::isAllow()` | ✅ forced false | — |
| Hyva `ViewModel\Wishlist`, `ViewModel\ProductCompare` | ✅ forced false | ✅ forced false |

## Admin config

`Stores → Configuration → Panth Infotech → Disable Wishlist & Compare`

- **Module Enabled** — master switch. No = module does nothing.
- **Disable Wishlist** — toggle the wishlist half.
- **Disable Compare** — toggle the compare half.
- **Block Direct URL Access** — 404 direct hits to the routes (recommended).

All four default to **Yes**, so after installing + enabling the module the
features are gone immediately; flip them back to No to re-enable.

## Install

```
composer require mage2kishan/module-disable-wishlist-compare
bin/magento module:enable Panth_DisableWishlistCompare
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```
