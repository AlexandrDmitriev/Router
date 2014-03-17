<?php

namespace Router;

use CoreInterfaces\IRouter;
use Router\Entity\RoutePattern;
use Router\Exception\RouterException;

class Router implements IRouter
{
    protected $routes;

    public function run(callable $patternConstructorDelegate)
    {
        $routes = call_user_func($patternConstructorDelegate);

        foreach ($routes as $route) {
            if (!$route instanceof RoutePattern) {
                throw new RouterException('All route patterns must be Router\Entity\RoutePattern instances');
            }
        }

        $this->routes = $routes;
    }

    public function getUrl($controller, $action)
    {
        // TODO: Implement getUrl() method.
    }
}
