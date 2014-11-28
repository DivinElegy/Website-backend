<?php

namespace Domain\VOs;

use Domain\VOs\IFileMirror;

class FileMirror implements IFileMirror
{
    private $_uri;
    private $_source;
    
    public function __construct($uri, $source)
    {
        $this->_uri = $uri;
        $this->_source = $source;
    }
    
    public function getUri()
    {
        return $this->_uri;
    }
    
    public function getSource()
    {
        return $this->_source;
    }
}

