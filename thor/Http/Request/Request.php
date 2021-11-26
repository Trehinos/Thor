<?php

/**
 * @package          Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Http\Request;

use Thor\Http\Uri;
use Thor\Http\Message;
use Thor\Stream\Stream;
use thor\Http\UriInterface;
use JetBrains\PhpStorm\Pure;
use Thor\Http\ProtocolVersion;
use Thor\Stream\StreamInterface;

class Request extends Message implements RequestInterface
{

    #[Pure]
    public function __construct(
        ProtocolVersion $version,
        array $headers,
        StreamInterface $body,
        private HttpMethod $method,
        private UriInterface $target,
    ) {
        parent::__construct($version, $headers, $body);
    }

    public static function create(
        HttpMethod $method,
        UriInterface $target,
        string $data = '',
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): self {
        return new self($version, $headers, Stream::create($data), $method, $target);
    }

    public function getRequestTarget(): string
    {
        return (string)$this->target;
    }

    public function withRequestTarget(string $requestTarget): static
    {
        return new self(
            $this->getProtocolVersion(),
            $this->getHeaders(),
            $this->getBody(),
            $this->getMethod(),
            Uri::create($requestTarget)
        );
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function withMethod(HttpMethod $method): static
    {
        return new self($this->getProtocolVersion(), $this->getHeaders(), $this->getBody(), $method, $this->getUri());
    }

    public function getUri(): UriInterface
    {
        return $this->target;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        return new self(
            $this->getProtocolVersion(),
            $this->getHeaders() + ($preserveHost ? [] : ['Host' => $uri->getHost()]),
            $this->getBody(),
            $this->getMethod(),
            $uri
        );
    }
}
