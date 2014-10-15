<?php

namespace Services;

use Services\IFacebookSessionFactory;
use Facebook\FacebookSession;

class FacebookSessionFactory implements IFacebookSessionFactory {
    private $_appId;
    private $_appSecret;
    
    public function __construct($appConfig) {
        $config = include $appConfig;

        $this->_appId = $config['appId'];
        $this->_appSecret = $config['appSecret'];
    }
    
    public function createInstance($token)
    {
        FacebookSession::setDefaultApplication($this->_appId, $this->_appSecret);
        return new FacebookSession($token);
    }
}
