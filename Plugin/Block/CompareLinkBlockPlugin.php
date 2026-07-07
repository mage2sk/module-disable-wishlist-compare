<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Panth\DisableWishlistCompare\Helper\Config;

class CompareLinkBlockPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function afterGetAddToCompareUrl(AbstractProduct $subject, $result): string
    {
        return $this->config->isCompareDisabled() ? '' : (string) $result;
    }

    public function afterGetAddToWishlistUrl(AbstractProduct $subject, $result): string
    {
        return $this->config->isWishlistDisabled() ? '' : (string) $result;
    }
}
