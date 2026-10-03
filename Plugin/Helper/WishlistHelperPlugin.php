<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Helper;

use Magento\Wishlist\Helper\Data as WishlistHelper;
use Panth\DisableWishlistCompare\Helper\Config;

class WishlistHelperPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function afterIsAllow(WishlistHelper $subject, bool $result): bool
    {
        return $this->config->isWishlistDisabled() ? false : $result;
    }

    public function afterIsAllowInCart(WishlistHelper $subject, bool $result): bool
    {
        return $this->config->isWishlistDisabled() ? false : $result;
    }
}
