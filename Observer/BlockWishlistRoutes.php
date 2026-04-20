<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Panth\DisableWishlistCompare\Helper\Config;

/**
 * Fires on `controller_action_predispatch_wishlist` (every wishlist/* URL).
 *
 * The controller plugin on AbstractIndex::execute() runs too late: Magento's
 * customer-session auth check in AbstractIndex::dispatch() already redirects
 * unauthenticated visitors to the login page with a 302, so /wishlist/*
 * never reaches execute(). Predispatch fires first — we forward to
 * noroute, letting Magento serve the canonical 404 page.
 */
class BlockWishlistRoutes implements ObserverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly ActionFlag $actionFlag,
        private readonly ForwardFactory $forwardFactory
    ) {
    }

    public function execute(Observer $observer): void
    {
        if (!$this->config->isRouteBlockingEnabled() || !$this->config->isWishlistDisabled()) {
            return;
        }

        /** @var Action|null $controller */
        $controller = $observer->getEvent()->getData('controller_action');
        if (!$controller instanceof Action) {
            return;
        }

        $request = $controller->getRequest();
        // Leave AJAX hooks alone — the controller plugin handles them and
        // returns an empty JSON so any stale Alpine/JS listeners fail quietly.
        if ($request->isAjax() || $request->isPost()) {
            return;
        }

        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        $request->setDispatched(false)
            ->setModuleName('noroute')
            ->setControllerName('index')
            ->setActionName('index');
    }
}
