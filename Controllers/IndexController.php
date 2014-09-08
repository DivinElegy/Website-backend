<?php

namespace Controllers;

use DataAccess\StepMania\ISimfileRepository;
use Services\IHttpResponse;

class IndexController implements IDivineController
{
    
    private $_content;
    private $_simfileRepository;
    private $_jsonResponse;
    private $_response;
    
    //override
    public function __construct(
        IHttpResponse $response,
        ISimfileRepository $repository
    ) {
        $this->_response = $response;
        $this->_simfileRepository = $repository;
    }
    
    public function setJsonResponse() {
        $this->_jsonResponse = true;
    }
        
    public function getAction() {
        /* @var $simfile Domain\Entities\StepMania\ISimfile */
        $simfile = $this->_simfileRepository->find(1);
        $modes = array();
        /* @var $steps Domain\VOs\StepMania\IStepChart */
        foreach ($simfile->getSteps() as $steps) {
            $modes[] = $steps->getArtist()->getTag();
        }
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($modes))
                        ->sendResponse();
    }
}
