<?php

require_once('../vendor/autoload.php');

$config = require('../config/app.php');

// Allow these origins to do cross domain JS.
header("Access-Control-Allow-Origin: " . $config['allow-origin']);
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Nice exceptions
if($config['mode'] == 'production')
{
    ini_set('display_errors', 0);
    set_exception_handler(array('\Services\StatusReporter', 'exception'));
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Everything time related should be UTC+0 based
date_default_timezone_set('UTC');

// Set up the DI container
$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions('../config/DI.php');
$containerBuilder->useAutowiring(true);

$container = $containerBuilder->build();

/* @var $router Services\Routing\IRouter */
$router = $container->get('Services\Routing\IRouter');

$controllerName= $router->getControllerName();
$controllerAction = $router->getActionName();
$controllerActionArgs = $router->getActionArgs();

$controller = $container->get('Controllers\\' . ucfirst($controllerName) . 'Controller' );

// Last thing to do, call the action on the specified controller.
call_user_func_array(array($controller, $controllerAction . 'Action'), $controllerActionArgs);
