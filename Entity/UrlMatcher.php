<?php

namespace Router\Entity;

use Router\Utility\BaseAccessor;

/**
 * Class UrlMatcher
 *
 * @package Router\Entity
 *
 * @property string $regExp;
 * @property array  $params;
 */
class UrlMatcher extends BaseAccessor
{
    private $regExp;
    private $params;

    /**
     * @param string $regExp
     * @param array  $params
     */
    public function __construct($regExp, array $params)
    {
        $this->regExp = $regExp;
        $this->params = $params;
    }
}
