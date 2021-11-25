<?php

/**
 * @package          Trehinos/Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Http\Controllers;

use Thor\Http\UriInterface;
use JetBrains\PhpStorm\Pure;
use Thor\Http\Server\HttpServer;
use Thor\Http\Response\Response;
use Thor\Http\Request\HttpMethod;
use Thor\Factories\ResponseFactory;
use Thor\Http\Response\ResponseInterface;
use Thor\Http\Request\UploadedFileInterface;
use Thor\Http\Request\ServerRequestInterface;

abstract class HttpController
{

    public HttpMethod $method;
    public UriInterface $uri;
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private array $cookies;
    private array $headers;
    private array $queryAttributes;

    public function __construct(protected HttpServer $httpServer)
    {
        $this->method = $this->getRequest()->getMethod();
        $this->uri = $this->getRequest()->getUri();
        $this->get = $this->getRequest()->getQueryParams();
        $this->post = $this->getRequest()->getParsedBody();
        $this->server = $this->getRequest()->getServerParams();
        $this->files = $this->getRequest()->getUploadedFiles();
        $this->cookies = $this->getRequest()->getCookieParams();
        $this->headers = $this->getRequest()->getHeaders();
        $this->queryAttributes = $this->getRequest()->getAttributes();
    }

    #[Pure]
    public function getRequest(): ServerRequestInterface
    {
        return $this->httpServer->getRequest();
    }

    public function attribute(string $name, mixed $default = null): mixed
    {
        return $this->queryAttributes[$name] ?? $default;
    }

    public function get(string $name, array|string|null $default = null): array|string|null
    {
        return $this->get[$name] ?? $default;
    }

    public function post(string $name, array|string|null $default = null): array|string|null
    {
        return $this->post[$name] ?? $default;
    }

    public function server(string $name, array|string|null $default = null): array|string|null
    {
        return $this->server[$name] ?? $default;
    }

    public function cookie(string $name, array|string|null $default = null): array|string|null
    {
        return $this->cookies[$name] ?? $default;
    }

    public function header(string $name, array|string|null $default = null): array|string|null
    {
        return $this->headers[$name] ?? $default;
    }

    public function file(string $name): ?UploadedFileInterface
    {
        return $this->files[$name] ?? null;
    }

    public function redirect(string $routeName, array $params = [], array $query = []): ResponseInterface
    {
        return $this->getServer()->redirect($routeName, $params, $query);
    }

    public function getServer(): HttpServer
    {
        return $this->httpServer;
    }

}
