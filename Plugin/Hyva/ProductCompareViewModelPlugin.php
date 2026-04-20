<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Hyva;

use Panth\DisableWishlistCompare\Helper\Config;

/**
 * Plugin for Hyva\Theme\ViewModel\ProductCompare.
 *
 * Hyva uses this view-model to gate the header compare icon, product-card
 * compare link and sidebar. Returning false from all three display methods
 * drops every visible compare UI element.
 *
 * `$subject` is untyped so this file loads even without Hyva installed.
 */
class ProductCompareViewModelPlugin
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterShowInProductList($subject, bool $result): bool
    {
        return $this->config->isCompareDisabled() ? false : $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterShowOnProductPage($subject, bool $result): bool
    {
        return $this->config->isCompareDisabled() ? false : $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterShowCompareSidebar($subject, bool $result): bool
    {
        return $this->config->isCompareDisabled() ? false : $result;
    }
}
