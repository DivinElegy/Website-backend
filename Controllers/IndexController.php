<?php

namespace Controllers;

use DataAccess\StepMania\ISimfileRepository;
use Services\Http\IHttpResponse;
use Services\Http\IHttpRequest;
use Controllers\AbstractBaseController;

class IndexController implements IDivineController
{
    
    private $_content;
    private $_simfileRepository;
    private $_response;
    private $_request;
    
    //override
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
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode(array('message' => 'nothing to see here')))
                        ->sendResponse();
    }
}
