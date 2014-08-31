<?php

namespace Domain\VOs\StepMania;

use Domain\Exception\InvalidDanceModeException;

class DanceMode implements IDanceMode
{
    protected $stepManiaName;
    protected $prettyName;
    
    private $_nameMap = array(
        'dance-single' => 'Single',
        'dance-double' => 'Double'
    );
    
    public function __construct($stepManiaName)
    {
        if(array_key_exists($stepManiaName, $this->_nameMap)) {
            $this->stepManiaName = $stepManiaName;
            $this->prettyName = $this->_nameMap[$stepManiaName];
        } else {
            throw new InvalidDanceModeException(sprintf('Invalid dance mode %s', $stepManiaName));
        }        
    }
    
    public function getStepManiaName() {
        return $this->stepManiaName;
    }
    
    public function getPrettyName() {
        return $this->prettyName;
    }
}
