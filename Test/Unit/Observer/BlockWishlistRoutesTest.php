<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Test\Unit\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Panth\DisableWishlistCompare\Helper\Config;
use Panth\DisableWishlistCompare\Observer\BlockWishlistRoutes;
use Panth\DisableWishlistCompare\Test\Unit\Fixture\ConfigFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BlockWishlistRoutesTest extends TestCase
{
    use ConfigFactoryTrait;

    /**
     * @var array<string, mixed>
     */
    private array $routed = [];

    /**
     * Request stub that records the noroute rewrite.
     *
     * @param bool $ajax
     * @param bool $post
     * @return HttpRequest
     */
    private function request(bool $ajax, bool $post): HttpRequest
    {
        $this->routed = [];
        $routed = &$this->routed;
        $request = $this->createStub(HttpRequest::class);
        $request->method('isAjax')->willReturn($ajax);
        $request->method('isPost')->willReturn($post);
        foreach (['setDispatched', 'setModuleName', 'setControllerName', 'setActionName'] as $method) {
            $request->method($method)->willReturnCallback(
                static function ($value) use (&$routed, $method, $request) {
                    $routed[$method] = $value;
                    return $request;
                }
            );
        }

        return $request;
    }

    /**
     * @param HttpRequest $request
     * @param ResponseInterface $response
     * @return Observer
     */
    private function observer(HttpRequest $request, ResponseInterface $response): Observer
    {
        $controller = $this->createStub(Action::class);
        $controller->method('getRequest')->willReturn($request);
        $controller->method('getResponse')->willReturn($response);

        return new Observer(['event' => new Event(['controller_action' => $controller])]);
    }

    /**
     * @param Config $config
     * @param ActionFlag $flag
     * @return BlockWishlistRoutes
     */
    private function subject(Config $config, ActionFlag $flag): BlockWishlistRoutes
    {
        return new BlockWishlistRoutes($config, $flag, $this->createStub(ForwardFactory::class));
    }

    /**
     * @return array<string, array{bool, bool, bool}>
     */
    public static function inactiveConfigs(): array
    {
        return [
            'module disabled'        => [true, true, false],
            'route blocking off'     => [true, false, true],
            'wishlist not disabled'  => [false, true, true],
        ];
    }

    #[DataProvider('inactiveConfigs')]
    public function testDoesNothingUnlessRouteBlockingAndWishlistDisableAreBothOn(
        bool $wishlist,
        bool $routes,
        bool $enabled
    ): void {
        $flag = $this->createMock(ActionFlag::class);
        $flag->expects($this->never())->method('set');
        $response = $this->createMock(HttpResponse::class);
        $response->expects($this->never())->method('clearHeader');

        $this->subject($this->configAll($wishlist, false, $routes, $enabled), $flag)
            ->execute($this->observer($this->request(false, false), $response));

        $this->assertSame([], $this->routed);
    }

    public function testIgnoresEventsWithoutAnActionController(): void
    {
        $flag = $this->createMock(ActionFlag::class);
        $flag->expects($this->never())->method('set');

        $this->subject($this->configAll(true, false, true), $flag)
            ->execute(new Observer(['event' => new Event(['controller_action' => new \stdClass()])]));
    }

    public function testGetRequestIsForwardedToNoroute(): void
    {
        $flag = $this->createMock(ActionFlag::class);
        $flag->expects($this->exactly(2))->method('set')->with('', Action::FLAG_NO_DISPATCH, true);
        $response = $this->createMock(HttpResponse::class);
        $response->expects($this->once())->method('clearHeader')->with('Location');
        $response->expects($this->never())->method('representJson');

        $this->subject($this->configAll(true, false, true), $flag)
            ->execute($this->observer($this->request(false, false), $response));

        $this->assertSame(
            [
                'setDispatched'     => false,
                'setModuleName'     => 'noroute',
                'setControllerName' => 'index',
                'setActionName'     => 'index',
            ],
            $this->routed
        );
    }

    public function testNoDispatchFlagIsAlsoSetForTheRewrittenNorouteRequest(): void
    {
        $snapshots = [];
        $flag = $this->createMock(ActionFlag::class);
        $request = $this->request(false, false);
        $routed = &$this->routed;
        $flag->expects($this->exactly(2))->method('set')->willReturnCallback(
            static function (string $action, string $name, bool $value) use (&$snapshots, &$routed) {
                $snapshots[] = [$action, $name, $value, $routed['setModuleName'] ?? null];
            }
        );

        $this->subject($this->configAll(true, false, true), $flag)
            ->execute($this->observer($request, $this->createStub(HttpResponse::class)));

        $this->assertSame(
            [
                ['', Action::FLAG_NO_DISPATCH, true, null],
                ['', Action::FLAG_NO_DISPATCH, true, 'noroute'],
            ],
            $snapshots,
            'The flag must be set again after the noroute rewrite so the original action is never dispatched'
        );
    }

    /**
     * @return array<string, array{bool, bool}>
     */
    public static function jsonRequests(): array
    {
        return [
            'ajax get'  => [true, false],
            'form post' => [false, true],
            'ajax post' => [true, true],
        ];
    }

    #[DataProvider('jsonRequests')]
    public function testAjaxAndPostRequestsGetAJsonRefusal(bool $ajax, bool $post): void
    {
        $flag = $this->createMock(ActionFlag::class);
        $flag->expects($this->once())->method('set')->with('', Action::FLAG_NO_DISPATCH, true);
        $response = $this->createMock(HttpResponse::class);
        $response->expects($this->once())->method('clearHeader')->with('Location');
        $response->expects($this->once())->method('setHttpResponseCode')->with(200);
        $json = null;
        $response->expects($this->once())->method('representJson')->willReturnCallback(
            static function (string $body) use (&$json) {
                $json = $body;
            }
        );

        $this->subject($this->configAll(true, false, true), $flag)
            ->execute($this->observer($this->request($ajax, $post), $response));

        $this->assertSame(['success' => false, 'message' => 'Not available.'], json_decode((string) $json, true));
        $this->assertSame([], $this->routed, 'JSON refusals must not rewrite the route');
    }

    public function testNonHttpResponseStillBlocksDispatchWithoutTouchingHeaders(): void
    {
        $flag = $this->createMock(ActionFlag::class);
        $flag->expects($this->once())->method('set')->with('', Action::FLAG_NO_DISPATCH, true);

        $this->subject($this->configAll(true, false, true), $flag)
            ->execute($this->observer($this->request(true, false), $this->createStub(ResponseInterface::class)));

        $this->assertSame([], $this->routed);
    }
}
