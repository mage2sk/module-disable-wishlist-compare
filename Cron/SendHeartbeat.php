<?php
declare(strict_types=1);

namespace Panth\DisableWishlistCompare\Cron;

use Magento\Framework\Module\Manager as ModuleManager;
use Panth\DisableWishlistCompare\Service\InstallReporter;

class SendHeartbeat
{
    public function __construct(
        private readonly InstallReporter $reporter,
        private readonly ModuleManager $moduleManager
    ) {
    }

    public function execute(): void
    {
        if ($this->moduleManager->isEnabled('Panth_Core')) {
            return;
        }
        $this->reporter->reportHeartbeat();
    }
}
