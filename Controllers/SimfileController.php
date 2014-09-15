<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\IHttpRequest;
use Services\Http\IHttpResponse;
use DataAccess\StepMania\ISimfileRepository;

class SimfileController implements IDivineController
{
    private $_simfileRepository;
    private $_response;
    private $_request;
    
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        ISimfileRepository $repository
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_simfileRepository = $repository;
    }
    
    public function indexAction() {
        ;
    }
    
    // list simfiles
    public function listAction()
    {
        /* @var $simfile Domain\Entities\StepMania\ISimfile */
        $simfiles = $this->_simfileRepository->findRange(1, 10);
        $returnArray = array();
        
        foreach($simfiles as $simfile)
        {
            $returnArray[$simfile->getTitle()] = array('artist' => $simfile->getArtist()->getName());
        }
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($returnArray))
                        ->sendResponse();
    }
    
    public function testAction($testArg)
    {
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode(array('testArg' => $testArg)))
                        ->sendResponse();
    }
}
