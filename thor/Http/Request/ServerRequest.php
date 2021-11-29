<?php

namespace Thor\Http\Request;

use thor\Http\UriInterface;
use JetBrains\PhpStorm\Pure;
use Thor\Http\ProtocolVersion;
use Thor\Stream\StreamInterface;
use Thor\Factories\ServerRequestFactory;

/**
 * Describes an HTTP Request send to a Server.
 *
 * @see              ServerRequestFactory to create a ServerRequest from globals
 *
 * @package          Thor/Http/Request
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class ServerRequest extends Request implements ServerRequestInterface
{

    /**
     * @param ProtocolVersion         $version
     * @param array                   $headers
     * @param StreamInterface         $body
     * @param HttpMethod              $method
     * @param UriInterface            $target
     * @param array                   $attributes
     * @param array                   $cookies
     * @param array|object|null       $parsedBody
     * @param array                   $queryParams
     * @param array                   $serverParams
     * @param UploadedFileInterface[] $uploadedFiles
     */
    #[Pure]
    public function __construct(
        ProtocolVersion $version,
        array $headers,
        StreamInterface $body,
        HttpMethod $method,
        UriInterface $target,
        private array $attributes = [],
        private array $cookies = [],
        private array|object|null $parsedBody = null,
        private array $queryParams = [],
        private array $serverParams = [],
        private array $uploadedFiles = [],
    ) {
        parent::__construct($version, $headers, $body, $method, $target);
    }

    /**
     * @inheritDoc
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams(array $cookies): static
    {
        return new self(
            $this->getProtocolVersion(),
            $this->getHeaders(),
            $this->getBody(),
            $this->getMethod(),
            $this->getUri(),
            $this->attributes,
            $cookies,
            $this->parsedBody,
            $this->queryParams,
            $this->serverParams
        );
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams(array $query): static
    {
        return new self(
            $this->getProtocolVersion(),
            $this->getHeaders(),
            $this->getBody(),
            $this->getMethod(),
            $this->getUri(),
            $this->attributes,
            $this->cookies,
            $this->parsedBody,
            $query,
            $this->serverParams
        );
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles(array $uploadedFiles): static
    {
        return new self(
            $this->getProtocolVersion(),
            $this->getHeaders(),
            $this->getBody(),
            $this->getMethod(),
            $this->getUri(),
            $this->attributes,
            $this->cookies,
            $this->parsedBody,
            $this->queryParams,
            $this->serverParams
        );
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody(): null|array|object
    {
        return $this->parsedBody;
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody(object|array|null $data): static
    {
        return new self(
            $this->getProtocolVersion(),
            $this->getHeaders(),
            $this->getBody(),
            $this->getMethod(),
            $this->getUri(),
            $this->attributes,
            $this->cookies,
            $data,
            $this->queryParams,
            $this->serverParams
        );
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function withAttribute(string $name, mixed $value): static
    {
        return new self(
            $this->getProtocolVersion(),
            $this->getHeaders(),
            $this->getBody(),
            $this->getMethod(),
            $this->getUri(),
            [$name => $value] + $this->attributes,
            $this->cookies,
            $this->parsedBody,
            $this->queryParams,
            $this->serverParams
        );
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute(string $name): static
    {
        $attributes = $this->attributes;
        $attributes[$name] = null;
        unset($attributes[$name]);
        return new self(
            $this->getProtocolVersion(),
            $this->getHeaders(),
            $this->getBody(),
            $this->getMethod(),
            $this->getUri(),
            $attributes,
            $this->cookies,
            $this->parsedBody,
            $this->queryParams,
            $this->serverParams
        );
    }
}
