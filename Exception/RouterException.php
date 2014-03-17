<?php

namespace Router\Exception;

class RouterException extends \Exception
{
    const ERROR_CODE = 602;

    public function __construct($message)
    {
        parent::__construct("Exception in router with message: $message", RouterException::ERROR_CODE);
    }
}
