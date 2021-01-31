<?php

namespace Thor\Http;

class Response
{

    public const STATUS_SUCCESS = 200;
    public const STATUS_REDIRECT = 302;
    public const STATUS_FORBIDDEN = 403;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_METHOD_NOT_ALLOWED = 405;

    public function __construct(
        private string $body = '',
        private  int $status = self::STATUS_SUCCESS,
        private array $headers = []
    ) {
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @param array|string $value
     */
    public function setHeader(string $name, array|string $value)
    {
        $this->headers[$name] = $value;
    }


}
