<?php

namespace Domain\VOs\StepMania;

class Artist implements IArtist
{
    private $_name;
    
    public function __construct($name)
    {
        $this->_name = $name;        
    }
    
    public function getName() {
        return $this->_name;
    }
}