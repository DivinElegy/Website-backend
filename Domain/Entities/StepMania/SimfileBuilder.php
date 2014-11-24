<?php

namespace Domain\Entities\StepMania;

use Domain\VOs\StepMania\IArtist;
use Domain\VOs\StepMania\IBPM;
use Domain\Entities\IUser;
use Domain\Entities\IFile;
use Domain\Entities\StepMania\ISimfileFactory;
use Domain\Entities\StepMania\ISimfileBuilder;

class SimfileBuilder implements ISimfileBuilder  
{    
    private $_simfileFactory;
    private $_title;
    private $_artist;
    private $_uploader;
    private $_bpm;
    private $_bpmChanges;
    private $_stops;
    private $_fgChanges;
    private $_bgChanges;
    private $_banner;
    private $_simfile;
    private $_packId;
    private $_steps;
    
    //override parent
    public function __construct(ISimfileFactory $simfileFactory) {
        $this->_simfileFactory = $simfileFactory;
    }
    
    public function With_Title($title)
    {
        $this->_title = $title;
        return $this;
    }
    
    public function With_Artist(IArtist $artist) {
        $this->_artist = $artist;
        return $this;
    }
    
    public function With_Uploader(IUser $uploader) {
        $this->_uploader = $uploader;
        return $this;
    }
    
    public function With_BPM(IBPM $bpm) {
        $this->_bpm = $bpm;
        return $this;
    }
    
    public function With_BpmChanges($const) {
        $this->_bpmChanges = $const;
        return $this;
    }
    
    public function With_Stops($const) {
        $this->_stops = $const;
        return $this;
    }
    
    public function With_FgChanges($const) {
        $this->_fgChanges = $const;
        return $this;
    }
    
    public function With_BgChanges($const) {
        $this->_bgChanges = $const;
        return $this;
    }
    
    public function With_Banner(IFile $banner = null) {
        $this->_banner = $banner;
        return $this;
    }
    
    public function With_Simfile(IFile $simfile = null) {
        $this->_simfile = $simfile;
        return $this;
    }
    
    public function With_PackId($packId = null)
    {
        $this->_packId = $packId;
        return $this;
    }
    
    public function With_Steps(array $steps) {
        $this->_steps = $steps;
        return $this;
    }
    
    public function build() {
        return $this->_simfileFactory
                    ->createInstance($this->_title,
                                     $this->_artist,
                                     $this->_uploader,
                                     $this->_bpm,
                                     $this->_bpmChanges,
                                     $this->_stops,
                                     $this->_fgChanges,
                                     $this->_bgChanges,
                                     $this->_banner,
                                     $this->_simfile,
                                     $this->_packId,
                                     $this->_steps);
    }
}

