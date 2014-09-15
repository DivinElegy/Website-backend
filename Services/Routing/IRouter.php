<?php

namespace Services\Routing;

interface IRouter
{
    public function getControllerName();
    public function getActionName();
}