<?php

namespace Controllers;

use Controllers\IDivineController;
use DataAccess\StepMania\IPackRepository;
use DataAccess\StepMania\ISimfileRepository;

class PackTestController implements IDivineController
{
    private $_packRepository;
    
    public function __construct(
        IPackRepository $repository
    ) {
        $this->_packRepository = $repository;
    }
    
    public function indexAction() {
        $pack = $this->_packRepository->findById(10);

        echo '<pre>';
        print_r($pack);
        echo '</pre>';
    }
}
