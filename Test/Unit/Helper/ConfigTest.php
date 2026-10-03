<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Panth\DisableWishlistCompare\Helper\Config;
use Panth\DisableWishlistCompare\Test\Unit\Fixture\ConfigFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    use ConfigFactoryTrait;

    public function testEverythingIsOffWhenNothingIsConfigured(): void
    {
        $config = $this->buildConfig([]);

        $this->assertFalse($config->isEnabled());
        $this->assertFalse($config->isWishlistDisabled());
        $this->assertFalse($config->isCompareDisabled());
        $this->assertFalse($config->isRouteBlockingEnabled());
    }

    public function testFeatureFlagsAreIgnoredWhileTheMasterSwitchIsOff(): void
    {
        $config = $this->configAll(true, true, true, false);

        $this->assertFalse($config->isEnabled());
        $this->assertFalse($config->isWishlistDisabled());
        $this->assertFalse($config->isCompareDisabled());
        $this->assertFalse($config->isRouteBlockingEnabled());
    }

    public function testMasterSwitchAloneDisablesNothing(): void
    {
        $config = $this->configAll(false, false, false, true);

        $this->assertTrue($config->isEnabled());
        $this->assertFalse($config->isWishlistDisabled());
        $this->assertFalse($config->isCompareDisabled());
        $this->assertFalse($config->isRouteBlockingEnabled());
    }

    /**
     * @return array<string, array{bool, bool, bool}>
     */
    public static function flagCombinations(): array
    {
        return [
            'wishlist only' => [true, false, false],
            'compare only'  => [false, true, false],
            'routes only'   => [false, false, true],
            'all three'     => [true, true, true],
        ];
    }

    #[DataProvider('flagCombinations')]
    public function testEachFlagIsReportedIndependently(bool $wishlist, bool $compare, bool $routes): void
    {
        $config = $this->configAll($wishlist, $compare, $routes);

        $this->assertSame($wishlist, $config->isWishlistDisabled());
        $this->assertSame($compare, $config->isCompareDisabled());
        $this->assertSame($routes, $config->isRouteBlockingEnabled());
    }

    public function testFlagsAreReadAtStoreScopeForTheGivenStore(): void
    {
        $calls = [];
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturnCallback(
            static function (string $path, string $scope, $storeId) use (&$calls) {
                $calls[] = [$path, $scope, $storeId];
                return true;
            }
        );

        $this->assertTrue((new Config($scopeConfig))->isCompareDisabled(7));
        $this->assertSame(
            [
                [Config::XML_ENABLED, ScopeInterface::SCOPE_STORE, 7],
                [Config::XML_DISABLE_COMPARE, ScopeInterface::SCOPE_STORE, 7],
            ],
            $calls
        );
    }

    public function testFeatureFlagIsNotReadWhenModuleIsDisabled(): void
    {
        $paths = [];
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturnCallback(
            static function (string $path) use (&$paths) {
                $paths[] = $path;
                return false;
            }
        );

        $this->assertFalse((new Config($scopeConfig))->isWishlistDisabled(3));
        $this->assertSame([Config::XML_ENABLED], $paths);
    }

    public function testStoreIdDefaultsToNull(): void
    {
        $stores = [];
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturnCallback(
            static function (string $path, string $scope, $storeId) use (&$stores) {
                $stores[] = $storeId;
                return true;
            }
        );

        $this->assertTrue((new Config($scopeConfig))->isRouteBlockingEnabled());
        $this->assertSame([null, null], $stores);
    }
}
