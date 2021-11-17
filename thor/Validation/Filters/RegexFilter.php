<?php

/**
 * @package Trehinos/Thor/Validation
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Validation\Filters;

use Thor\Validation\FilterInterface;

class RegexFilter implements FilterInterface
{

    private string $regExp;

    public function __construct(string $regExp = '.*')
    {
        $this->regExp = $regExp;
    }

    public function filter(mixed $value): array|string|null
    {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['regexp' => $this->regExp]);
    }

}
