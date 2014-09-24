<?php

namespace DataAccess;

use DataAccess\IDatabaseFactory;

use PDO;

class DatabaseFactory implements IDatabaseFactory {
    
    private $_username;
    private $_password;
    private $_dsn;
    
    public function __construct($dbCredentials)
    {
        $credentials = include $dbCredentials;
        //TODO: should probably do all this through a configuration object or something
        $this->_dsn = $credentials['dsn'];
        $this->_username = $credentials['user'];
        $this->_password = $credentials['pass'];
    }
    
    public function createInstance()
    {
        $options = array(PDO::ATTR_EMULATE_PREPARES => false,
                         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        return new PDO($this->_dsn, $this->_username, $this->_password, $options);
    }
}
