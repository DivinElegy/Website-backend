<?php

namespace Domain\VOs\StepMania;

class BPM implements IBPM
{
    protected $high;
    protected $low;
    
    public function __construct($high, $low)
    {
        $this->high = $high;
        $this->low = $low;
    }
    
    public function getHigh()
    {
        return $this->high;
    }
    
    public function getLow()
    {
        return $this->low;
    }
}
