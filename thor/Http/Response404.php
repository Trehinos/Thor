<?php

namespace Thor\Http;

use JetBrains\PhpStorm\Pure;

final class Response404 extends Response
{

    #[Pure] public function __construct(string $body)
    {
        parent::__construct($body, Response::STATUS_NOT_FOUND);
    }

}
