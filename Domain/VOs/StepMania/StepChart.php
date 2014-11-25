<?php

namespace Domain\VOs\StepMania;

use Domain\VOs\StepMania\IDanceMode;
use Domain\VOs\StepMania\IDifficulty;
use Domain\VOs\StepMania\IStepArtist;

class StepChart implements IStepChart
{   
    protected $mode;
    
    protected $rating;
    
    protected $difficulty;
    
    protected $artist;
            
    function __construct(
        IDanceMode $mode,
        IDifficulty $difficulty,
        IStepArtist $artist = null,
        $rating
    ) {
        $this->mode = $mode;
        $this->difficulty = $difficulty;
        $this->artist = $artist;
        $this->rating = $rating;
    }
    
    public function getMode()
    {
        return $this->mode;
    }
    
    public function getRating()
    {
        return $this->rating;
    }
    
    public function getDifficulty()
    {
        return $this->difficulty;
    }
    
    public function getArtist()
    {
        return $this->artist;
    }
}