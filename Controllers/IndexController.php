<?php

namespace Controllers;

use DataAccess\StepMania\ISimfileRepository;
use Services\Http\IHttpResponse;
use Services\Http\IHttpRequest;
use Controllers\AbstractBaseController;

class IndexController extends AbstractBaseController implements IDivineController
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
        
    public function getAction() {
        /* @var $simfile Domain\Entities\StepMania\ISimfile */
//        public function getMethod();
//        public function isGet();
//        public function isPost();
//        public function isPut();
//        public function isDelete();
//        public function isHead();
//        public function isFormData();
//        public function get();
//        public function put();
//        public function post();
//        public function delete();
//        public function cookies();
//        public function getBody();
//        public function getContentType();
//        public function getHost();
//        public function getIp();
//        public function getReferrer();
//        public function getReferer();
//        public function getUserAgent();
        $r = $this->_request;
//        echo $r->getMethod();
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode(array('body' => $r->getBody())))
                        ->sendResponse();
    }
}
