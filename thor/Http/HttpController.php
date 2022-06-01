<?php

namespace Thor\Http;

use JetBrains\PhpStorm\Pure;
use JetBrains\PhpStorm\ExpectedValues;
use Thor\Http\{Server\HttpServer,
    Request\HttpMethod,
    Response\HttpStatus,
    Response\ResponseInterface,
    Request\UploadedFileInterface,
    Request\ServerRequestInterface};

/**
 * This abstract class is a base class for every controller of an HttpServer.
 *
 * It defines shortcuts to access Server and Request objects.
 *
 * @package          Thor/Http/Controllers
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
abstract class HttpController
{

    /**
     * @var HttpMethod the method of the client's request.
     */
    public HttpMethod $method;

    /**
     * @var UriInterface the Uri requested by the client.
     */
    public UriInterface $uri;

    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private array $cookies;
    private array $headers;
    private array $queryAttributes;

    /**
     * @param HttpServer $httpServer
     */
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
     * Sends a redirect response with an Uri corresponding the route name.
     *
     * @param string     $routeName
     * @param array      $params
     * @param array      $query
     * @param HttpStatus $status (default : 302 FOUND)
     *
     * @return ResponseInterface
     */
    public function redirect(
        string $routeName,
        array $params = [],
        array $query = [],
        #[ExpectedValues([
            HttpStatus::FOUND,
            HttpStatus::SEE_OTHER,
            HttpStatus::TEMPORARY_REDIRECT,
            HttpStatus::PERMANENT_REDIRECT,
        ])]
        HttpStatus $status = HttpStatus::FOUND
    ): ResponseInterface {
        return $this->getServer()->redirect($routeName, $params, $query, $status);
    }

    /**
     * Gets the server which has executed this Controller.
     *
     * @return HttpServer
     */
    public function getServer(): HttpServer
    {
        return $this->httpServer;
    }

}
