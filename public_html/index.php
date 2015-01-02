<?php

require_once('../vendor/autoload.php');

//Timezone biz
date_default_timezone_set('UTC');

//DI biz
$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions('../config/DI.php');
$containerBuilder->useAutowiring(true);
$container = $containerBuilder->build();

$config = $container->get('Services\IConfigManager');
$router = $container->get('Services\Routing\IRouter');

//Exception biz
if($config->getDirective('mode') == 'production')
{
    ini_set('display_errors', 0);
    set_exception_handler(array('\Services\StatusReporter', 'exception'));
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}    

//CORS biz
$request_headers = apache_request_headers();
$http_origin = isset($request_headers['Origin']) ? $request_headers['Origin'] : null;

if(in_array($http_origin, $config->getDirective('allowed-origins')))
{
    header("Access-Control-Allow-Origin: " . $http_origin);
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
}

//Controller biz
$controllerName= $router->getControllerName();
$controllerAction = $router->getActionName();
$controllerActionArgs = $router->getActionArgs();
$controller = $container->get('Controllers\\' . ucfirst($controllerName) . 'Controller' );

//Last thing to do, call the action on the specified controller.
//Biz biz
call_user_func_array(array($controller, $controllerAction . 'Action'), $controllerActionArgs);
