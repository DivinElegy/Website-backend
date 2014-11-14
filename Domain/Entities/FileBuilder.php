<?php

namespace Domain\Entities;

use Domain\Entities\IFileFactory;

class FileBuilder implements IFileBuilder
{
    private $_fileFactory;
    private $_hash;
    private $_path;
    private $_filename;
    private $_mimetype;
    private $_size;
    private $_date;
    
    public function __construct(IFileFactory $fileFactory)
    {
        $this->_fileFactory = $fileFactory;
    }
    
    public function With_Filename($filename)
    {
        $this->_filename = $filename;
    }
    
    public function With_Hash($hash)
    {
        $this->_hash = $hash;
    }
    
    public function With_Mimetype($mimetype)
    {
        $this->_mimetype = $mimetype;
    }
    
    public function With_Path($path)
    {
        $this->_path = $path;
    }
    
    public function With_Size($size)
    {
        $this->_size = $size;
    }
    
    public function With_UploadDate($date)
    {
        $this->_date = $date;
    }
    
    public function build()
    {
        return $this->_fileFactory
                    ->createInstance(
                            $this->_hash,
                            $this->_path,
                            $this->_filename,
                            $this->_mimetype,
                            $this->_size,
                            $this->_date);
    }
}