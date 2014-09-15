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
        $simfile = $this->_simfileRepository->find(1);
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode(array('artist' => $simfile->getArtist()->getName())))
                        ->sendResponse();
    }
}
