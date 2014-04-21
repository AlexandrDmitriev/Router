<?php

namespace Router\Entity;

use Router\Utility\BaseAccessor;

/**
 * Class UrlMatcher
 *
 * @package Router\Entity
 *
 * @property string $regExp url matcher regexp;
 * @property array  $params array of param names;
 */
class UrlMatcher extends BaseAccessor
{
    const PARAMS_MATCHER = '/\{([^}]+)\}/';

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
