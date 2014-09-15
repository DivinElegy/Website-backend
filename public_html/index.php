<?php
require_once('../vendor/autoload.php');

// Set up the DI container
$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions('../config/DI.php');
$containerBuilder->useAutowiring(true);

$container = $containerBuilder->build();

/* @var $router Services\Routing\IRouter */
$router = $container->get('Services\Routing\IRouter');

$controllerName= $router->getControllerName();
$controllerAction = $router->getActionName();

$controller = $container->get('Controllers\\' . $controllerName . 'Controller' );
$controller->{$controllerAction . 'Action'}();
