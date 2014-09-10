<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\HttpRequest;
use Exception;

abstract class AbstractBaseController
{
    protected $_jsonResponse;
    
    //TODO: Not really used as this application probably won't have views.
    //But hey, the intended usage is when you want a controller to not render
    //a view. So it's there if I ever use this for anything else.
    public function setJsonResponse($bool) {
        if(!is_bool($bool)) {
            throw new Exception('Not a boolean value.');
        }
        
        $this->_jasonResponse = $bool;
    }
}
