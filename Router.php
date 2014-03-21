<?php

namespace Router;

use CoreInterfaces\IRouter;
use Router\Entity\RoutePattern;
use Router\Exception\RouterException;

class Router implements IRouter
{
    protected $routes;

    /**
     * @param callable $patternConstructorDelegate
     *
     * @throws Exception\RouterException
     */
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
     * @param string $controller
     * @param string $action
     * @param array  $params
     *
     * @throws Exception\RouterException
     *
     * @return mixed|string
     */
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

        if ($matched === null) {
            throw new RouterException('No one route configured for this params');
        }

        return $this->compileRoute($matched);
    }

    protected function compileRoute(RoutePattern $pattern)
    {
        return preg_replace_callback(
            '/\{([^}]*)\}/',
            function ($matches) use ($pattern) {
                //todo: realise params replace, think about additional params pattern
            },
            $pattern->pattern
        );
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

    /**
     * @param RoutePattern $newRoute
     * @param RoutePattern $currentRoute
     *
     * @return RoutePattern
     */
    protected function choseMoreRelevant(RoutePattern $newRoute, RoutePattern $currentRoute)
    {
        if ($currentRoute === null) {
            return $newRoute;
        }

        return $currentRoute->relevanceIndex > $newRoute->relevanceIndex ? $currentRoute : $newRoute;
    }
}
