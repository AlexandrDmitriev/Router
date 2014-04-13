<?php

namespace Router\Entity;

use Router\Utility\BaseAccessor;

/**
 * Class UrlPattern
 *
 * @property string $urlPattern
 * @property string $paramsPattern
 * @property string $separator params separator
 * @property array $params params in pattern
 * @property UrlMatcher $routeMatcher Is Uri match route Regexp and expected params list
 *
 * @package Router\Entity
 */
class UrlPattern extends BaseAccessor
{
    protected $urlPattern;
    protected $paramsPattern;
    protected $separator;
    protected $params;
    protected $routeMatcher;

    /**
     * @param string $urlPattern
     * @param string $paramsPattern
     * @param string $separator
     */
    public function __construct($urlPattern, $paramsPattern, $separator = '&')
    {
        $this->urlPattern = $urlPattern;
        $this->paramsPattern = $paramsPattern;
        $this->separator = $separator;
    }

    /**
     * @param UrlMatcher $routeMatcher
     */
    public function setRouteMatcher(UrlMatcher $routeMatcher)
    {
        $this->routeMatcher = $routeMatcher;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
}
