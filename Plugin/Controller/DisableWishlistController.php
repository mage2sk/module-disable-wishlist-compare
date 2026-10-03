<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Panth\DisableWishlistCompare\Helper\Config;

class DisableWishlistController
{
    public function __construct(
        private readonly Config $config,
        private readonly RequestInterface $request,
        private readonly ResultFactory $resultFactory
    ) {
    }

    public function aroundExecute(ActionInterface $subject, callable $proceed)
    {
        if (!$this->config->isRouteBlockingEnabled() || !$this->config->isWishlistDisabled()) {
            return $proceed();
        }

        if ($this->request->isAjax() || $this->request->isPost()) {
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)
                ->setData(['success' => false, 'message' => 'Not available.']);
        }

        throw new NotFoundException(__('Not Found'));
    }
}
