<?php

namespace Thor\Http\Controllers;

use JetBrains\PhpStorm\Pure;
use Thor\Http\{UriInterface,
    Server\HttpServer,
    Request\HttpMethod,
    Response\ResponseInterface,
    Request\UploadedFileInterface,
    Request\ServerRequestInterface
};

/**
 * This abstract class is a base class for every controller of an HttpServer.
 *
 * @package          Thor/Http/Controllers
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
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

    /**
     * Gets the request send by the client.
     *
     * @return ServerRequestInterface
     */
    #[Pure]
    public function getRequest(): ServerRequestInterface
    {
        return $this->httpServer->getRequest();
    }

    /**
     * Gets an attribute from the request.
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function attribute(string $name, mixed $default = null): mixed
    {
        return $this->queryAttributes[$name] ?? $default;
    }

    /**
     * Gets a query parameter from the request.
     *
     * @param string            $name
     * @param array|string|null $default
     *
     * @return array|string|null
     */
    public function get(string $name, array|string|null $default = null): array|string|null
    {
        return $this->get[$name] ?? $default;
    }

    /**
     * Gets a POST parameter from the request.
     *
     * @param string            $name
     * @param array|string|null $default
     *
     * @return array|string|null
     */
    public function post(string $name, array|string|null $default = null): array|string|null
    {
        return $this->post[$name] ?? $default;
    }

    /**
     * Gets a SERVER attribute from the request.
     *
     * @param string            $name
     * @param array|string|null $default
     *
     * @return array|string|null
     */
    public function server(string $name, array|string|null $default = null): array|string|null
    {
        return $this->server[$name] ?? $default;
    }

    /**
     * Gets a cookie value.
     *
     * @param string            $name
     * @param array|string|null $default
     *
     * @return array|string|null
     */
    public function cookie(string $name, array|string|null $default = null): array|string|null
    {
        return $this->cookies[$name] ?? $default;
    }

    /**
     * Gets a header from the request.
     *
     * @param string            $name
     * @param array|string|null $default
     *
     * @return array|string|null
     */
    public function header(string $name, array|string|null $default = null): array|string|null
    {
        return $this->headers[$name] ?? $default;
    }

    /**
     * Gets an uploaded file from the request.
     *
     * @param string $name
     *
     * @return UploadedFileInterface|null
     */
    public function file(string $name): ?UploadedFileInterface
    {
        return $this->files[$name] ?? null;
    }

    /**
     *
     *
     * @param string $routeName
     * @param array  $params
     * @param array  $query
     *
     * @return ResponseInterface
     */
    public function redirect(string $routeName, array $params = [], array $query = []): ResponseInterface
    {
        return $this->getServer()->redirect($routeName, $params, $query);
    }

    public function getServer(): HttpServer
    {
        return $this->httpServer;
    }

}
