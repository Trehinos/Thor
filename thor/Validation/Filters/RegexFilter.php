<?php

namespace Thor\Validation\Filters;

use Thor\Validation\FilterInterface;

/**
 * Filters strings corresponding a regular expression.
 *
 * @package          Thor/Validation/Filters
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class RegexFilter implements FilterInterface
{

    private string $regExp;

    public function __construct(string $regExp = '.*')
    {
        $this->regExp = $regExp;
    }

    /**
     * @inheritDoc
     */
    public function filter(mixed $value): array|string|null
    {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $this->regExp]]);
    }

}
