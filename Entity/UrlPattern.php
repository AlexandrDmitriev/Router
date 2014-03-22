<?php

namespace Router\Entity;

/**
 * Class UrlPattern
 *
 * @property string $urlPattern
 * @property string $paramsPattern
 * @property string $separator params separator
 *
 * @package Router\Entity
 */
class UrlPattern
{
    protected $urlPattern;
    protected $paramsPattern;
    protected $separator;

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
}
