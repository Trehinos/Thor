# Thor Http
This module is in charge of handling HTTP requests. It offers classes to implement a Request->Controller->Response cycle
managed by a Router.

## The HTTP kernel and the HTTP cycle

The ```HttpKernel``` is in charge of ```Thor\Http\Server``` instantiation.  
It creates a ```Request``` from the environment and makes the server handle it. Then the kernel sends the headers,
and the body, extracted from a ```Response``` object returned by the controller, to the client.

### Request ```class``` ```final```

* Public constants :  
```php
// HTTP 1.1
const GET = 'GET';
const HEAD = 'HEAD';
const POST = 'POST';
const PUT = 'PUT';
const DELETE = 'DELETE';
const CONNECT = 'CONNECT';
const OPTIONS = 'OPTIONS';
const TRACE = 'TRACE';
const PATCH = 'PATCH';

// WEBDAV   
/** @see http://www.webdav.org/specs/rfc4918.html */
const MKCOL = 'MKCOL';
const COPY = 'COPY';
const MOVE = 'MOVE';
const PROPFIND = 'PROPFIND';
const PROPPATCH = 'PROPPATCH';
const LOCK = 'LOCK';
const UNLOCK = 'UNLOCK';
```
* Public properties :  
```php
bool $hasBody;
bool $responseHasBody;
bool $safe;
bool $idempotent;
bool $cache;
bool $html;
```

* ```static createFromServer(): self```
* ```static getAllHeaders(): array```
* ```getMethod(): string```
* ```getPathInfo(): string```
* ```getHeader(string $name, $default = null): array|string|null```
* ```getHeaders(): array```
* ```getBody(): string```
* ```queryGet(string $name, $default = null): string|array|null```
* ```postVariable(string $name, $default = null): string|array|null```

### Response ```class``` 

* Constants :
```php
const STATUS_SUCCESS = 200;
const STATUS_REDIRECT = 302;
const STATUS_FORBIDDEN = 403;
const STATUS_NOT_FOUND = 404;
const STATUS_METHOD_NOT_ALLOWED = 405;
```

* ```__construct(string $body = '', int $status = self::STATUS_SUCCESS, array $headers = [])```
* ```getBody(): string```
* ```getStatus(): int```
* ```getHeaders(): array```
* ```setHeader(string $name, array|string $value): void```

### The Server class

This class is a facade of a Http server.



### BaseController and controllers

#### Routing
> Click on the link below to read how to link a specific route to a method :  
> [Thor routing documentation](thor_routing.md)
