# Changelog

All notable changes to this extension are documented here. The format is
based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this
project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.1]

### Fixed
- **Admin-config section now appears in the same Panth Infotech tab as
  the rest of the Panth modules.** v1.0.0 declared its own
  `panth_infotech` tab with sortOrder 999, which pushed it to the bottom
  of the admin nav away from all the other Panth_* modules. Switched to
  the shared `panth` tab declared by `Panth_Core` so it sits alongside
  Core, Extra Fee, AdvancedSEO, and the rest.
- **Added `etc/adminhtml/menu.xml`** with a direct link under
  *Panth Infotech → Disable Wishlist & Compare* that jumps straight to
  the config section. Matches the menu pattern every other Panth_*
  module uses.

### Added
- **`Panth_Core` as a required dependency** (`mage2kishan/module-core: ^1.0`).
  Needed because the shared `panth` admin tab and the
  `Panth_Core::panth_extensions` menu parent are both declared by
  Panth_Core. Module sequence and composer require updated.
- **Admin-configuration screenshot** (`docs/admin-configuration.png`) and
  a Preview section in the README showing the four toggles.

## [1.0.0] — Initial release

### Added

- Admin config section `Panth Infotech → Disable Wishlist & Compare` with
  four toggles: master enable, disable wishlist, disable compare, block
  direct URL access. All default to **Yes**.
- Layout XML removals on the common handles (`default`, `catalog_product_view`,
  `catalog_category_view`, `checkout_cart_index`, `customer_account`) for
  every wishlist + compare block the audit identified — header links,
  sidebars, PDP add-to buttons (incl. related/upsell), category-list
  add-to icons, cart "Move to Wishlist" row action, customer-account side
  nav tab, and Hyva's `header-compare` icon. All gated by `ifconfig` so
  the admin toggles actually take effect without a re-compile.
- Plugin on `Magento\Wishlist\Helper\Data::isAllow()` / `isAllowInCart()`
  that forces false when wishlist is disabled. Drops any stray wishlist
  widget or third-party UI that asks the helper before rendering.
- Plugin on `Hyva\Theme\ViewModel\Wishlist::isEnabled()` / `isAllowInCart()`
  and `Hyva\Theme\ViewModel\ProductCompare::show*` that returns false
  when the respective feature is disabled. Harmless on Luma-only stores
  — Magento's plugin runtime skips plugin declarations whose target class
  never gets instantiated.
- Plugin on `Magento\Wishlist\Controller\AbstractIndex::execute` and
  `Magento\Catalog\Controller\Product\Compare::execute` that returns 404
  on HTML requests and a JSON stub on AJAX/POST. Covers every action in
  both namespaces (wishlist: Index, Add, Remove, Update, Cart, Fromcart,
  Configure, UpdateItemOptions, Share, Send; compare: Index, Add, Remove,
  Clear) without individual per-controller plugins.
