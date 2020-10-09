<?php

namespace Thor\Http;

class Response
{

    private int $status;

    const STATUS_SUCCESS = 200;
    const STATUS_NOT_FOUND = 404;
    const STATUS_METHOD_NOT_ALLOWED = 405;

    private array $headers;

    private string $body;

    public function __construct(
        string $body = '',
        int $status = self::STATUS_SUCCESS,
        array $headers = []
    ) {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
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
    public function setHeader(string $name, $value)
    {
        $this->headers[$name] = $value;
    }


}
