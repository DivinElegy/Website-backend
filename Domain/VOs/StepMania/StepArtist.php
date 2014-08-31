<?php

namespace Domain\VOs\StepMania;

class StepArtist implements IStepArtist
{
    protected $tag;
    
    public function __construct($tag)
    {
        $this->tag = $tag;
    }
    
    public function getTag()
    {
        return $this->tag;
    }
}