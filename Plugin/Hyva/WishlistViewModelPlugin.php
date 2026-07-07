<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Hyva;

use Panth\DisableWishlistCompare\Helper\Config;

class WishlistViewModelPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function afterIsEnabled($subject, bool $result): bool
    {
        return $this->config->isWishlistDisabled() ? false : $result;
    }

    public function afterIsAllowInCart($subject, bool $result): bool
    {
        return $this->config->isWishlistDisabled() ? false : $result;
    }
}
