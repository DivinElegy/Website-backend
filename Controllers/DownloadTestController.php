<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\IUserQuota;

class DownloadTestController implements IDivineController
{
    private $_quotaManager;
    
    public function __construct(
        IUserQuota $quotaManager
    ) {
        $this->_quotaManager = $quotaManager;
    }
    
    public function indexAction() {
        $quota = (($this->_quotaManager->getCurrentUserQuotaRemaining())/1000)/1000;
        echo $quota;
    }
}
