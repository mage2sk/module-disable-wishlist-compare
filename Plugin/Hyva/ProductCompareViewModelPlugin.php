<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Hyva;

use Panth\DisableWishlistCompare\Helper\Config;

class ProductCompareViewModelPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function afterShowInProductList($subject, bool $result): bool
    {
        return $this->config->isCompareDisabled() ? false : $result;
    }

    public function afterShowOnProductPage($subject, bool $result): bool
    {
        return $this->config->isCompareDisabled() ? false : $result;
    }

    public function afterShowCompareSidebar($subject, bool $result): bool
    {
        return $this->config->isCompareDisabled() ? false : $result;
    }
}
