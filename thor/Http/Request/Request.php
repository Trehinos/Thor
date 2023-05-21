<?php

namespace Thor\Http\Request;

use Thor\Http\Uri;
use Thor\Http\Message;
use thor\Http\UriInterface;
use JetBrains\PhpStorm\Pure;
use Thor\Http\ProtocolVersion;
use Thor\FileSystem\Stream\Stream;
use Thor\FileSystem\Stream\StreamInterface;


/**
 * Describes a base HTTP Request.
 *
 * This class is not attended to be instantiated directly in any project.
 *
 * @package          Thor/Http/Request
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Request extends Message implements RequestInterface
{

    /**
     * @param ProtocolVersion                         $version
     * @param array                                   $headers
     * @param \Thor\FileSystem\Stream\StreamInterface $body
     * @param HttpMethod                              $method
     * @param UriInterface                            $target
     */
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

    /**
     * Creates a new Request with some parameters as optionals.
     *
     * @param HttpMethod      $method
     * @param UriInterface    $target
     * @param string          $data
     * @param array           $headers
     * @param ProtocolVersion $version
     *
     * @return static
     */
    public static function create(
        HttpMethod $method,
        UriInterface $target,
        string $data = '',
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): self {
        return new self($version, $headers, Stream::create($data), $method, $target);
    }

    /**
     * Gets the string corresponding the current request.
     *
     * @return string
     */
    public function getRaw(): string
    {
        $requestStr =
            "{$this->getMethod()->value} {$this->getUri()} HTTP/{$this->getProtocolVersion()->value}\r\n";

        foreach ($this->getHeaders() as $name => $value) {
            $requestStr .= "$name: " . (is_array($value) ?  implode(', ', $value) : $value) . "\r\n";
        }

        $requestStr .= "\r\n" . $this->getBody()->getContents();

        return $requestStr;
    }

    /**
     * @inheritDoc
     */
    public function getRequestTarget(): string
    {
        return (string)$this->target;
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod(HttpMethod $method): static
    {
        return new self($this->getProtocolVersion(), $this->getHeaders(), $this->getBody(), $method, $this->getUri());
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->target;
    }

    /**
     * @inheritDoc
     */
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
