<?php

/**
 * @package Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Http\Response;

use JetBrains\PhpStorm\Pure;
use Thor\Http\Response\Response;
use Thor\Http\Response\HttpStatus;

final class Response404 extends Response
{

    #[Pure]
    public function __construct(string $body)
    {
        parent::__construct($body, HttpStatus::NOT_FOUND);
    }

}
