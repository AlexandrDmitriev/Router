<?php

namespace Router\Services;

use Router\Entity\RoutePattern;
use Router\Entity\UrlPattern;

class RouteService
{
    /**
     * @param RoutePattern $pattern
     * @param string       $controller
     * @param string       $action
     * @param array        $params
     *
     * @return mixed
     */
    public function compileRoute(RoutePattern $pattern, $controller, $action, array $params)
    {
        $preparedUrl = preg_replace_callback(
            '/\{([^}]*)\}/',
            function ($matches) use ($pattern, $controller, $action, &$params) {
                switch ($matches[1]) {
                    case 'controller':
                        return $controller;
                    case 'action':
                        return $action;
                    case 'params':
                        $replacement = $this->compileParams($pattern->pattern, $params);
                        $params = array();
                        return $replacement;
                    default:
                        if (array_key_exists($matches[1], $params)) {
                            $replacement = $params[$matches[1]];
                            unset($params[$matches[1]]);
                            return $replacement;
                        } else {
                            return $matches[0];
                        }
                }
            },
            $pattern->pattern->urlPattern
        );

        return $preparedUrl;
    }

    /**
     * @param UrlPattern $pattern
     * @param array      $params
     *
     * @return string
     */
    protected function compileParams(UrlPattern $pattern, array $params)
    {
        $paramsUrlParts = array();

        foreach ($params as $paramName => $paramValue) {
            $paramsUrlParts[] = sprintf($pattern->paramsPattern, $paramName, $paramValue);
        }

        return implode($pattern->separator, $paramsUrlParts);
    }

    /**
     * @param RoutePattern $routePattern
     * @param string       $controller
     * @param string       $action
     * @param array        $params
     *
     * @return bool
     */
    public function isRouteMatch(RoutePattern $routePattern, $controller, $action, array $params)
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
    public function choseMoreRelevant(RoutePattern $newRoute, RoutePattern $currentRoute)
    {
        if ($currentRoute === null) {
            return $newRoute;
        }

        return $currentRoute->relevanceIndex > $newRoute->relevanceIndex ? $currentRoute : $newRoute;
    }
}
