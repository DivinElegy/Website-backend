<?php

namespace Services\Routing;

use Services\Routing\Route;
use Services\Routing\IRouter;
use Services\Http\IHttpRequest;

class Router implements IRouter
{
    private $_maps;
    private $_routes = array();
    private $_request;
    private $_matchedRoute;
    
    public function __construct($maps, IHttpRequest $request) {
        $this->_request = $request;
        $this->_maps = include $maps;
        
        foreach($this->_maps as $pattern => $routeInfo)
        {
            $methods = isset($routeInfo['methods']) ? $routeInfo['methods'] : array('GET');
            $controller = isset($routeInfo['controller']) ? $routeInfo['controller'] : 'index';
            $action = isset($routeInfo['action']) ? $routeInfo['action'] : 'index';
            
            //TODO: really I should be using a builder or a factory with DI for this but yolo.
            $this->_routes[] = new Route($pattern, $methods, $controller, $action);
        }
    }
    
    public function getControllerName()
    {
        $matchedRoute = $this->findMatch();
        return $matchedRoute ? $matchedRoute->getControllerName() : 'index';
    }
    
    public function getActionName()
    {
        $matchedRoute = $this->findMatch();
        return $matchedRoute ? $matchedRoute->getActionName() : 'index';
    }
    
    public function getActionArgs()
    {
        $matchedRoute = $this->findMatch();
        return $matchedRoute ? $matchedRoute->getActionArgs() : array() ;
    }
    
    private function findMatch()
    {
        if($this->_matchedRoute)
        {
            return $this->_matchedRoute;
        }
        
        foreach($this->_routes as $route)
        {
            if($route->matches($this->_request->getPath()) && $route->supports($this->_request->getMethod()))
            {
                $this->_matchedRoute = $route;
                return $route;
            }
        }
    }
}
