<?php

namespace Domain\Entities;

use Domain\Entities\AbstractEntity;
use Domain\VOs\IFileMirror;

class File extends AbstractEntity implements IFile
{    
    private $_hash;
    private $_path;
    private $_filename;
    private $_mimetype;
    private $_size;
    private $_uploadDate;
    private $_mirrors;
    
    public function __construct(
        $hash,
        $path,
        $filename,
        $mimetype,
        $size,
        $uploadDate,
        array $mirrors = null
    ) {
        $this->_hash = $hash;
        $this->_path = $path;
        $this->_filename = $filename;
        $this->_mimetype = $mimetype;
        $this->_size = $size;
        $this->_uploadDate = $uploadDate;
        
        if($mirrors)
        {
            foreach($mirrors as $mirror) {
                if(!$mirror instanceof IFileMirror) {
                    throw new InvalidStepChartException(sprintf('Invalid FileMirror array. All array elements must be an instance of IFileMirror.'));
                }
            }

            $this->_mirrors = $mirrors;
        }
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
    
    public function getMirrors()
    {
        return $this->_mirrors;
    }
    
    public function addMirror(IFileMirror $mirror)
    {
        $this->_mirrors[] = $mirror;
    }
}