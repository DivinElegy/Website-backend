<?php

namespace Controllers;

use Exception;
use Controllers\IDivineController;
use Services\Http\IHttpResponse;
use Services\Uploads\IUploadManager;
use Services\IUserSession;
use Services\IZipParser;
use Services\ISMOMatcher;
use Services\IStatusReporter;
use DataAccess\StepMania\ISimfileRepository;
use DataAccess\StepMania\IPackRepository;
use DataAccess\IFileRepository;
use DataAccess\IDownloadRepository;
use Domain\Entities\StepMania\ISimfile;
use Domain\Entities\IFile;
use Domain\Entities\StepMania\IPack;
use Domain\Util;

class SimfileController implements IDivineController
{
    private $_simfileRepository;
    private $_packRepository;
    private $_fileRepository;
    private $_response;
    private $_uploadManager;
    private $_zipParser;
    private $_smoMatcher;
    private $_downloadRepository;
    private $_statusReporter;
    private $_userSession;
    
    public function __construct(
        IHttpResponse $response,
        IUploadManager $uploadManager,
        ISimfileRepository $simfileRepository,
        IPackRepository $packRepository,
        IFileRepository $fileRepository,
        IUserSession $userSession,
        IZipParser $zipParser,
        ISMOMatcher $smoMatcher,
        IDownloadRepository $downloadRepository,
        IStatusReporter $statusReporter
    ) {
        $this->_response = $response;
        $this->_uploadManager = $uploadManager;
        $this->_simfileRepository = $simfileRepository;
        $this->_packRepository = $packRepository;
        $this->_fileRepository = $fileRepository;
        $this->_zipParser = $zipParser;
        $this->_smoMatcher = $smoMatcher;
        $this->_downloadRepository = $downloadRepository;
        $this->_statusReporter = $statusReporter;
        $this->_userSession = $userSession;
    }
    
    public function indexAction() {
        ;
    }
    
    // list simfiles
    public function listAction()
    {
        /* @var $simfile Domain\Entities\StepMania\ISimfile */
        $simfiles = $this->_simfileRepository->findAll();
        $packs = $this->_packRepository->findAll();
        $simfileArray = array();
        $packArray = array();
        
        foreach($simfiles as $simfile)
        {
            $simfileArray[] = $this->simfileToArray($simfile);
        }
        
        foreach($packs as $pack)
        {
            $packArray[] = array(
                'title'=> $pack->getTitle(),
                'contributors' => $pack->getContributors(),
                'simfiles' => $this->getPackSimfilesArray($pack),
                'banner' => $pack->getBanner() ? 'files/banner/' . $pack->getBanner()->getHash() : 'files/banner/default',
                'mirrors' => $this->getPackMirrorsArray($pack),
                'size' => $pack->getFile() ? Util::bytesToHumanReadable($pack->getFile()->getSize()) : null,
                'uploaded' => $pack->getFile() ? date('F jS, Y', $pack->getFile()->getUploadDate()) : null
            );
        }
        
        $returnArray = array('simfiles' => $simfileArray, 'packs' => $packArray);
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($returnArray))
                        ->sendResponse();
    }
    
    public function latestSimfileAction()
    {
        $simfile = $this->_simfileRepository->findRange(0, -1);
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($this->simfileToArray(reset($simfile))))
                        ->sendResponse();
    }
    
    public function latestPackAction()
    {
        $pack = $this->_packRepository->findRange(0, -1);
        $pack = reset($pack);
        
        $packArray = array(
            'title'=> $pack->getTitle(),
            'contributors' => $pack->getContributors(),
            'simfiles' => $this->getPackSimfilesArray($pack),
            'banner' => $pack->getBanner() ? 'files/banner/' . $pack->getBanner()->getHash() : 'files/banner/default',
            'mirrors' => $this->getPackMirrorsArray($pack),
            'size' => $pack->getFile() ? Util::bytesToHumanReadable($pack->getFile()->getSize()) : null,
            'uploaded' => $pack->getFile() ? date('F jS, Y', $pack->getFile()->getUploadDate()) : null
        );
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($packArray))
                        ->sendResponse();
    }
    
    public function popularAction()
    {
        $returnArray = array();
        $popularDownloads = $this->_downloadRepository->findPopular();
        $popularDownloads = reset($popularDownloads);
        $packOrFileId = $popularDownloads->getFile()->getId();
        
        $simfile = $this->_simfileRepository->findByFileId($packOrFileId);
        if($simfile)
        {
            $returnArray = $this->simfileToArray(reset($simfile));
        } else {
            $pack = $this->_packRepository->findByFileId($packOrFileId);
            $pack = reset($pack);
            $returnArray = array(
                'title'=> $pack->getTitle(),
                'contributors' => $pack->getContributors(),
                'simfiles' => $this->getPackSimfilesArray($pack),
                'banner' => $pack->getBanner() ? 'files/banner/' . $pack->getBanner()->getHash() : 'files/banner/default',
                'mirrors' => $this->getPackMirrorsArray($pack),
                'size' => $pack->getFile() ? Util::bytesToHumanReadable($pack->getFile()->getSize()) : null,
                'uploaded' => $pack->getFile() ? date('F jS, Y', $pack->getFile()->getUploadDate()) : null
            );
        }
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($returnArray))
                        ->sendResponse();
        
    }
    
    public function uploadAction()
    {
        if(!$this->_userSession->getCurrentUser()) $this->_statusReporter->error('You must be authenticated to upload files');
        
        //TODO: Put directory in config ?
        $files = $this->_uploadManager->setFilesDirectory('../files')
                                      ->setDestination('StepMania/')
                                      ->process();

        foreach($files as $file)
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
            }
            
            foreach($zipParser->simfiles() as $simfile)
            {   
                $banner = $simfile->getBanner() ? $this->_fileRepository->save($simfile->getBanner()) : null;
                $simfileZip = $simfile->getSimfile() ? $this->_fileRepository->save($simfile->getSimfile()) : null;

                if(isset($pack)) $simfile->addToPack($pack);
                $this->_simfileRepository->save($simfile);
            }
        }
        
        $this->_statusReporter->success('Uploaded succesfully');
    }
    
    private function getPackMirrorsArray(IPack $pack)
    {
        $packMirrors = array();

        if($pack->getFile())
        {
            $packMirrors[] = array('source' => 'DivinElegy', 'uri' => 'files/pack/' . $pack->getFile()->getHash());
        }

        if($pack->getFile()->getMirrors())
        {
            foreach($pack->getFile()->getMirrors() as $mirror)
            {
                $packMirrors[] = array('source' => $mirror->getSource(), 'uri' => $mirror->getUri());
            }
        }
        
        return $packMirrors;
    }
    
    private function getPackSimfilesArray(IPack $pack)
    {
        $packSimfiles = array();
        foreach($pack->getSimfiles() as $simfile)
        {
            $packSimfiles[] = $this->simfileToArray($simfile);
        }
        
        return $packSimfiles;
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
    
    private function simfileToArray(ISimfile $simfile)
    {
        $singleSteps = array();
        $doubleSteps = array();

        foreach($simfile->getSteps() as $steps)
        {   
            $stepDetails = array(
                'artist' => $steps->getArtist()->getTag(),
                'difficulty' => $steps->getDifficulty()->getITGName(),
                'rating' => $steps->getRating()
            );

            if($steps->getMode()->getPrettyName() == 'Single')
            {
                $singleSteps[] = $stepDetails;
            } else {
                $doubleSteps[] = $stepDetails;
            }
        }

        return array(            
            'title' => $simfile->getTitle(),
            'artist' => $simfile->getArtist()->getName(),
            'steps' => array(
                'single' => $singleSteps,
                'double' => $doubleSteps
            ),
            'bgChanges' => $simfile->hasBgChanges() ? 'Yes' : 'No',
            'fgChanges' => $simfile->hasFgChanges() ? 'Yes' : 'No',
            'bpmChanges' => $simfile->hasBPMChanges() ? 'Yes' : 'No',
            'banner' => $simfile->getBanner() ? 'files/banner/' . $simfile->getBanner()->getHash() : 'files/banner/default',
            'download' => $simfile->getSimfile() ?  'files/simfile/' . $simfile->getSimfile()->getHash() : null,
            'size' => $simfile->getSimfile() ? Util::bytesToHumanReadable($simfile->getSimfile()->getSize()) : null,
            'uploaded' => $simfile->getSimfile() ? date('F jS, Y', $simfile->getSimfile()->getUploadDate()) : null
        );
    }
}
