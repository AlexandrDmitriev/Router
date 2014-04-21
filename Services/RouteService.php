<?php

namespace Router\Services;

use Router\Entity\Request;
use Router\Entity\RoutePattern;
use Router\Entity\UrlMatcher;
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
            UrlMatcher::PARAMS_MATCHER,
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
        if ($routePattern->controller && $routePattern->controller != $controller
            || $routePattern->action && $routePattern->action != $action
        ) {
            return false;
        }

        $patternParams = array();

        foreach ($this->urlService->getPatternParams($routePattern->pattern) as $param) {
            if ($this->urlService->isParamPlaceholder($param)) {
                $patternParams[$param] = true;
            }
        }

        if (count(array_diff_key($patternParams, $params)) > 0) {
            foreach (array_diff_key($params, $patternParams) as $paramName => $paramValue) {
                if ((!array_key_exists($paramName, $routePattern->defaultParams)
                        || $routePattern->defaultParams[$paramName] != $paramValue)
                    && !$this->urlService->hasParamsPlaceholder($routePattern->pattern)
                ) {
                    return false;
                }
            }
        }



        return true;
    }

    public function isRouteMatchUri(RoutePattern $routePattern, $uri, array &$params)
    {
        $matcher = $this->urlService->getUrlMatcher($routePattern->pattern);
        //todo: replace into automate logic
        $isMatch = preg_match($matcher->regExp, $uri, $matches) > 0 && $matches[0] == $uri;

        if ($isMatch) {
            array_shift($matches);
            $requestParams = $this->urlService->extractRequestParams($matches, $matcher);

            if ($routePattern->controller) {
                $params[Request::CONTROLLER] = $routePattern->controller;
            } elseif (!empty($requestParams[Request::CONTROLLER])) {
                $params[Request::CONTROLLER] = $requestParams[Request::CONTROLLER];
                unset($requestParams[Request::CONTROLLER]);
            } else {
                throw new RouterException('Parsing error unrecognized controller');
            }

            if ($routePattern->action) {
                $params[Request::ACTION]  = $routePattern->action;
            } elseif (!empty($requestParams[Request::ACTION])) {
                $params[Request::ACTION] = $requestParams[Request::ACTION];
                unset($requestParams[Request::ACTION]);
            } else {
                throw new RouterException('Parsing error unrecognized action');
            }

            $extractedParams = array();
            if (array_key_exists(Request::PARAMS, $params)) {
                $paramsPieces = explode($routePattern->pattern->separator, $params[Request::PARAMS]);
                $namePosition = strpos($routePattern->pattern->paramsPattern, '{name}');
                $valuePosition = strpos($routePattern->pattern->paramsPattern, '{value}');
                if ($namePosition == -1 || $valuePosition == -1) {
                    throw new RouterException('Invalid params pattern');
                }
                $orderedParams = array(
                    $namePosition  => 'name',
                    $valuePosition => 'value'
                );
                $regexp = str_replace(
                    '{value}',
                    '(.*)',
                    str_replace('{name}', '(.*)', $routePattern->pattern->paramsPattern)
                );
                $matcher = new UrlMatcher($regexp, $orderedParams);
                foreach ($paramsPieces as $piece) {
                    //todo: move to one regexp
                    if (!preg_match($matcher->regExp, $piece, $matches) > 0 || $matches[0] != $piece) {
                        continue;
                    }

                    $nameValueArray = $this->urlService->extractRequestParams($piece, $matcher);
                    $extractedParams[$nameValueArray['name']] = $nameValueArray['value'];
                }
            }

            $params['params'] = $extractedParams;
        }

        return $isMatch;
    }
}
