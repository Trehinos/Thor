<?php

namespace Thor\Http\Server;

use Thor\Http\Uri;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\Security\Security;
use Thor\Http\UriInterface;
use Thor\Http\Routing\Router;
use Thor\Http\Response\Response;
use Thor\Http\Response\HttpStatus;
use Thor\Factories\ResponseFactory;
use Thor\Http\Response\ResponseInterface;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Http\Request\ServerRequestInterface;
use Thor\Database\PdoExtension\PdoCollection;

class HttpServer implements RequestHandlerInterface
{

    public function __construct(
        private Router $router,
        private ?Security $security,
        private PdoCollection $pdoCollection,
        private array $language
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'localhost';
        Logger::write("Routing request [{method} '{path}'] from $ip", context: [
            'method' => $request->getMethod()->value,
            'path'   => $request->getUri()->getPath(),
        ]);

        $route = $this->router->match($request);
        if (false === $route) {
            Logger::write(
                ' -> Method {method} not allowed',
                LogLevel::DEBUG,
                ['method' => $request->getMethod()->value]
            );
            return Response::createFromStatus(
                HttpStatus::METHOD_NOT_ALLOWED,
                ['Allow' => $this->router->getErrorRoute()->getMethod()->value]
            );
        }
        if (null === $route) {
            Logger::write(' -> No route matched', LogLevel::DEBUG);
            return Response::createFromStatus(HttpStatus::NOT_FOUND);
        }

        $controllerHandler = new ControllerHandler($this, $route);
        if (null !== ($redirect = $this->security->protect($request, $this->router, $controllerHandler))) {
            return $redirect;
        }
        return $controllerHandler->handle($request);
    }

    public function getRequester(string $name = 'default'): ?PdoRequester
    {
        return null !== ($handler = $this->getHandler($name)) ? new PdoRequester($handler) : null;
    }

    public function getHandler(string $name = 'default'): ?PdoHandler
    {
        return $this->pdoCollection->get($name);
    }

    public function getSecurity(): ?Security
    {
        return $this->security;
    }

    public function getLanguage(): array
    {
        return $this->language;
    }

    public function redirect(string $routeName, array $params = [], string $queryString = ''): ResponseInterface
    {
        return $this->redirectTo($this->generateUrl($routeName, $params, $queryString));
    }

    public function redirectTo(UriInterface $uri): ResponseInterface
    {
        return ResponseFactory::createRedirection($uri);
    }

    public function generateUrl(string $routeName, array $params = [], string $queryString = ''): UriInterface
    {
        if ($this->router->getRoute($routeName) === null) {
            return Uri::create("#generate-url-error");
        }
        return $this->router->getUrl($routeName, $params, $queryString);
    }
}
