<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\IHttpRequest;
use Services\Http\IHttpResponse;
use Services\IUserSession;
use Services\IStatusReporter;
use Services\IUserQuota;
use Services\IConfigManager;
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
    private $_statusReporter;
    private $_configManager;
    
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        IFileRepository $repository,
        IDownloadFactory $downloadFactory,
        IDownloadRepository $downloadRepository,
        IUserSession $userSession,
        IUserQuota $userQuota,
        IStatusReporter $statusReporter,
        IConfigManager $configManager
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_fileRepository = $repository;
        $this->_downloadRepository = $downloadRepository;
        $this->_downloadFactory = $downloadFactory;
        $this->_userSession = $userSession;
        $this->_userQuota = $userQuota;
        $this->_statusReporter = $statusReporter;
        $this->_configManager = $configManager;
    }
    
    public function indexAction() {
        ;
    }
    
    // list simfiles
    public function serveBannerAction($hash)
    {
        //TODO: This DOES NOT check that the file the request is asking for is _actually_
        //the file we are server. This is because at this stage banners cannot change, we should
        //be careful maybe.
        if($this->_request->getHeader('HTTP_IF_MODIFIED_SINCE'))
        {
            $this->_response->setHeader("HTTP/1.1 304 Not Modified", 'Nice meme!');
        } else {
            $file = $this->_fileRepository->findByHash($hash);
            if($hash == 'default') $this->serveDefaultBanner();
            if(!$file) $this->notFound();

            $matches = glob(realpath($this->_configManager->getDirective('filesPath') . '/' . $file->getPath()) . '/' . $file->getHash() . '.*');
            $match = reset($matches);
            
            $this->_response->setHeader('Content-Type', $file->getMimetype())
                            ->setHeader('Content-Length', $file->getSize())
                            ->setHeader('etag', $file->getHash())
                            ->setHeader('last-modified', gmdate("D, d M Y H:i:s", $file->getUploadDate()) . " GMT")
                            ->setHeader('cache-control', 'max-age=-1')
                            ->setBody(file_get_contents($match));
        }
         
        $this->_response->sendResponse();
    }
    
    private function serveDefaultBanner()
    {
        //XXX: As above
        if($this->_request->getHeader('HTTP_IF_MODIFIED_SINCE'))
        {
            $this->_response->setHeader("HTTP/1.1 304 Not Modified", 'Nice meme!');
        } else {
            $path = $this->_configManager->getDirective('filesPath') . '/banners/default.png';
            $file = realpath($path);
            $this->_response->setHeader('Content-Type', 'image/png')
                            ->setHeader('Content-Length', filesize($file))
                            ->setBody(file_get_contents($file))
                            ->setHeader('etag', md5_file($file))
                            ->setHeader('last-modified', gmdate("D, d M Y H:i:s", filemtime($file)) . " GMT")
                            ->setHeader('cache-control', 'max-age=-1')
                            ->sendResponse();
        }
        
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
        
        $zip = $this->_configManager->getDirective('filesPath') . '/' . $file->getPath() . '/' . $file->getHash() . '.zip';
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
                        ->setBody($this->_statusReporter->error('You must be authenticated to download files')->json())
                        ->sendResponse();
        exit();
    }
    
    private function notEnoughQuota()
    {
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody($this->_statusReporter->error('You don\'t have enough quota remaining for this file.')->json())
                        ->sendResponse();
        exit();
    }
}
