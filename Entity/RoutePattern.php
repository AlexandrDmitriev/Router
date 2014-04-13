<?php

namespace Router\Entity;

use Router\Utility\BaseAccessor;

/**
 * Class RoutePattern
 *
 * @package Router\Entity
 *
 * @property  UrlPattern      $pattern
 * @property  string          $controller
 * @property  string          $action
 * @property  array|null      $defaultParams
 * @property  int             $relevanceIndex
 */
class RoutePattern extends BaseAccessor
{
    private $pattern;

    private $controller;
    private $action;
    private $defaultParams;

    /**
     * @param UrlPattern $pattern
     * @param null       $controller
     * @param null       $action
     * @param array      $defaultParams
     */
    public function __construct(UrlPattern $pattern, $controller = null, $action = null, $defaultParams = array())
    {
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->action = $action;
        $this->defaultParams = $defaultParams;
    }

    /**
     * @param int $relevancyIndex
     */
    public function setRelevancyIndex($relevancyIndex)
    {
        $this->relevanceIndex = $relevancyIndex;
    }
}
