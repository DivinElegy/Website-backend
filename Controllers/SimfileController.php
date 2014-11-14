<?php

namespace Controllers;

use ZipArchive;
use Exception;
use Controllers\IDivineController;
use Services\Http\IHttpResponse;
use Services\Uploads\IUploadManager;
use Services\ISimfileParser;
use Services\IUserSession;
use Services\IBannerExtracter;
use DataAccess\StepMania\ISimfileRepository;
use Domain\Entities\StepMania\ISimfileStepByStepBuilder;

class SimfileController implements IDivineController
{
    private $_simfileRepository;
    private $_response;
    private $_uploadManager;
    private $_simfileParser;
    private $_simfileBuilder;
    private $_userRepository;
    private $_userSession;
    private $_bannerExtracter;
    
    public function __construct(
        IHttpResponse $response,
        IUploadManager $uploadManager,
        ISimfileRepository $repository,
        IUserSession $userSession,
        ISimfileParser $simfileParser,
        ISimfileStepByStepBuilder $simfileBuilder,
        IBannerExtracter $bannerExtracter
    ) {
        $this->_response = $response;
        $this->_uploadManager = $uploadManager;
        $this->_simfileRepository = $repository;
        $this->_userSession = $userSession;
        $this->_simfileParser = $simfileParser;
        $this->_simfileBuilder = $simfileBuilder;
        $this->_bannerExtracter = $bannerExtracter;
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

            $returnArray[] = array(
                'title' => $simfile->getTitle(),
                'artist' => $simfile->getArtist()->getName(),
                'steps' => array(
                    'single' => $singleSteps,
                    'double' => $doubleSteps
                ),
                'bgChanges' => $simfile->hasBgChanges() ? 'Yes' : 'No',
                'fgChanges' => $simfile->hasFgChanges() ? 'Yes' : 'No',
                'bpmChanges' => $simfile->hasBPMChanges() ? 'Yes' : 'No',
                'banner' => $simfile->getBanner() ? 'files/banner/' . $simfile->getBanner()->getHash() : 'files/banner/default'
            );
        }
        
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
            $za = new ZipArchive();
            //XXX: We assume all files are zips. Should be enforced by validation elsewhere.
            $res = $za->open('../files/StepMania/' . $file->getHash() . '.zip');
            
            if($res !== true) throw new Exception ('Could not open zip for reading.');
            
            for($i=0; $i<$za->numFiles; $i++)
            {
                $stat = $za->statIndex($i);
                if(pathinfo($stat['name'], PATHINFO_EXTENSION) == 'sm')
                {
                    $smData = file_get_contents('zip://../files/StepMania/' . $file->getHash() . '.zip#' . $stat['name']);
                    break;
                }
            }

            if(!$smData) throw new Exception('Could not extract simfile.');

            /* @var $parser \Services\ISimfileParser */
            $parser = $this->_simfileParser;
            $parser->parse($smData);

            $banner = $this->_bannerExtracter->extractBanner('../files/StepMania/' . $file->getHash() . '.zip', $parser->banner());
                        
            //TODO: Create file object for banner and .zip then link them up
            //shouldn't need to use repository as the mapper can create the db entries
            //all in one go (I think ...)
            //
            //Need to make FileBuilder and FileStepByStepBuilder
            $simfile = $this->_simfileBuilder->With_Title($parser->title())
                                             ->With_Artist($parser->artist())
                                             ->With_Uploader($this->_userSession->getCurrentUser()) //obj
                                             ->With_BPM($parser->bpm())
                                             ->With_BpmChanges($parser->bpmChanges())
                                             ->With_Stops($parser->stops())
                                             ->With_FgChanges($parser->fgChanges())
                                             ->With_BgChanges($parser->bgChanges())
                                             ->With_Steps($parser->steps())
                                             ->With_Simfile($file)
                                             ->With_Banner($banner)
                                             ->build();
            
            $this->_simfileRepository->save($simfile);
        }
    }
}
