<?php

namespace Thor\Http\Request;

use Thor\Http\Uri;
use Thor\Stream\Stream;
use thor\Http\UriInterface;
use Thor\Http\ProtocolVersion;
use Thor\Stream\StreamInterface;

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

    public static function fromGlobals(): static
    {
        $version = explode('/', $_SERVER['SERVER_PROTOCOL'])[1] ?? '1.1';

        return new ServerRequest(
                           ProtocolVersion::from($version),
                           getallheaders(),
                           new Stream(Stream::fileOpen('php://temp', 'r+')),
                           HttpMethod::from($_SERVER['REQUEST_METHOD'] ?? 'GET'),
                           Uri::fromGlobals(),
            cookies:       $_COOKIE,
            parsedBody:    $_POST,
            queryParams:   $_GET,
            serverParams:  $_SERVER,
            uploadedFiles: UploadedFile::normalizeFiles($_FILES)
        );
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getCookieParams(): array
    {
        return $this->cookies;
    }

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

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

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

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

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

    public function getParsedBody(): null|array|object
    {
        return $this->getParsedBody();
    }

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

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

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
