<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Test\Unit\Plugin\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Panth\DisableWishlistCompare\Helper\Config;
use Panth\DisableWishlistCompare\Plugin\Controller\DisableCompareController;
use Panth\DisableWishlistCompare\Plugin\Controller\DisableWishlistController;
use Panth\DisableWishlistCompare\Test\Unit\Fixture\ConfigFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Both controller plugins share the same decision logic; each case runs for both.
 */
class DisableControllerPluginsTest extends TestCase
{
    use ConfigFactoryTrait;

    private const WISHLIST = 'wishlist';
    private const COMPARE  = 'compare';

    /**
     * @param bool $ajax
     * @param bool $post
     * @return RequestInterface
     */
    private function request(bool $ajax, bool $post): RequestInterface
    {
        $request = $this->createStub(HttpRequest::class);
        $request->method('isAjax')->willReturn($ajax);
        $request->method('isPost')->willReturn($post);

        return $request;
    }

    /**
     * @param string $kind
     * @param Config $config
     * @param RequestInterface $request
     * @param ResultFactory $factory
     * @return DisableWishlistController|DisableCompareController
     */
    private function plugin(string $kind, Config $config, RequestInterface $request, ResultFactory $factory)
    {
        return $kind === self::WISHLIST
            ? new DisableWishlistController($config, $request, $factory)
            : new DisableCompareController($config, $request, $factory);
    }

    /**
     * Config with only the given feature disabled (plus route blocking as asked).
     *
     * @param string $kind
     * @param bool $routes
     * @param bool $enabled
     * @return Config
     */
    private function configFor(string $kind, bool $routes, bool $enabled = true): Config
    {
        return $this->configAll($kind === self::WISHLIST, $kind === self::COMPARE, $routes, $enabled);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function kinds(): array
    {
        return ['wishlist' => [self::WISHLIST], 'compare' => [self::COMPARE]];
    }

    #[DataProvider('kinds')]
    public function testProceedsWhenRouteBlockingIsOff(string $kind): void
    {
        $factory = $this->createMock(ResultFactory::class);
        $factory->expects($this->never())->method('create');

        $result = $this->plugin($kind, $this->configFor($kind, false), $this->request(false, false), $factory)
            ->aroundExecute($this->createStub(ActionInterface::class), static fn() => 'original');

        $this->assertSame('original', $result);
    }

    #[DataProvider('kinds')]
    public function testProceedsWhenModuleIsDisabled(string $kind): void
    {
        $result = $this->plugin(
            $kind,
            $this->configFor($kind, true, false),
            $this->request(false, false),
            $this->createStub(ResultFactory::class)
        )->aroundExecute($this->createStub(ActionInterface::class), static fn() => 'original');

        $this->assertSame('original', $result);
    }

    #[DataProvider('kinds')]
    public function testProceedsWhenOnlyTheOtherFeatureIsDisabled(string $kind): void
    {
        $other = $kind === self::WISHLIST
            ? $this->configAll(false, true, true)
            : $this->configAll(true, false, true);

        $result = $this->plugin($kind, $other, $this->request(true, true), $this->createStub(ResultFactory::class))
            ->aroundExecute($this->createStub(ActionInterface::class), static fn() => 'original');

        $this->assertSame('original', $result);
    }

    #[DataProvider('kinds')]
    public function testPlainGetRequestIsAnswered404(string $kind): void
    {
        $called = false;
        $plugin = $this->plugin(
            $kind,
            $this->configFor($kind, true),
            $this->request(false, false),
            $this->createStub(ResultFactory::class)
        );

        try {
            $plugin->aroundExecute(
                $this->createStub(ActionInterface::class),
                static function () use (&$called) {
                    $called = true;
                }
            );
            $this->fail('NotFoundException expected');
        } catch (NotFoundException $e) {
            $this->assertSame('Not Found', $e->getMessage());
        }
        $this->assertFalse($called, 'The original controller must not run');
    }

    /**
     * @return array<string, array{string, bool, bool}>
     */
    public static function jsonCases(): array
    {
        $cases = [];
        foreach ([self::WISHLIST, self::COMPARE] as $kind) {
            $cases[$kind . ' ajax'] = [$kind, true, false];
            $cases[$kind . ' post'] = [$kind, false, true];
        }

        return $cases;
    }

    #[DataProvider('jsonCases')]
    public function testAjaxOrPostGetsAJsonRefusal(string $kind, bool $ajax, bool $post): void
    {
        $json = $this->createMock(Json::class);
        $json->expects($this->once())->method('setData')
            ->with(['success' => false, 'message' => 'Not available.'])
            ->willReturnSelf();
        $factory = $this->createMock(ResultFactory::class);
        $factory->expects($this->once())->method('create')->with(ResultFactory::TYPE_JSON)->willReturn($json);

        $result = $this->plugin($kind, $this->configFor($kind, true), $this->request($ajax, $post), $factory)
            ->aroundExecute(
                $this->createStub(ActionInterface::class),
                function () {
                    $this->fail('The original controller must not run');
                }
            );

        $this->assertSame($json, $result);
    }
}
