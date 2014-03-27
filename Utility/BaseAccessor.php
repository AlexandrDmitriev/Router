<?php

namespace Router\Utility;

use Router\Exception\RouterException;

class BaseAccessor
{
    /**
     * @param $name
     *
     * @throws RouterException
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new RouterException("Can not return protected or private property: {$name}. It is not exist.");
    }

    public function __set($name, $value)
    {
        $reflectionObject = new \ReflectionObject($this);
        $setterName = 'set'.ucfirst($name);

        if ($reflectionObject->hasMethod($setterName)) {
            $method = $reflectionObject->getMethod($setterName);

            if ($method->getNumberOfRequiredParameters() > 1) {
                throw new RouterException("Setter for property: {$name} has more than one argument");
            }

            return $method->invoke($this, $value);
        }

        throw new RouterException("Has no setter protected or private property: {$name}.");
    }
}
