<?php

namespace Domain\Entities\StepMania;

use Exception;
use Domain\Entities\StepMania\ISimfile;
use Domain\Entities\IUser;
use Domain\Entities\IFile;
use Domain\Entities\IPack;
use Domain\Entities\AbstractEntity;

class Pack extends AbstractEntity implements IPack
{    
    private $_title;
    private $_uploader;
    private $_simfiles;
    private $_file;
    
    public function __construct(
        $title,
        IUser $uploader,
        array $simfiles,
        IFile $file = null)
    {
        $this->_title = $title;
        $this->_uploader = $uploader;
        $this->_file = $file;
        
        foreach($simfiles as $simfile) {
            if(!$simfile instanceof ISimfile) {
                throw new Exception('Invalid Simfile array. All elements must be an instance of ISimfile.');
            }
        }
        
        $this->_simfiles = $simfiles;
    }
    
    public function getContributors() {
        $contributors = array();
        foreach($this->_simfiles as $simfile)
        {
            /* @var $simfile \Domain\Entities\StepMania\Simfile */
            $contributors = array_unique(
                array_merge($contributors, $this->getAllStepArtistsFromSimfile($simfile))
            );
        }
        
        return $contributors;
    }
    
    public function getFile()
    {
        return $this->_file;
    }
    
    public function getSimfiles()
    {
        return $this->_file;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }
    
    public function getUploader()
    {
        return $this->_uploader;
    }
    
    private function getAllStepArtistsFromSimfile(ISimfile $simfile)
    {
        $artists = array();
        foreach($simfile->getSteps() as $steps)
        {
            /* @var $steps \Domain\VOs\StepMania\IStepChart */
            if(!in_array($steps->getArtist(), $artists)) $artists[] = $steps->getArtist ();
        }
        
        return $artists;
    }
}