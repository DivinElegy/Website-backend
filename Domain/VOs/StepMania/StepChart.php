<?php

namespace Domain\VOs\StepMania;

use Domain\VOs\StepMania\DanceMode;
use Domain\VOs\StepMania\Difficulty;
use Domain\VOs\StepMania\StepArtist;

class StepChart implements IStepChart
{   
    protected $mode;
    
    protected $rating;
    
    protected $difficulty;
    
    protected $artist;
            
    function __construct(
        DanceMode $mode,
        Difficulty $difficulty,
        StepArtist $artist,
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