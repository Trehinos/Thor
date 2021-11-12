<?php

/**
 * @package Trehinos/Thor/Validation
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Validation\Filters;

use Thor\Http\Server;
use Thor\Validation\FilterInterface;

class PostVarRegex implements FilterInterface
{

    private string $regExp;

    public function __construct(string $regExp = '.*')
    {
        $this->regExp = $regExp;
    }

    public function filter(mixed $value): array|string|null
    {
        return Server::post(
            $value,
            null,
            FILTER_VALIDATE_REGEXP,
            [
                'options' => ['regexp' => $this->regExp]
            ]
        );
    }

}
