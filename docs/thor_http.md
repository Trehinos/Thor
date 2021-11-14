# Thor Http

This module is in charge of handling HTTP requests. It offers classes to implement a Request->Controller->Response cycle
managed by a Router.

## The HTTP kernel and the HTTP cycle

### Kernels and entry points

* ```HttpKernel``` : instantiate a ```HttpServer```, to create APIs. Entry point : ```web/api.php```
* ```WebKernel``` : instantiate a ```WebServer```, to create webpages with **Twig**. Entry point : ```web/index.php```

It creates a ```ServerRequestInterface``` from the environment and makes the server handle it. Then the kernel sends the headers, and
the body, extracted from a ```ResponseInterface``` object returned by the controller, to the client.

### Servers

```HttpServer``` and ```WebServer``` are ```RequestHandlerInterface``` :

```php
# PSR 15
interface RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
```

#### ```HttpServer``` :

```php
public function __construct(
    private Router $router,
    private ?Security $security,
    private PdoCollection $pdoCollection,
    private array $language
);
public function getRequest(): ?ServerRequestInterface;
public function handle(ServerRequestInterface $request): ResponseInterface;
public function getRouter(): Router;
public function getRequester(string $name = 'default'): ?PdoRequester;
public function getHandler(string $name = 'default'): ?PdoHandler;
public function getSecurity(): ?Security;
public function getLanguage(): array;
public function redirect(string $routeName, array $params = [], string $queryString = ''): ResponseInterface;
public function redirectTo(UriInterface $uri): ResponseInterface;
public function generateUrl(string $routeName, array $params = [], string $queryString = ''): UriInterface;
```

#### ```WebServer``` :

```php
public function __construct(
    Router $router,
    ?Security $security,
    PdoCollection $pdoCollection,
    array $language,
    private Environment $twig
);
public function getTwig(): Environment;
```

### Request

#### ```HttpMethod``` enumeration :

* Cases
```php
# HTTP 1.1
case GET = 'GET';
case POST = 'POST';
case PUT = 'PUT';
case PATCH = 'PATCH';
case DELETE = 'DELETE';
case HEAD = 'HEAD';
case TRACE = 'TRACE';
case CONNECT = 'CONNECT';
case OPTIONS = 'OPTIONS';
```

* Public methods :

```php
public function hasBody(): bool;
public function responseHasBody(): bool;
public function isSafe(): bool;
public function isIdempotent(): bool;
public function compatibleWithCache(): bool;
public function compatibleWithHtml(): bool;
```

#### The ```RequestInterface``` ```extends MessageInterface```


```php
# PSR 7
public function getRequestTarget(): string;
public function withRequestTarget(string $requestTarget): static;
public function getMethod(): HttpMethod;
public function withMethod( HttpMethod $method): static;
public function getUri(): UriInterface;
public function withUri(UriInterface $uri, bool $preserveHost = false) : static;
```

### Response ```class```

-

### BaseController and controllers

#### Http controller

```php
public function __construct(protected HttpServer $httpServer);
public function attribute(string $name, mixed $default = null): mixed;
public function get(string $name, array|string|null $default = null):array|string|null;
public function post(string $name, array|string|null $default = null):array|string|null;
public function server(string $name, array|string|null $default = null):array|string|null;
public function cookie(string $name, array|string|null $default = null):array|string|null;
public function header(string $name, array|string|null $default = null):array|string|null;
public function file(string $name): ?UploadedFileInterface;
public function redirect(string $routeName, array $params = [], string $queryString = ''): Response;
public function getRequest(): ServerRequestInterface;
public function getServer(): HttpServer;
```

#### Web controller ```extends HttpController```

```php
public function __construct(protected WebServer $webServer);
public function getServer(): WebServer;
public function twigResponse(string $fileName, array $params = [], HttpStatus $status = HttpStatus::OK, array $headers = []): Response;
```

#### Routing

> Click on the link below to read how to link a specific route to a method :  
> [Thor routing documentation](thor_routing.md)
