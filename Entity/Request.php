<?php

namespace Router\Entity;

use CoreInterfaces\IRequest;
use Router\Exception\RequestException;

class Request implements IRequest
{

    protected $params;

    protected $requestMethod;

    public function __construct(array $params, $requestMethod)
    {
        $this->params = $params;
        $this->requestMethod = $requestMethod;
    }

    /**
     * @param string $name
     * @param bool   $isStrict
     * @param null   $default
     *
     * @throws RequestException
     *
     * @return mixed param from request
     */
    public function get($name, $isStrict = true, $default = null)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->requestMethod[$name];
        }

        if ($isStrict) {
            throw new RequestException($name);
        } else {
            return $default;
        }
    }

    /**
     * @return array array of request params required
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string Request method
     */
    public function getMethod()
    {
        $this->requestMethod;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        // TODO: Implement isAjax() method.
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isMethod($name)
    {
        return $this->requestMethod == strtoupper($name);
    }
}
