<?php

namespace Domain\VOs;

use Domain\VOs\IFileMirror;

class FileMirror implements IFileMirror
{
    private $_uri;
    
    public function __construct($uri)
    {
        $this->_uri = $uri;
    }
    
    public function getUri()
    {
        return $this->_uri;
    }
}

