<?php

namespace Thor\Http;

final class Response404 extends Response
{

    public function __construct(string $body)
    {
        parent::__construct($body, Response::STATUS_NOT_FOUND);
    }

}
