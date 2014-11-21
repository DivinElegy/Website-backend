<?php

namespace Services\Uploads;

use Services\Uploads\IUploadManager;
use Services\Uploads\IFileFactory;
use Services\Uploads\IFile;
//This was a bit silly, I have a File object to use with the service but there is also a File entity.
//It's confusing but if you pay attention it should be OK.
use Domain\Entities\IFileStepByStepBuilder;
use DataAccess\IFileRepository;
use Exception;

class UploadManager implements IUploadManager{        
    
    private $_files= array();
    private $_fileFactory;
    private $_basePath;
    private $_destination;
    private $_fileBuilder;
    private $_fileRepository;
        
    public function __construct(IFileFactory $fileFactory, IFileStepByStepBuilder $builder, IFileRepository $fileRepository) {
        $this->_fileFactory = $fileFactory;
        $this->_fileBuilder = $builder;
        $this->_fileRepository = $fileRepository;
        
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
    
    public function setFilesDirectory($path) {
        if(!$this->destinationExists($path))
        {
            throw new Exception('Invalid path. Path does not exist.');
        }
        
        $this->_basePath = $path;
        
        return $this;
    }
    
    public function setDestination($path) {
        if(!$this->destinationExists($this->_basePath . '/' . $path))
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
            $result = move_uploaded_file($file->getTempName(), $this->_basePath . '/' . $this->_destination . '/' . $randomName . '.' . $file->getExtension());
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
            $hash = $this->saveFile($file);
            
            /* @var $file \Services\Uploads\IFile */
            $results[] = $this->_fileBuilder->With_Hash($hash)
                                       ->With_Path(rtrim($this->_destination, '/'))
                                       ->With_Filename($file->getName())
                                       ->With_Mimetype($file->getType())
                                       ->With_Size($file->getSize())
                                       ->With_UploadDate(time())
                                       ->build();
        }
        
        return $results;
    }
}
