<?php

namespace Router\Exception;

class RequestException extends \Exception
{
    const ERROR_CODE = 603;

    public function __construct($param)
    {
        parent::__construct("Request has no param with name: $param", RequestException::ERROR_CODE);
    }
}
