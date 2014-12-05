<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\IHttpRequest;
use Services\Http\IHttpResponse;
use Services\IUserSession;
use Services\IUserQuota;
use Domain\Entities\IDownloadFactory;
use DataAccess\IFileRepository;
use DataAccess\IDownloadRepository;

class FileController implements IDivineController
{
    private $_fileRepository;
    private $_response;
    private $_request;
    private $_downloadRepository;
    private $_downloadFactory;
    private $_userSession;
    private $_userQuota;
    
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        IFileRepository $repository,
        IDownloadFactory $downloadFactory,
        IDownloadRepository $downloadRepository,
        IUserSession $userSession,
        IUserQuota $userQuota
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_fileRepository = $repository;
        $this->_downloadRepository = $downloadRepository;
        $this->_downloadFactory = $downloadFactory;
        $this->_userSession = $userSession;
        $this->_userQuota = $userQuota;
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
    
    public function serveSimfileOrPackAction($hash)
    {
        $file = $this->_fileRepository->findByHash($hash);
        $quotaRemaining = $this->_userQuota->getCurrentUserQuotaRemaining();

        if(!$file) $this->notFound();
        if(!$quotaRemaining) $this->notAuthorised();
        if($quotaRemaining < $file->getSize()) $this->notEnoughQuota();
        
        // TODO: Builder?
        $download = $this->_downloadFactory->createInstance($this->_userSession->getCurrentUser(),
                                                            $file,
                                                            time(),
                                                            $this->_request->getIp());
        
        $this->_downloadRepository->save($download);
        
        $zip = '../files/' . $file->getPath() . '/' . $file->getHash() . '.zip';
        //TODO: This may not work on all browser or something. We'll have to see. Also it may hog ram so...
        $this->_response->setHeader('Content-Type', $file->getMimetype())
                        ->setHeader('Content-Length', $file->getSize())
                        ->setHeader('Content-Disposition', 'filename="' . $file->getFileName() . '";')
                        ->setHeader('Content-Transfer-Encoding', 'binary')
                        ->setBody(file_get_contents($zip))
                        ->sendResponse();
    }
        
    private function notFound()
    {
        $this->_response->setHeader('HTTP/1.0 404 Not Found', 'Nothing to see here')
                        ->setBody('Move along.')
                        ->sendResponse();
        exit();
    }
    
    private function notAuthorised()
    {
        $this->_response->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode(array('error' => 'You must be authenticated to download files')))
            ->sendResponse();
        exit();
    }
    
    private function notEnoughQuota()
    {
        $this->_response->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode(array('error' => 'You don\'t have enough quota remaining for this file.')))
            ->sendResponse();
        exit();
    }
}
