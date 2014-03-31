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

    public function getPatternParams(UrlPattern $pattern)
    {
        //todo: add to cache
        if ($pattern->params === null) {
            $pattern->params = $this->calculateParams($pattern->urlPattern);
        }

        return $pattern->params;
    }

    protected function calculateParams($pattern)
    {
        $paramsList = array();
        preg_match('/\{([^}]+)\}/', $pattern, $matches);
        for ($i = 0; $i < count($matches); $i++) {
            if ($i % 2 == 0 || !$this->isParamPlaceholder($matches[$i])) {
                continue;
            }

            $paramsList[$matches[$i]] = true;
        }

        return $paramsList;
    }

    protected function isParamPlaceholder($placeholder)
    {
        return $placeholder != 'action' && $placeholder != 'controller' && $placeholder != 'params';
    }
}
