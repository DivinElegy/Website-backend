<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\IHttpResponse;
use Services\Uploads\IUploadManager;
use Services\IUserSession;
use Services\IZipParser;
use Services\ISMOMatcher;
use DataAccess\StepMania\ISimfileRepository;
use DataAccess\StepMania\IPackRepository;
use DataAccess\IFileRepository;
use Domain\Entities\StepMania\ISimfile;
use Domain\Entities\IFile;
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
    
    public function __construct(
        IHttpResponse $response,
        IUploadManager $uploadManager,
        ISimfileRepository $simfileRepository,
        IPackRepository $packRepository,
        IFileRepository $fileRepository,
        IUserSession $userSession,
        IZipParser $zipParser,
        ISMOMatcher $smoMatcher
    ) {
        $this->_response = $response;
        $this->_uploadManager = $uploadManager;
        $this->_simfileRepository = $simfileRepository;
        $this->_packRepository = $packRepository;
        $this->_fileRepository = $fileRepository;
        $this->_zipParser = $zipParser;
        $this->_smoMatcher = $smoMatcher;
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
            $packSimfiles = array();
            foreach($pack->getSimfiles() as $simfile)
            {
                $packSimfiles[] = $this->simfileToArray($simfile);
            }

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
            
            $packArray[] = array(
                'title'=> $pack->getTitle(),
                'contributors' => $pack->getContributors(),
                'simfiles' => $packSimfiles,
                'banner' => $pack->getBanner() ? 'files/banner/' . $pack->getBanner()->getHash() : 'files/banner/default',
                'mirrors' => $packMirrors,
                'size' => $pack->getFile() ? Util::bytesToHumanReadable($pack->getFile()->getSize()) : null
            );
        }
        
        $returnArray = array('simfiles' => $simfileArray, 'packs' => $packArray);
        
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($returnArray))
                        ->sendResponse();
    }
    
    public function uploadAction()
    {                       
        //TODO: Put directory in config ?
        $files = $this->_uploadManager->setFilesDirectory('../files')
                                      ->setDestination('StepMania/')
                                      ->process();

        foreach($files as $file)
        {
            $zipParser = $this->_zipParser;
            $zipParser->parse($file);
                        
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
            'size' => $simfile->getSimfile() ? Util::bytesToHumanReadable($simfile->getSimfile()->getSize()) : null
        );
    }
}
