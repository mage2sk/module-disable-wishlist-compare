<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Hyva;

use Panth\DisableWishlistCompare\Helper\Config;

/**
 * Plugin for Hyva\Theme\ViewModel\Wishlist.
 *
 * Hyva's header + product-card templates call isEnabled() / isAllowInCart()
 * on this view-model to decide whether to render the wishlist icon or button.
 * Forcing both to false makes Hyva's markup drop out cleanly.
 *
 * Intentionally untyped `$subject` so this file loads even when the
 * Hyva_Theme module is not installed (Luma-only stores). The plugin
 * declaration in di.xml is gated by the target class existing at
 * DI-compile time anyway, so there is no harm from the declaration.
 */
class WishlistViewModelPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsEnabled($subject, bool $result): bool
    {
        return $this->config->isWishlistDisabled() ? false : $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsAllowInCart($subject, bool $result): bool
    {
        return $this->config->isWishlistDisabled() ? false : $result;
    }
}
