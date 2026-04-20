<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Plugin\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Panth\DisableWishlistCompare\Helper\Config;

/**
 * 404 every /wishlist/* URL when wishlist is disabled. Applied to
 * Magento\Wishlist\Controller\AbstractIndex so it covers Index, Add,
 * Remove, Update, Cart, Fromcart, Configure, UpdateItemOptions, Share,
 * Send — i.e. all wishlist action controllers, not just the landing page.
 */
class DisableWishlistController
{
    public function __construct(
        private readonly Config $config,
        private readonly RequestInterface $request,
        private readonly ResultFactory $resultFactory
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws NotFoundException
     */
    public function aroundExecute(ActionInterface $subject, callable $proceed)
    {
        if (!$this->config->isRouteBlockingEnabled() || !$this->config->isWishlistDisabled()) {
            return $proceed();
        }

        // For AJAX / POST actions (add/remove/update), returning a 404 page
        // is jarring; send an empty JSON so Hyva/Luma JS hooks fail quietly.
        if ($this->request->isAjax() || $this->request->isPost()) {
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)
                ->setData(['success' => false, 'message' => 'Not available.']);
        }

        throw new NotFoundException(__('Not Found'));
    }
}
