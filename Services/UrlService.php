<?php

namespace Router\Services;

use Router\Entity\Request;
use Router\Entity\UrlMatcher;
use Router\Entity\UrlPattern;
use Router\Exception\RouterException;

class UrlService
{
    protected $serializeService;

    /**
     * @param SerializeService $serializeService
     */
    public function __construct(SerializeService $serializeService)
    {
        $this->serializeService = $serializeService;
    }

    /**
     * @param UrlPattern $pattern
     * @param array      $params
     *
     * @return string
     */
    public function compileParams(UrlPattern $pattern, array $params)
    {
        $paramsUrlParts = array();

        foreach ($params as $paramName => $paramValues) {
            if (!is_array($paramValues)) {
                $paramValues = array($paramValues);
            }
            foreach ($paramValues as $value) {
                $paramsUrlParts[] = $this->serializeService->serializeParam(
                    $pattern->paramsPattern,
                    $paramName,
                    $value
                );
            }
        }

        return implode($pattern->separator, $paramsUrlParts);
    }

    public function getPatternParams(UrlPattern $pattern)
    {
        //todo: add to cache
        if ($pattern->params === null) {
            $pattern->params = $this->calculateParams($pattern->urlPattern);
        }

        return $pattern->params;
    }

    protected function calculateParams($pattern)
    {
        $paramsList = array();
        preg_match(UrlMatcher::PARAMS_MATCHER, $pattern, $matches);
        for ($i = 0; $i < count($matches); $i++) {
            if ($i % 2 == 0) {
                continue;
            }

            $paramsList[] = $matches[$i];
        }

        return $paramsList;
    }

    /**
     * @param UrlPattern $urlPattern
     *
     * @return UrlMatcher
     */
    public function getUrlMatcher(UrlPattern $urlPattern)
    {
        if ($urlPattern->routeMatcher === null) {
            $urlPattern->routeMatcher = $this->calculateUrlMatcher($urlPattern);
        }

        return $urlPattern->routeMatcher;
    }

    //todo:replace logic into automate with magazine memory
    /**
     * @param UrlPattern $urlPattern
     *
     * @throws RouterException
     *
     * @return UrlMatcher
     */
    protected function calculateUrlMatcher(UrlPattern $urlPattern)
    {
        $params = array();

        $regExp = preg_replace_callback(
            UrlMatcher::PARAMS_MATCHER,
            function ($matches) use (&$params, $urlPattern) {
                if (count($matches) < 2 || strlen($matches[1]) == 0) {
                    throw new RouterException('Pattern is incorrect');
                }

                $params[] = $matches[1];

                return '(.*)';
            },
            $urlPattern->urlPattern
        );

        if (!$regExp) {
            throw new RouterException('Router matcher creation failed');
        }

        $regExp = '/'.addslashes($regExp).'/Um';

        return new UrlMatcher($regExp, $params);
    }

    /**
     * @param $placeholder
     *
     * @return bool
     */
    public function isParamPlaceholder($placeholder)
    {
        return $placeholder != 'action' && $placeholder != 'controller' && $placeholder != Request::PARAMS;
    }

    /**
     * @param array      $paramsPieces
     * @param UrlMatcher $urlMatcher
     *
     * @return array
     */
    public function extractRequestParams(array $paramsPieces, UrlMatcher $urlMatcher)
    {
        $result = array();

        $params = $urlMatcher->params;

        do {
            $result[current($params)] = current($paramsPieces);
        } while (next($paramsPieces) && next($params));

        return $result;
    }

    public function hasParamsPlaceholder(UrlPattern $urlPattern)
    {
        $params = $this->getPatternParams($urlPattern);
        return array_key_exists(Request::PARAMS, $params);
    }
}
