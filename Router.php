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
        $matched = null;

        foreach ($this->routes as $route) {
            if (!$this->routeService->isRouteMatch($route, $controller, $action, $params)) {
                continue;
            }

            $matched = $this->routeService->choseMoreRelevant($matched, $route);
        }

        if ($matched === null) {
            throw new RouterException('No one route configured for this params');
        }

        return $this->routeService->compileRoute($matched, $controller, $action, $params);
    }
}
