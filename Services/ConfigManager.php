<?php

namespace Services;

use Exception;
use Services\IConfigManager;

class ConfigManager implements IConfigManager
{
    private $_config;
    
    public function __construct($config)
    {
        $this->_config = include $config;
    }
    
    public function getDirective($directive) {
        if(!array_key_exists($directive, $this->_config)) throw new Exception('Invalid directive, '. $directive);
        
        return $this->_config[$directive];
    }
}