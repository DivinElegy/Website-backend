<?php

namespace Services\Uploads;

use Services\Uploads\IUploadQueueManager;
use Services\Uploads\IFileFactory;
use Services\Uploads\IFile;
//This was a bit silly, I have a File object to use with the service but there is also a File entity.
//It's confusing but if you pay attention it should be OK.
use Domain\Entities\IFileStepByStepBuilder;
use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use finfo;

class UploadQueueManager implements IUploadQueueManager{        
    
    private $_files= array();
    private $_fileFactory;
    private $_basePath;
    private $_destination;
    private $_fileBuilder;
        
    public function __construct(IFileFactory $fileFactory, IFileStepByStepBuilder $builder) {
        $this->_fileFactory = $fileFactory;
        $this->_fileBuilder = $builder;
        
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('../Users', RecursiveIteratorIterator::SELF_FIRST));
        foreach($files as $file)
        {
            
            //$parts = explode('/', $file->getPath());
            $dir = basename(dirname(($file->getPath())));
            if($file->getExtension() == 'zip' && $dir == 'Users')
            {   
                $finfo = new finfo(FILEINFO_MIME);
                $mimetype = $finfo->file($file->getPath() . '/' . $file->getFileName());
                //basename($file->getPath()) = the user's foldername, which is their ID
                $this->_files[basename($file->getPath())] = $this->_fileFactory->createInstance(
                    $file->getFilename(),
                    $mimetype,
                    $file->getPath() . '/' . $file->getFilename(),
                    $file->getSize()
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
            $randomName = md5_file($file->getTempName());
            //$result = move_uploaded_file($file->getTempName(), $this->_basePath . '/' . $this->_destination . '/' . $randomName . '.' . $file->getExtension());
            $result = rename($file->getTempName(), $this->_basePath . '/' . $this->_destination . '/' . $randomName . '.' . $file->getExtension());
        }
        
        if(!$result)
        {
            throw new Exception("Could not save file.");
        }
        
        return $randomName;
    }
    
    public function process($num)
    {
        $results = array();
        $i = 0;
        foreach($this->_files as $uid => $file)
        {
            $hash = $this->saveFile($file);
            
            /* @var $file \Services\Uploads\IFile */
            $results[$uid] = $this->_fileBuilder->With_Hash($hash)
                                            ->With_Path(rtrim($this->_destination, '/'))
                                            ->With_Filename($file->getName())
                                            ->With_Mimetype($file->getType())
                                            ->With_Size($file->getSize())
                                            ->With_UploadDate(time())
                                            ->build();
            $i++;
            if($i == $num) break;
        }
        
        return $results;
    }
}
