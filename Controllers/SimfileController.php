<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\IHttpRequest;
use Services\Http\IHttpResponse;
use Services\Uploads\IUploadManager;
use DataAccess\StepMania\ISimfileRepository;

class SimfileController implements IDivineController
{
    private $_simfileRepository;
    private $_response;
    private $_request;
    private $_uploadManager;
    
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        IUploadManager $uploadManager,
        ISimfileRepository $repository
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_uploadManager = $uploadManager;
        $this->_simfileRepository = $repository;
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
        
        //TODO: Put directory in config ?
        $filenames = $this->_uploadManager->setDestination('../files/StepMania/')
                                          ->process();
        
        //parse .sm files and save to DB. should use SimfileParser service
        
        echo '<pre>';
        print_r($filenames);
        echo '</pre>';
    }
    
    public function testAction($testArg)
    {
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode(array('testArg' => $testArg)))
                        ->sendResponse();
    }
}
