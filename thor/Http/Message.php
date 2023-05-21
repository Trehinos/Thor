<?php

namespace Thor\Http;

use JetBrains\PhpStorm\Pure;
use Thor\FileSystem\Stream\StreamInterface;

/**
 * Default base implementation of MessageInterface.
 *
 * @see MessageInterface
 *
 * @package          Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Message implements MessageInterface
{

    /**
     * @param ProtocolVersion                         $version
     * @param array                                   $headers
     * @param \Thor\FileSystem\Stream\StreamInterface $body
     */
    public function __construct
    (
        private ProtocolVersion $version,
        private array $headers,
        private StreamInterface $body
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): ProtocolVersion
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withProtocolVersion(ProtocolVersion $version): static
    {
        return new self($version, $this->headers, $this->body);
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getHeaderLine(string $name): string
    {
        return empty($header = $this->getHeader($name)) ? '' : $header[strtolower($name)];
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getHeader(string $name): array
    {
        if ($this->hasHeader($name)) {
            return [strtolower($name) => $this->headers[strtolower($name)]];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function hasHeader(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withHeader(string $name, array|string $value): static
    {
        return new self($this->version, [$name => $value] + $this->headers, $this->body);
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function withoutHeader(string $name): static
    {
        $n = new self($this->version, $this->headers, $this->body);
        if ($n->hasHeader($name)) {
            unset($n->headers[strtolower($name)]);
        }
        return $n;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withBody(StreamInterface $body): static
    {
        return new self($this->version, $this->headers, $body);
    }
}
