<?php

namespace Thor\Http;

class Response
{

    private int $status;

    const STATUS_SUCCESS = 200;
    const STATUS_NOT_FOUND = 404;

    private array $headers;

    private string $body;

    public function __construct(
        string $body = '',
        int $status = self::STATUS_SUCCESS,
        array $headers = []
    )
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

}
