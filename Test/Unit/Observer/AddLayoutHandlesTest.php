<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use Panth\DisableWishlistCompare\Observer\AddLayoutHandles;
use Panth\DisableWishlistCompare\Test\Unit\Fixture\ConfigFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AddLayoutHandlesTest extends TestCase
{
    use ConfigFactoryTrait;

    /**
     * @param mixed $layout
     * @return Observer
     */
    private function observerWith($layout): Observer
    {
        return new Observer(['event' => new Event(['layout' => $layout])]);
    }

    /**
     * @param array $added
     * @return LayoutInterface
     */
    private function recordingLayout(array &$added): LayoutInterface
    {
        $update = $this->createStub(ProcessorInterface::class);
        $update->method('addHandle')->willReturnCallback(
            static function ($handle) use (&$added, $update) {
                $added[] = $handle;
                return $update;
            }
        );
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getUpdate')->willReturn($update);

        return $layout;
    }

    /**
     * @return array<string, array{bool, bool, string[]}>
     */
    public static function handleCases(): array
    {
        return [
            'nothing disabled' => [false, false, []],
            'wishlist only'    => [true, false, [AddLayoutHandles::HANDLE_WISHLIST]],
            'compare only'     => [false, true, [AddLayoutHandles::HANDLE_COMPARE]],
            'both'             => [
                true,
                true,
                [AddLayoutHandles::HANDLE_WISHLIST, AddLayoutHandles::HANDLE_COMPARE],
            ],
        ];
    }

    #[DataProvider('handleCases')]
    public function testAddsOneHandlePerDisabledFeature(bool $wishlist, bool $compare, array $expected): void
    {
        $added = [];
        $layout = $this->recordingLayout($added);

        (new AddLayoutHandles($this->configAll($wishlist, $compare)))->execute($this->observerWith($layout));

        $this->assertSame($expected, $added);
    }

    public function testNoHandlesWhenModuleIsDisabledEvenIfFeaturesAreFlagged(): void
    {
        $added = [];
        $layout = $this->recordingLayout($added);

        (new AddLayoutHandles($this->configAll(true, true, false, false)))->execute($this->observerWith($layout));

        $this->assertSame([], $added);
    }

    public function testHandleNamesAreStable(): void
    {
        $this->assertSame('panth_disable_wc_wishlist', AddLayoutHandles::HANDLE_WISHLIST);
        $this->assertSame('panth_disable_wc_compare', AddLayoutHandles::HANDLE_COMPARE);
    }

    public function testIgnoresEventsWithoutALayoutObject(): void
    {
        $config = $this->createMock(\Panth\DisableWishlistCompare\Helper\Config::class);
        $config->expects($this->never())->method('isWishlistDisabled');
        $config->expects($this->never())->method('isCompareDisabled');

        $handler = new AddLayoutHandles($config);
        $handler->execute($this->observerWith('not-a-layout'));
        $handler->execute($this->observerWith(null));
    }
}
