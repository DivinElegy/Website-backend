<?php

namespace Controllers;

use Controllers\IDivineController;
use DataAccess\IDownloadRepository;
use DataAccess\Queries\DownloadQueryConstraints;
use DateTime;

class DownloadTestController implements IDivineController
{
    private $_downloadRepository;
    
    public function __construct(
        IDownloadRepository $repository
    ) {
        $this->_downloadRepository = $repository;
    }
    
    public function indexAction() {
        $start = new DateTime('0:00 today');
        $end = new DateTime();
        
        $constraints = new DownloadQueryConstraints();
        $constraints->inDateRange($start, $end);
        $downloads = $this->_downloadRepository->findByUserId(4, $constraints);

        echo '<pre>';
        print_r($downloads);
        echo '</pre>';
    }
}
