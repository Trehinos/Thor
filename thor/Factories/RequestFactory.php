<?php

namespace Thor\Factories;

use Thor\Http\Uri;
use Thor\Stream\Stream;
use Thor\Http\ProtocolVersion;
use Thor\Http\Request\Request;
use Thor\Http\Request\HttpMethod;
use Thor\Http\Request\RequestInterface;

class RequestFactory extends Factory
{

    public function __construct(private HttpMethod $method, private string $url, private string $body) {
    }


    public function produce(array $options = []): RequestInterface
    {
        return new Request(
            $options['HTTP_VERSION'] ?? ProtocolVersion::HTTP11,
            $options['HEADERS'] ?? [],
            Stream::create($this->body),
            $this->method,
            Uri::create($this->url)
        );
    }
}
