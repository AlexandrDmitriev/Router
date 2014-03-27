<?php

namespace Router\Services;

class SerializeService
{

    public function serializeParam($pattern, $name, $value)
    {
        if (is_array($value) || is_object($value)) {
            $value = $this->serialiseParam($value);
        } elseif (is_bool($value)) {
            $value = (int)$value;
        }

        return sprintf($pattern, $name, $value);
    }


    protected function serialiseParam($value)
    {
        //todo check if object and not serializable throw an exception

        //todo: in future add object state validation
        return serialize($value);
    }

    public function unserializeParam($value)
    {
        //todo add callback to validate instances (if class unknown unserialise it like std)

        //todo: in future add object state validation
        return unserialize($value);
    }
}
