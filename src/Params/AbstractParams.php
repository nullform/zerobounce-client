<?php

namespace Nullform\ZeroBounce\Params;

use Nullform\ZeroBounce\Exceptions\ParamsException;

/**
 * Base class for parameters.
 *
 * @package Nullform\ZeroBounce
 */
abstract class AbstractParams
{
    /**
     * Parameters as query string.
     *
     * @return string
     * @uses http_build_query()
     */
    public function toString(): string
    {
        return http_build_query($this);
    }

    /**
     * Parameters as associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array)$this;
    }

    /**
     * Check if required parameters are passed.
     *
     * @param array $params Required parameters.
     * @throws ParamsException
     */
    public function checkRequiredParams(array $params): void
    {
        foreach ($params as $param) {
            if (is_null($this->{$param})) {
                throw new ParamsException('Empty required parameter: ' . $param);
            }
        }
    }
}