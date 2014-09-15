<?php

namespace Services\Routing;

use Services\Routing\IRoute;

class Route implements IRoute
{
    private $_controllerName;
    private $_actionName;
    private $_pattern;
    private $_methods;
    
    public function __construct($pattern, array $methods, $controllerName, $actionName = null)
    {
        $this->_controllerName = $controllerName;
        $this->_actionName = $actionName;
        $this->_pattern = $pattern;
        $this->_methods = $methods;
    }
    
    public function matches($path) {
        $argNames = array();

        /*
         * Set up a callback for preg_replace_callback. What this does is 
         * replace the :argName style arguments with named groups to match
         * against the resource URI. So for example:
         * 
         * my/:pattern/
         * 
         * Becomes:
         * 
         * my/(?P<pattern>[^/]+
         * 
         * Then we can feed the new regex and the URI in to preg_match to
         * extract the variables.
         */
        $callback = function($m) use ($argNames) {
            /*
             * We save away the names of the arguments in a variable so we can
             * loop through later and put them in $this->arguments.
             */
            $argNames[] = $m[1];            
            return '(?P<' . $m[1] . '>[^/]+)';
        };
        
        $patternAsRegex = preg_replace_callback('#:([\w]+)\+?#', $callback, $this->_pattern);
        
        if (!preg_match('#^' . $patternAsRegex . '$#', $path, $argValues)) 
            return false;

        return true;
    }
    
    public function supports($method)
    {
        return in_array($method, $this->_methods);
    }
    
    public function getControllerName()
    {
        return $this->_controllerName;
    }
    
    public function getActionName()
    {
        return $this->_actionName;
    }
}

