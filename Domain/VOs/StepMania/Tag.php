<?php

namespace Domain\VOs\StepMania;

use Domain\VOs\StepMania\ITag;

class Tag implements ITag
{
    private $_tag;
    
    public function __construct($tag) {
        $this->_tag = $tag;
    }
    
    public function getTag()
    {
        return $this->_tag;
    }
}

