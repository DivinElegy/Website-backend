<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\IHttpResponse;
use Services\Http\IHttpRequest;
use Services\Uploads\IUploadManager;
use Services\Uploads\IUploadQueueManager;
use Services\IUserSession;
use Services\IZipParser;
use Services\ISMOMatcher;
use Services\IStatusReporter;
use Services\IConfigManager;
use Services\ICacheUpdater;
use DataAccess\StepMania\ISimfileRepository;
use DataAccess\StepMania\IPackRepository;
use DataAccess\IFileRepository;
use DataAccess\IDownloadRepository;
use Domain\Entities\IFile;
use Domain\Util;

class SimfileController implements IDivineController
{
    private $_simfileRepository;
    private $_packRepository;
    private $_fileRepository;
    private $_request;
    private $_response;
    private $_uploadManager;
    private $_uploadQueueManager;
    private $_cacheUpdater;
    private $_zipParser;
    private $_smoMatcher;
    private $_downloadRepository;
    private $_statusReporter;
    private $_userSession;
    private $_configManager;
    
    public function __construct(
        IHttpResponse $response,
        IHttpRequest $request,
        IUploadManager $uploadManager,
        IUploadQueueManager $uploadQueueManager,
        ICacheUpdater $cacheUpdater,
        ISimfileRepository $simfileRepository,
        IPackRepository $packRepository,
        IFileRepository $fileRepository,
        IUserSession $userSession,
        IZipParser $zipParser,
        ISMOMatcher $smoMatcher,
        IDownloadRepository $downloadRepository,
        IStatusReporter $statusReporter,
        IConfigManager $configManager
    ) {
        $this->_response = $response;
        $this->_request = $request;
        $this->_uploadManager = $uploadManager;
        $this->_uploadQueueManager = $uploadQueueManager;
        $this->_cacheUpdater = $cacheUpdater;
        $this->_simfileRepository = $simfileRepository;
        $this->_packRepository = $packRepository;
        $this->_fileRepository = $fileRepository;
        $this->_zipParser = $zipParser;
        $this->_smoMatcher = $smoMatcher;
        $this->_downloadRepository = $downloadRepository;
        $this->_statusReporter = $statusReporter;
        $this->_userSession = $userSession;
        $this->_configManager = $configManager;
    }
    
    public function indexAction() {
        ;
    }
    
    // list simfiles
    public function listAction()
    {
        $file = '../SimfileCache/simfiles.json';
        $path = realpath($file);
        
        //Always set incase list changes
        $hash = md5_file($path);
        $this->_response->setHeader('etag', $hash)
                        ->setHeader('last-modified', gmdate("D, d M Y H:i:s", filemtime($file)) . " GMT")
                        ->setHeader('cache-control', 'max-age=-1');
        
        if($this->_request->getHeader('HTTP_IF_NONE_MATCH') == $hash)
        {
            $this->_response->setHeader("HTTP/1.1 304 Not Modified", 'Nice meme!');
        } else {
            $this->_response->setHeader('Content-Type', 'application/json')
                            ->setBody(file_get_contents($path));
        }
        
        $this->_response->sendResponse();
    }
    
    public function latestSimfileAction()
    {
        $simfile = $this->_simfileRepository->findRange(0, -1);
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode(Util::simfileToArray(reset($simfile))))
                        ->sendResponse();
    }
    
    public function latestPackAction()
    {
        $pack = $this->_packRepository->findRange(0, -1);        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode(Util::packToArray(reset($pack))))
                        ->sendResponse();
    }
    
    public function popularAction()
    {
        $returnArray = array();
        $popularDownloads = $this->_downloadRepository->findPopular();
        $packOrFileId = reset($popularDownloads)->getFile()->getId();
        
        $simfile = $this->_simfileRepository->findByFileId($packOrFileId);
        if($simfile)
        {
            $returnArray = Util::simfileToArray(reset($simfile));
        } else {
            $pack = $this->_packRepository->findByFileId($packOrFileId);
            $returnArray = Util::packToArray(reset($pack));
        }
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($returnArray))
                        ->sendResponse();
    }
    
    public function uploadAction()
    {
        if(!$this->_userSession->getCurrentUser()) $this->_statusReporter->error('You must be authenticated to upload files');
        
        //TODO: Put directory in config ?
        $files = $this->_uploadManager->setFilesDirectory($this->_configManager->getDirective('filesPath'))
                                      ->setDestination('StepMania/')
                                      ->process();

        //TODO: This should be in a try-catch and if it fails the file should
        //be deleted from the filesystem and database.
        foreach($files as $file)
        {
            $this->saveToDB($file);
        }
        
        //Packs are inserted in saveToDB, only update here after they are all done.
        $this->_cacheUpdater->update();
        $this->_statusReporter->success('Uploaded succesfully');
    }
    
    public function processUploadQueueAction()
    {
        $get = $this->_request->get();
        if(!$get['cronToken']) throw new Exception('Token missing');
        
        //TODO: I should make $req->get('token') give the element and $req->get() return the array maybe?
        if($get['cronToken'] !== $this->_configManager->getDirective('cronToken')) throw new Exception ('Invalid token');
        
        $files = $this->_uploadQueueManager->setFilesDirectory($this->_configManager->getDirective('filesPath'))
                                           ->setDestination('StepMania')
                                           ->process(5); //TODO: Num to process should be config'd
        
        foreach($files as $uid => $file)
        {
            $this->_userSession->setCurrentUser($uid);
            $this->saveToDB($file);
        }
        
        //Packs are inserted in saveToDB, only update here after they are all done.
        $this->_cacheUpdater->update();
        $this->_statusReporter->success('Uploaded succesfully');
    }
            
       
    private function saveToDB(IFile $file)
    {
        $zipParser = $this->_zipParser;
        $zipParser->parse($file);

        if(!$zipParser->simfiles()) $this->_statusReporter->error('That zip doesn\'t seem to have any simfiles in it.');

        //save the actual zip in the db
        $this->findAndAddSmoMirror($file);
        $this->_fileRepository->save($file);  

        if($zipParser->isPack())
        {
            //XXX: Tricky! pack() uses packbuilder and so returns a new pack each time.
            //I tried to be clever and call pack() multiple times thinking I was getting the same
            //object. Should I cache it in zipparser?
            $pack = $zipParser->pack();
            $packBanner = $pack->getBanner() ? $this->_fileRepository->save($pack->getBanner()) : null;
            $this->_packRepository->save($pack);
            $this->_cacheUpdater->insert($pack);
        }

        foreach($zipParser->simfiles() as $simfile)
        {   
            $banner = $simfile->getBanner() ? $this->_fileRepository->save($simfile->getBanner()) : null;
            $simfileZip = $simfile->getSimfile() ? $this->_fileRepository->save($simfile->getSimfile()) : null;

            if(isset($pack)) $simfile->addToPack($pack);
            $this->_simfileRepository->save($simfile);
        }
    }
    
    private function findAndAddSmoMirror(IFile $file)
    {
        $basename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $match = $this->_smoMatcher->match($basename, $file->getSize());
        
        //XXX: Direct instantiation of FileMirror bad?
        if($match && $match['confidence'] > 90)
        {
            $file->addMirror(new \Domain\VOs\FileMirror($match['href'], 'Stepmania Online'));
        }
    }
}
