<?php

namespace Domain\Entities\StepMania;

use Exception;
use Domain\Entities\StepMania\ISimfile;
use Domain\Entities\IUser;
use Domain\Entities\IFile;
use Domain\Entities\StepMania\IPack;
use Domain\Entities\AbstractEntity;

class Pack extends AbstractEntity implements IPack
{    
    private $_title;
    private $_uploader;
    private $_simfiles;
    private $_banner;
    private $_file;
    
    public function __construct(
        $title,
        IUser $uploader,
        array $simfiles,
        IFile $banner = null,
        IFile $file = null
    ) {
        $this->_title = $title;
        $this->_uploader = $uploader;
        $this->_banner = $banner;
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
        
        //XXX: If there are duplicate contributors, say, index 5 and 6 are the same
        //then we loose index 5 and have an array like 1,2,3,4,6
        //
        //This makes json_encode use the indicies as object keys, so we need to
        //reshuffle the array into a continuous list
        return array_values($contributors);
    }
    
    public function getFile()
    {
        return $this->_file;
    }
    
    public function getSimfiles()
    {
        return $this->_simfiles;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }
    
    public function getUploader()
    {
        return $this->_uploader;
    }
    
    public function getBanner()
    {
        return $this->_banner;
    }
    
    private function getAllStepArtistsFromSimfile(ISimfile $simfile)
    {
        $artists = array();
        foreach($simfile->getSteps() as $steps)
        {
            /* @var $steps \Domain\VOs\StepMania\StepChart */
            if($steps->getArtist()->getTag() && !in_array($steps->getArtist()->getTag(), $artists)) $artists[] = $steps->getArtist()->getTag();
        }
        
        return $artists;
    }
}