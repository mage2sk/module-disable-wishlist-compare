<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Test\Unit\Plugin\Helper;

use Magento\Wishlist\Helper\Data as WishlistHelper;
use Panth\DisableWishlistCompare\Plugin\Helper\WishlistHelperPlugin;
use Panth\DisableWishlistCompare\Test\Unit\Fixture\ConfigFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WishlistHelperPluginTest extends TestCase
{
    use ConfigFactoryTrait;

    /**
     * @return array<string, array{bool}>
     */
    public static function originalResults(): array
    {
        return ['allowed' => [true], 'not allowed' => [false]];
    }

    #[DataProvider('originalResults')]
    public function testOriginalResultIsKeptWhileWishlistIsEnabled(bool $original): void
    {
        $plugin = new WishlistHelperPlugin($this->configAll(false, true));
        $helper = $this->createStub(WishlistHelper::class);

        $this->assertSame($original, $plugin->afterIsAllow($helper, $original));
        $this->assertSame($original, $plugin->afterIsAllowInCart($helper, $original));
    }

    #[DataProvider('originalResults')]
    public function testWishlistIsNeverAllowedOnceDisabled(bool $original): void
    {
        $plugin = new WishlistHelperPlugin($this->configAll(true, false));
        $helper = $this->createStub(WishlistHelper::class);

        $this->assertFalse($plugin->afterIsAllow($helper, $original));
        $this->assertFalse($plugin->afterIsAllowInCart($helper, $original));
    }

    public function testMasterSwitchOffLeavesTheHelperAlone(): void
    {
        $plugin = new WishlistHelperPlugin($this->configAll(true, true, true, false));
        $helper = $this->createStub(WishlistHelper::class);

        $this->assertTrue($plugin->afterIsAllow($helper, true));
        $this->assertTrue($plugin->afterIsAllowInCart($helper, true));
    }
}
