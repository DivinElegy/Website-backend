<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\IHttpRequest;
use Services\Http\IHttpResponse;
use DataAccess\IFileRepository;

class FileController implements IDivineController
{
    private $_fileRepository;
    private $_response;
    private $_request;
    
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        IFileRepository $repository
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_fileRepository = $repository;
    }
    
    public function indexAction() {
        ;
    }
    
    // list simfiles
    public function serveBannerAction($hash)
    {
        $file = $this->_fileRepository->findByHash($hash);
        if($hash == 'default') $this->serveDefaultBanner();
        if(!$file) $this->notFound();
                
        $match = reset(glob('../files/' . $file->getPath() . '/' . $file->getHash() . '.*'));
        
        $this->_response->setHeader('Content-Type', $file->getMimetype())
                        ->setHeader('Content-Length', $file->getSize())
                        ->setBody(file_get_contents($match))
                        ->sendResponse();
    }
    
    private function serveDefaultBanner()
    {
        $file = '../files/banners/default.png';
        $this->_response->setHeader('Content-Type', 'image/png')
                        ->setHeader('Content-Length', filesize($file))
                        ->setBody(file_get_contents($file))
                        ->sendResponse();
        exit();
    }
    
    private function notFound()
    {
        $this->_response->setHeader('HTTP/1.0 404 Not Found', 'Nothing to see here')
                        ->setBody('Move along.')
                        ->sendResponse();
        exit();
    }
}
