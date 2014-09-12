<?php

namespace Services\Routing;

interface IRoute
{
    public function matches($uri);
    public function supports($method);
    public function execute();
}
