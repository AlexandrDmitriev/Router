<?php

namespace Router;

use CoreInterfaces\IRouter;
use Router\Entity\RoutePattern;
use Router\Exception\RouterException;
use Router\Services\RouteService;

class Router implements IRouter
{
    protected $routes;

    /**
     * @var RouteService
     */
    protected $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    /**
     * @param callable $patternConstructorDelegate
     *
     * @throws RouterException
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
     * @throws RouterException
     *
     * @return mixed|string
     */
    public function getUrl($controller, $action, $params = array())
    {
        foreach ($this->routes as $route) {
            if ($this->routeService->isRouteMatchParams($route, $controller, $action, $params)) {
                return $this->routeService->compileRoute($route, $controller, $action, $params);
            }
        }

        throw new RouterException('No one route configured for this params');
    }

    /**
     * @return array Array with three keys controller, action, params required
     */
    public function getRouteParams()
    {
        $requestUri = $_SERVER['REQUEST_URI'];

        foreach ($this->routes as $route) {
            if ($this->routeService->isRouteMatchUri($route, $requestUri, $params)) {
                return $params;
            }
        }

        throw new RouterException('No one route found');
    }
}
