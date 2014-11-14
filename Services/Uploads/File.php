<?php

namespace Services\Uploads;

use Services\Uploads\IFile;

class File implements IFile {
    
    private $_size;
    private $_name;
    private $_tempName;
    private $_type;
    
    public function __construct($name, $type, $tempName, $size) {
        $this->_name = $name;
        $this->_type = $type;
        $this->_tempName = $tempName;
        $this->_size = $size;
    }
            
    public function getExtension()
    {
        return pathinfo($this->_name, PATHINFO_EXTENSION);
    }
    
    public function getTempName()
    {
        return $this->_tempName;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function getSize()
    {
        return $this->_size;
    }
}
