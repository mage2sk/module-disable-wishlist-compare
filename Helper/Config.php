<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XML_ENABLED          = 'panth_disable_wc/general/enabled';
    public const XML_DISABLE_WISHLIST = 'panth_disable_wc/general/disable_wishlist';
    public const XML_DISABLE_COMPARE  = 'panth_disable_wc/general/disable_compare';
    public const XML_BLOCK_ROUTES     = 'panth_disable_wc/general/block_routes';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->flag(self::XML_ENABLED, $storeId);
    }

    public function isWishlistDisabled(?int $storeId = null): bool
    {
        return $this->isEnabled($storeId) && $this->flag(self::XML_DISABLE_WISHLIST, $storeId);
    }

    public function isCompareDisabled(?int $storeId = null): bool
    {
        return $this->isEnabled($storeId) && $this->flag(self::XML_DISABLE_COMPARE, $storeId);
    }

    public function isRouteBlockingEnabled(?int $storeId = null): bool
    {
        return $this->isEnabled($storeId) && $this->flag(self::XML_BLOCK_ROUTES, $storeId);
    }

    private function flag(string $path, ?int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
