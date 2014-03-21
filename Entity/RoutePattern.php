<?php

namespace Router\Entity;

use Router\Exception\RouterException;

/**
 * Class RoutePattern
 * @package Router\Entity
 * @property  string      $pattern
 * @property  string      $controller
 * @property  string      $action
 * @property  array|null  $params
 * @property  int         $relevanceIndex
 */
class RoutePattern
{
    private $pattern;
    private $controller;
    private $action;
    private $params;

    private $relevanceIndex = 0;

    /**
     * @param      $pattern
     * @param null $controller
     * @param null $action
     * @param null $params
     */
    public function __construct($pattern, $controller = null, $action = null, $params = null)
    {
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->action = $action;
        $this->params = $params;
    }

    /**
     * @param int $relevancyIndex
     */
    public function setRelevancyIndex($relevancyIndex)
    {
        $this->relevanceIndex = $relevancyIndex;
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \Router\Exception\RouterException
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new RouterException("Can not return protected or private property: {$name}. It is not exist.");
    }
}
