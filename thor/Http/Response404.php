<?php

/**
 * @package Trehinos/Thor/Http
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\Http;

use JetBrains\PhpStorm\Pure;

final class Response404 extends Response
{

    #[Pure]
    public function __construct(string $body)
    {
        parent::__construct($body, Response::STATUS_NOT_FOUND);
    }

}
