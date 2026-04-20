<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Panth\DisableWishlistCompare\Helper\Config;

/**
 * Plugin for Magento\Catalog\Block\Product\AbstractProduct.
 *
 * Luma widgets (new_grid.phtml / new_list.phtml, listing.phtml) hardcode
 * `$showCompare = true;` and then check `$block->getAddToCompareUrl()` — so
 * the only way to suppress the compare link on those widgets without
 * template overrides is to return an empty URL from the helper method. The
 * Hyva ViewModel path is already handled separately; this one covers Luma.
 *
 * Also kill the add-to-wishlist URL path via the same abstract class —
 * this removes leftover wishlist links in Luma widgets and category lists.
 */
class CompareLinkBlockPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAddToCompareUrl(AbstractProduct $subject, $result): string
    {
        return $this->config->isCompareDisabled() ? '' : (string) $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAddToWishlistUrl(AbstractProduct $subject, $result): string
    {
        return $this->config->isWishlistDisabled() ? '' : (string) $result;
    }
}
