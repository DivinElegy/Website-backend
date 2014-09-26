<?php

namespace Services\Uploads;

use Services\Uploads\IUploadManager;
use Services\Uploads\IFileFactory;
use Services\Uploads\IFile;
use Exception;

class UploadManager implements IUploadManager{        
    
    private $_files= array();
    private $_fileFactory;
    private $_destination;
        
    public function __construct(IFileFactory $fileFactory) {
        $this->_fileFactory = $fileFactory;
        
        if($_FILES) {
            foreach($_FILES as $file)
            {
                $this->_files[] = $this->_fileFactory->createInstance(
                    $file['name'],
                    $file['type'],
                    $file['tmp_name'],
                    $file['size']
                );
            }
        }
    }
    
   public function setDestination($path) {
        if(!$this->destinationExists($path))
        {
            throw new Exception('Invalid path. Path does not exist.');
        }
        
        $this->_destination = $path;
        
        return $this;
    }
    
    private function destinationExists($path) {
        return file_exists($path);
    }
    
    private function saveFile(IFile $file)
    {
        if($this->_destination)
        {
            $randomName = $this->randomFilename();
            $result = move_uploaded_file($file->getTempName(), $this->_destination . '/' . $randomName . '.' . $file->getExtension());
        }
        
        if(!$result)
        {
            throw new Exception("Could not save file.");
        }
        
        return $randomName;
    }
    
    private function randomFilename()
    {
        return sha1(mt_rand(1, 9999) . $this->_destination . uniqid() . time());
    }
    
    public function process()
    {
        $results = array();
        
        foreach($this->_files as $file)
        {
            $results[$file->getName()] = $this->saveFile($file);
        }
        
        return $results;
    }
}
