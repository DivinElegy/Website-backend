<?php

namespace Controllers;

use Controllers\IDivineController;

abstract class AbstractController implements IDivineController
{
    protected $_isJsonResponse;
    protected $_response;
    
    
    public function __construct
    
    public function setJsonResponse()
    {
        $this->_isJsonResponse = true;
    }
}