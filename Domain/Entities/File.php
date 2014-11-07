<?php

namespace Domain\Entities;

use Domain\Entities\AbstractEntity;

class File extends AbstractEntity implements IFile
{    
    private $_hash;
    private $_path;
    private $_filename;
    private $_mimetype;
    private $_size;
    private $_uploadDate;
    
    public function __construct(
        $hash,
        $path,
        $filename,
        $mimetype,
        $size,
        $uploadDate
    ) {
        $this->_hash = $hash;
        $this->_path = $path;
        $this->_filename = $filename;
        $this->_mimetype = $mimetype;
        $this->_size = $size;
        $this->_uploadDate = $uploadDate;
    }
    
    public function getFilename() 
    {
        return $this->_filename;
    }
    
    public function getHash() 
    {
        return $this->_hash;
    }
    
    public function getPath()
    {
        return $this->_path;
    }
    
    public function getMimetype()
    {
        return $this->_mimetype;
    }
    
    public function getSize() 
    {
        return $this->_size;
    }
    
    public function getUploadDate()
    {
        return $this->_uploadDate;
    }
}