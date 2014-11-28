<?php

namespace Domain\Entities;

use Domain\Entities\IDownload;
use Domain\Entities\IUser;
use Domain\Entities\IFile;

class Download extends AbstractEntity implements IDownload
{
    private $_user;
    private $_file;
    private $_timestamp;
    private $_ip;
    
    public function __construct(
        IUser $user,
        IFile $file,
        $timestamp,
        $ip
    ) {
        $this->_user = $user;
        $this->_file = $file;
        $this->_timestamp = $timestamp;
        $this->_ip = $ip;
    }
    
    public function getFile()
    {
        return $this->_file;
    }
    
    public function getIp()
    {
        return $this->_ip;
    }
    
    public function getTimestamp()
    {
        return $this->_timestamp;
    }
    
    public function getUser()
    {
        return $this->_user;
    }
}

