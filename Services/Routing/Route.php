<?php

namespace Services\Routing;

use Services\Routing\IRoute;

class Route implements IRoute
{
    private $_controllerName;
    private $_actionName;
    private $_pattern;
    private $_methods;
    private $_argNames;
    private $_argValues;
    
    public function __construct($pattern, array $actions, $controllerName)
    {
        $this->_controllerName = $controllerName;
        $this->_actions = $actions;
        $this->_methods = array_keys($actions);
        $this->_pattern = $pattern;
    }
    
    public function matches($path) {
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
        $callback = function($m) {
            /*
             * We save away the names of the arguments in a variable so we can
             * loop through later and put them in $this->arguments.
             */
            $this->_argNames[] = $m[1]; 
            return '(?P<' . $m[1] . '>[^/]+)';
        };
        
        $patternAsRegex = preg_replace_callback('#:([\w]+)\+?#', $callback, $this->_pattern);
        if (!preg_match('#^' . $patternAsRegex . '$#', $path, $this->_argValues))
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
    
    public function getActionName($method)
    {
        return $this->_actions[$method];
    }
    
    public function getActionArgs()
    {
        if(empty($this->_argNames)) {
            return array();
        }
        
        $argValues = array();
        foreach($this->_argNames as $argName)
        {
            $argValues[] = $this->_argValues[$argName];
        }
        
        return $argValues;
    }
}

