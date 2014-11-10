<?php

namespace Controllers;

use ZipArchive;
use Exception;
use Controllers\IDivineController;
use Services\Http\IHttpRequest;
use Services\Http\IHttpResponse;
use Services\Uploads\IUploadManager;
use Services\ISimfileParser;
use DataAccess\StepMania\ISimfileRepository;
use DataAccess\IUserRepository;
use Domain\Entities\StepMania\ISimfileStepByStepBuilder;

class SimfileController implements IDivineController
{
    private $_simfileRepository;
    private $_response;
    private $_request;
    private $_uploadManager;
    private $_simfileParser;
    private $_simfileBuilder;
    private $_userRepository;
    
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        IUploadManager $uploadManager,
        ISimfileRepository $repository,
        IUserRepository $userRepository,
        ISimfileParser $simfileParser,
        ISimfileStepByStepBuilder $simfileBuilder
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_uploadManager = $uploadManager;
        $this->_simfileRepository = $repository;
        $this->_userRepository = $userRepository;
        $this->_simfileParser = $simfileParser;
        $this->_simfileBuilder = $simfileBuilder;
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
        //logic for if pack or individual file
        $request = $this->_request->post();
        //XXX: Could be a place UserService could look for token ?
        $token = $request['token'];
                
        //TODO: Put directory in config ?
        $filenames = $this->_uploadManager->setDestination('../files/StepMania/')
                                          ->process();
        
        print_r($filenames);
        foreach($filenames as $filename => $hash)
        {
            $za = new ZipArchive();
            //XXX: We assume all files are zips. Should be enforced by validation elsewhere.
            $res = $za->open('../files/StepMania/' . $hash . '.zip');
            
            if($res !== true) throw new Exception ('Could not open zip for reading.');
            
            for($i=0; $i<$za->numFiles; $i++)
            {
                $stat = $za->statIndex($i);
                if(pathinfo($stat['name'], PATHINFO_EXTENSION) == 'sm')
                {
                    $smData = file_get_contents('zip://../files/StepMania/' . $hash . '.zip#' . $stat['name']);
                    break;
                }
            }
        }
        
        if(!$smData) throw new Exception('Could not extract simfile.');
        
        /* @var $parser \Services\ISimfileParser */
        $parser = $this->_simfileParser;
        $parser->parse($smData);

        //TODO: Instantiating VOs like this bad ?
        $this->_simfileBuilder->With_Title($parser->title())
                              ->With_Artist(new \Domain\VOs\StepMania\Artist($parser->artist()))
                              ->With_Uploader($this->_userRepository->findByAuthToken($token)) //obj
                              ->With_BPM($bpm) //obj
                              ->With_BpmChanges($parser->bpmChanges())
                              ->With_Stops($parser->stops())
                              ->With_FgChanges($parser->fgChanges())
                              ->With_BgChanges($parser->bgChanges())
                              ->With_Steps($steps) //obj
                              ->build();

        //parse .sm files and save to DB. should use SimfileParser service
    }
}
