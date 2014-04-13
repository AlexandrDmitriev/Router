<?php

namespace Router\Services;

use Router\Entity\RoutePattern;
use Router\Exception\RouterException;

class RouteService
{
    /**
     * @var UrlService
     */
    protected $urlService;

    /**
     * @param UrlService $paramsService
     */
    public function __construct(UrlService $paramsService)
    {
        $this->urlService = $paramsService;
    }

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
            '/\{([^}]+)\}/',
            function ($matches) use ($pattern, $controller, $action, &$params) {
                switch ($matches[1]) {
                    case 'controller':
                        return $controller;
                    case 'action':
                        return $action;
                    case 'params':
                        $replacement = $this->urlService->compileParams($pattern->pattern, $params);
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
     * @param RoutePattern $routePattern
     * @param string       $controller
     * @param string       $action
     * @param array        $params
     *
     * @return bool
     */
    public function isRouteMatchParams(RoutePattern $routePattern, $controller, $action, array $params)
    {
        if ($routePattern->controller && $routePattern->controller != $controller) {
            return false;
        }

        if ($routePattern->action && $routePattern->action != $action) {
            return false;
        }

        $patternParams = $this->urlService->getPatternParams($routePattern->pattern);

        if (count(array_diff_key($patternParams, $params)) > 0) {
            return false;
        }

        foreach (array_diff_key($params, $patternParams) as $paramName => $paramValue) {
            if (!$routePattern->defaultParams[$paramName] || $routePattern->defaultParams[$paramName] != $paramValue) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function isRouteMatchUri(RoutePattern $routePattern, $uri, array &$params)
    {
        $matcher = $this->urlService->getUrlMatcher($routePattern->pattern);

        $isMatch = preg_match($matcher->regExp, $uri, $matches) > 0 && $matches[0] == $uri;

        if ($isMatch) {
            array_shift($matches);
            $requestParams = $this->urlService->extractRequestParams($matches, $matcher);

            if ($routePattern->controller) {
                $params['controller'] = $routePattern->controller;
            } elseif (!empty($requestParams['controller'])) {
                $params['controller'] = $requestParams['controller'];
                unset($requestParams['controller']);
            } else {
                throw new RouterException('Parsing error unrecognized controller');
            }

            if ($routePattern->action) {
                $params['action']  = $routePattern->action;
            } elseif (!empty($requestParams['action'])) {
                $params['action'] = $requestParams['action'];
                unset($requestParams['action']);
            } else {
                throw new RouterException('Parsing error unrecognized action');
            }


        }

        return $isMatch;
    }
}
