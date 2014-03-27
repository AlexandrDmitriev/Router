<?php

namespace Router\Services;

use Router\Entity\UrlPattern;

class ParamsService
{
    protected $serializeService;

    /**
     * @param SerializeService $serializeService
     */
    public function __construct(SerializeService $serializeService)
    {
        $this->serializeService = $serializeService;
    }

    /**
     * @param UrlPattern $pattern
     * @param array      $params
     *
     * @return string
     */
    public function compileParams(UrlPattern $pattern, array $params)
    {
        $paramsUrlParts = array();

        foreach ($params as $paramName => $paramValues) {
            if (!is_array($paramValues)) {
                $paramValues = array($paramValues);
            }
            foreach ($paramValues as $value) {
                $paramsUrlParts[] = $this->serializeService->serializeParam(
                    $pattern->paramsPattern,
                    $paramName,
                    $value
                );
            }
        }

        return implode($pattern->separator, $paramsUrlParts);
    }

    public function getPatternParams(UrlPattern $pattern, array $params)
    {
        $pattern->params;

        return array();
    }
}
