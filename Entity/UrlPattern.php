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
 *
 * @package Router\Entity
 */
class UrlPattern extends BaseAccessor
{
    protected $urlPattern;
    protected $paramsPattern;
    protected $separator;
    protected $params;

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

    public function setParams($params)
    {
        $this->params = $params;
    }
}
