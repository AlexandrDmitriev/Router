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
        if (!$routes) {
            throw new RouterException('No one route detected');
        }
        foreach ($routes as $route) {
            if (!$route instanceof RoutePattern) {
                throw new RouterException('All route patterns must be Router\Entity\RoutePattern instances');
            }
        }

        $this->routes = $routes;
    }

    /**
     * @param Entity\RoutePattern $routePattern
     * @param string              $controller
     * @param string              $action
     * @param array               $params
     *
     * @return bool
     */
    protected function isRouteMatch(RoutePattern $routePattern, $controller, $action, array $params)
    {
        if ($routePattern->controller && $routePattern->controller != $controller) {
            return false;
        }

        if ($routePattern->action && $routePattern->action != $action) {
            return false;
        }

        if ($routePattern->params && $routePattern->params != $params) {
            return false;
        }

        return true;
    }

    protected function choseMoreRelevant($newRoute, $currentRoute)
    {
        if ($currentRoute === null) {
            return $newRoute;
        }
//todo: add relevant compare
        return $currentRoute;
    }

    public function getUrl($controller, $action, $params = array())
    {
        $url = null;

        $matched = null;

        foreach ($this->routes as $route) {
            if (!$this->isRouteMatch($route, $controller, $action, $params)) {
                continue;
            }

            $matched = $this->choseMoreRelevant($matched, $route);
        }

        if (count($matched) == 0) {
            throw new RouterException('No one route configured for this params');
        }

        return $url;
    }
}
