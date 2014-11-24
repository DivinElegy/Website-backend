<?php

namespace Domain\Entities\StepMania;

use Domain\Entities\IUser;
use Domain\Entities\IFile;
use Domain\Entities\StepMania\IPackFactory;
use Domain\Entities\StepMania\IPackBuilder;

class PackBuilder implements IPackBuilder
{    
    
    private $_packFactory;
    private $_title;
    private $_uploader;
    private $_simfiles;
    private $_banner;
    private $_file;
    
    //override parent
    public function __construct(IPackFactory $packFactory)
    {
        $this->_packFactory = $packFactory;
    }
    
    public function With_Title($title)
    {
        $this->_title = $title;
        return $this;
    }
    
    public function With_Banner(IFile $banner)
    {
        $this->_banner = $banner;
    }
    
    public function With_File(IFile $file)
    {
        $this->_file = $file;
        return $this;
    }
    
    public function With_Simfiles(array $simfiles)
    {
        $this->_simfiles = $simfiles;
        return $this;
    }
    
    public function With_Uploader(IUser $uploader)
    {
        $this->_uploader = $uploader;
        return $this;
    }
    
    public function build()
    {
        return $this->_packFactory->createInstance($this->_title,
                                                   $this->_uploader,
                                                   $this->_simfiles,
                                                   $this->_banner,
                                                   $this->_file);
    }
}