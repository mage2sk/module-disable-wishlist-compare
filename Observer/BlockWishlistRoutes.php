<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Panth\DisableWishlistCompare\Helper\Config;

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

        $controller = $observer->getEvent()->getData('controller_action');
        if (!$controller instanceof Action) {
            return;
        }

        $request = $controller->getRequest();

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
