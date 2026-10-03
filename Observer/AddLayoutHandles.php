<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Panth\DisableWishlistCompare\Helper\Config;

class AddLayoutHandles implements ObserverInterface
{
    public const HANDLE_WISHLIST = 'panth_disable_wc_wishlist';
    public const HANDLE_COMPARE  = 'panth_disable_wc_compare';

    public function __construct(
        private readonly Config $config
    ) {
    }

    public function execute(Observer $observer): void
    {
        $layout = $observer->getEvent()->getData('layout');
        if (!$layout instanceof LayoutInterface) {
            return;
        }

        $update = $layout->getUpdate();
        if ($this->config->isWishlistDisabled()) {
            $update->addHandle(self::HANDLE_WISHLIST);
        }
        if ($this->config->isCompareDisabled()) {
            $update->addHandle(self::HANDLE_COMPARE);
        }
    }
}
