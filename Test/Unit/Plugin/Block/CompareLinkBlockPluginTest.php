<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Test\Unit\Plugin\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Panth\DisableWishlistCompare\Plugin\Block\CompareLinkBlockPlugin;
use Panth\DisableWishlistCompare\Test\Unit\Fixture\ConfigFactoryTrait;
use PHPUnit\Framework\TestCase;

class CompareLinkBlockPluginTest extends TestCase
{
    use ConfigFactoryTrait;

    private const URL = 'https://shop.test/catalog/product_compare/add/';

    public function testUrlsPassThroughWhenNothingIsDisabled(): void
    {
        $plugin = new CompareLinkBlockPlugin($this->configAll(false, false));
        $block = $this->createStub(AbstractProduct::class);

        $this->assertSame(self::URL, $plugin->afterGetAddToCompareUrl($block, self::URL));
        $this->assertSame(self::URL, $plugin->afterGetAddToWishlistUrl($block, self::URL));
    }

    public function testCompareUrlIsBlankedOnlyWhenCompareIsDisabled(): void
    {
        $plugin = new CompareLinkBlockPlugin($this->configAll(false, true));
        $block = $this->createStub(AbstractProduct::class);

        $this->assertSame('', $plugin->afterGetAddToCompareUrl($block, self::URL));
        $this->assertSame(self::URL, $plugin->afterGetAddToWishlistUrl($block, self::URL));
    }

    public function testWishlistUrlIsBlankedOnlyWhenWishlistIsDisabled(): void
    {
        $plugin = new CompareLinkBlockPlugin($this->configAll(true, false));
        $block = $this->createStub(AbstractProduct::class);

        $this->assertSame(self::URL, $plugin->afterGetAddToCompareUrl($block, self::URL));
        $this->assertSame('', $plugin->afterGetAddToWishlistUrl($block, self::URL));
    }

    public function testMasterSwitchOffKeepsBothUrls(): void
    {
        $plugin = new CompareLinkBlockPlugin($this->configAll(true, true, false, false));
        $block = $this->createStub(AbstractProduct::class);

        $this->assertSame(self::URL, $plugin->afterGetAddToCompareUrl($block, self::URL));
        $this->assertSame(self::URL, $plugin->afterGetAddToWishlistUrl($block, self::URL));
    }

    public function testNonStringResultsAreCastToString(): void
    {
        $plugin = new CompareLinkBlockPlugin($this->configAll(false, false));
        $block = $this->createStub(AbstractProduct::class);

        $this->assertSame('', $plugin->afterGetAddToCompareUrl($block, null));
        $this->assertSame('', $plugin->afterGetAddToWishlistUrl($block, false));
    }
}
