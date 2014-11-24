<?php

namespace Domain\Entities\StepMania;

use Domain\VOs\StepMania\IArtist;
use Domain\VOs\StepMania\IBPM;
use Domain\VOs\StepMania\IStepChart;
use Domain\Exception\InvalidStepChartException;
use Domain\Entities\StepMania\ISimfile;
use Domain\Entities\StepMania\IPack;
use Domain\Entities\IUser;
use Domain\Entities\IFile;
use Domain\Entities\AbstractEntity;

class Simfile extends AbstractEntity implements ISimfile
{    
    private $_title;
    private $_artist;
    private $_uploader;
    private $_bpm;
    private $_bpmChanges = false;
    private $_stops = false;
    private $_fgChanges = false;
    private $_bgChanges = false;
    private $_banner;
    private $_simfile;
    private $_steps;
    private $_packId;
    
    public function __construct(
        $title,
        IArtist $artist,
        IUser $uploader,
        IBPM $bpm,
        $bpmChanges,
        $stops,
        $fgChanges,
        $bgChanges,
        IFile $banner = null,
        IFile $simfile = null,
        $packId = null,
        array $steps
    ) {
        $this->_title = $title;
        $this->_artist = $artist;
        $this->_uploader = $uploader;
        $this->_bpm = $bpm;
        $this->_bpmChanges = $bpmChanges;
        $this->_stops = $stops;
        $this->_fgChanges = $fgChanges;
        $this->_bgChanges = $bgChanges;
        $this->_banner = $banner;
        $this->_simfile = $simfile;
        $this->_packId = $packId;

        foreach($steps as $stepChart) {
            if(!$stepChart instanceof IStepChart) {
                throw new InvalidStepChartException(sprintf('Invalid StepChart array. All array elements must be an instance of Stepchart.'));
            }
        }
        
        $this->_steps = $steps;
    }
        
    public function getTitle()
    {
        return $this->_title;
    }
        
    public function getArtist()
    {
        return $this->_artist;
    }
    
    public function getUploader()
    {
        return $this->_uploader;
    }
    
    public function getBPM()
    {
        return $this->_bpm;
    }
    
    public function hasBPMChanges()
    {
        return $this->_bpmChanges;
    }
    
    public function hasStops()
    {
        return $this->_stops;
    }
    
    public function hasFgChanges()
    {
        return $this->_fgChanges;
    }
    
    public function hasBgChanges()
    {
        return $this->_bgChanges;
    }
    
    public function addStepChart(IStepChart $stepChart) {
        $this->_steps[] = $stepChart;
    }
    
    public function getBanner()
    {
        return $this->_banner;
    }
    
    public function getSimfile()
    {
        return $this->_simfile;
    }
    
    public function getSteps()
    {
        return $this->_steps;
    }
    
    public function addToPack(IPack $pack) {
        $this->_packId = $pack->getId();
    }
    
    public function getPackId()
    {
        return $this->_packId;
    }
}
