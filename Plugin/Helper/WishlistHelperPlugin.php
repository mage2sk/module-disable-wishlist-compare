<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Helper;

use Magento\Wishlist\Helper\Data as WishlistHelper;
use Panth\DisableWishlistCompare\Helper\Config;

/**
 * Short-circuit wishlist gating to false whenever the module is on. Everything
 * downstream (Hyva ViewModels, Magento blocks, links, sidebar, buttons) asks
 * this helper first — killing it here removes 95% of UI with zero layout edits.
 */
class WishlistHelperPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * @param WishlistHelper $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsAllow(WishlistHelper $subject, bool $result): bool
    {
        return $this->config->isWishlistDisabled() ? false : $result;
    }

    /**
     * @param WishlistHelper $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsAllowInCart(WishlistHelper $subject, bool $result): bool
    {
        return $this->config->isWishlistDisabled() ? false : $result;
    }
}
