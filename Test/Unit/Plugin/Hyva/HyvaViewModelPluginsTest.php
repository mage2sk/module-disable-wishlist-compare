<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Test\Unit\Plugin\Hyva;

use Panth\DisableWishlistCompare\Plugin\Hyva\ProductCompareViewModelPlugin;
use Panth\DisableWishlistCompare\Plugin\Hyva\WishlistViewModelPlugin;
use Panth\DisableWishlistCompare\Test\Unit\Fixture\ConfigFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The Hyva view models are optional dependencies, so a plain object stands in for the subject.
 */
class HyvaViewModelPluginsTest extends TestCase
{
    use ConfigFactoryTrait;

    private const COMPARE_METHODS  = ['afterShowInProductList', 'afterShowOnProductPage', 'afterShowCompareSidebar'];
    private const WISHLIST_METHODS = ['afterIsEnabled', 'afterIsAllowInCart'];

    /**
     * @return array<string, array{bool}>
     */
    public static function originalResults(): array
    {
        return ['shown' => [true], 'hidden' => [false]];
    }

    #[DataProvider('originalResults')]
    public function testCompareViewModelKeepsOriginalWhileCompareIsEnabled(bool $original): void
    {
        $plugin = new ProductCompareViewModelPlugin($this->configAll(true, false));

        foreach (self::COMPARE_METHODS as $method) {
            $this->assertSame($original, $plugin->$method(new \stdClass(), $original), $method);
        }
    }

    #[DataProvider('originalResults')]
    public function testCompareViewModelHidesEverythingWhenCompareIsDisabled(bool $original): void
    {
        $plugin = new ProductCompareViewModelPlugin($this->configAll(false, true));

        foreach (self::COMPARE_METHODS as $method) {
            $this->assertFalse($plugin->$method(new \stdClass(), $original), $method);
        }
    }

    #[DataProvider('originalResults')]
    public function testWishlistViewModelKeepsOriginalWhileWishlistIsEnabled(bool $original): void
    {
        $plugin = new WishlistViewModelPlugin($this->configAll(false, true));

        foreach (self::WISHLIST_METHODS as $method) {
            $this->assertSame($original, $plugin->$method(new \stdClass(), $original), $method);
        }
    }

    #[DataProvider('originalResults')]
    public function testWishlistViewModelHidesEverythingWhenWishlistIsDisabled(bool $original): void
    {
        $plugin = new WishlistViewModelPlugin($this->configAll(true, false));

        foreach (self::WISHLIST_METHODS as $method) {
            $this->assertFalse($plugin->$method(new \stdClass(), $original), $method);
        }
    }

    public function testMasterSwitchOffLeavesBothViewModelsAlone(): void
    {
        $config = $this->configAll(true, true, true, false);
        $compare = new ProductCompareViewModelPlugin($config);
        $wishlist = new WishlistViewModelPlugin($config);

        foreach (self::COMPARE_METHODS as $method) {
            $this->assertTrue($compare->$method(new \stdClass(), true), $method);
        }
        foreach (self::WISHLIST_METHODS as $method) {
            $this->assertTrue($wishlist->$method(new \stdClass(), true), $method);
        }
    }
}
