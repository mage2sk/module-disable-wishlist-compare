<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Test\Unit\Fixture;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Panth\DisableWishlistCompare\Helper\Config;

/**
 * Builds a real Config helper backed by an in-memory flag map.
 */
trait ConfigFactoryTrait
{
    /**
     * Create a Config whose isSetFlag() answers from the given map.
     *
     * @param array<string, bool> $flags
     * @return Config
     */
    private function buildConfig(array $flags): Config
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturnCallback(
            static fn(string $path) => (bool) ($flags[$path] ?? false)
        );

        return new Config($scopeConfig);
    }

    /**
     * Create a Config with every module flag set explicitly.
     *
     * @param bool $wishlist
     * @param bool $compare
     * @param bool $routes
     * @param bool $enabled
     * @return Config
     */
    private function configAll(bool $wishlist, bool $compare, bool $routes = false, bool $enabled = true): Config
    {
        return $this->buildConfig([
            Config::XML_ENABLED          => $enabled,
            Config::XML_DISABLE_WISHLIST => $wishlist,
            Config::XML_DISABLE_COMPARE  => $compare,
            Config::XML_BLOCK_ROUTES     => $routes,
        ]);
    }
}
