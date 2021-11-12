<?php

namespace Thor\Http;

use JetBrains\PhpStorm\Pure;
use Thor\Stream\StreamInterface;

class Message implements MessageInterface
{

    /**
     * @param ProtocolVersion $version
     * @param array           $headers
     * @param StreamInterface $body
     */
    public function __construct
    (
        private ProtocolVersion $version,
        private array $headers,
        private StreamInterface $body
    ) {
    }

    public function getProtocolVersion(): ProtocolVersion
    {
        return $this->version;
    }

    #[Pure]
    public function withProtocolVersion(ProtocolVersion $version): static
    {
        return new self($version, $this->headers, $this->body);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    #[Pure]
    public function getHeaderLine(string $name): string
    {
        return empty($header = $this->getHeader($name)) ? '' : $header[strtolower($name)];
    }

    #[Pure]
    public function getHeader(string $name): array
    {
        if ($this->hasHeader($name)) {
            return [strtolower($name) => $this->headers[strtolower($name)]];
        }
        return [];
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    #[Pure]
    public function withHeader(string $name, array|string $value): static
    {
        return new self($this->version, [$name => $value] + $this->headers, $this->body);
    }

    #[Pure]
    public function withAddedHeader(string $name, array|string $value): static
    {
        $n = new self($this->version, $this->headers, $this->body);
        if ($n->hasHeader($name)) {
            if (!is_array($n->headers[strtolower($name)])) {
                $n->headers[$name] = [$n->headers[$name], $value];
            } else {
                $n->headers[$name][] = $value;
            }
        } else {
            $n->headers[$name] = [$value];
        }
        return $n;
    }

    public function withoutHeader(string $name): static
    {
        $n = new self($this->version, $this->headers, $this->body);
        if ($n->hasHeader($name)) {
            unset($n->headers[strtolower($name)]);
        }
        return $n;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    #[Pure]
    public function withBody(StreamInterface $body): static
    {
        return new self($this->version, $this->headers, $body);
    }
}