<?php

namespace Thor\Http\Server;

use InvalidArgumentException;
use Thor\Debug\{Logger, LogLevel};
use Thor\Factories\ResponseFactory;
use Thor\Security\SecurityInterface;
use JetBrains\PhpStorm\ExpectedValues;
use Thor\Database\PdoExtension\{PdoHandler, PdoRequester, PdoCollection};
use Thor\Http\{Uri,
    UriInterface,
    Routing\Route,
    Routing\Router,
    Response\HttpStatus,
    Response\ResponseInterface,
    Request\ServerRequestInterface
};

class HttpServer implements RequestHandlerInterface
{

    private ?ServerRequestInterface $request = null;

    public function __construct(
        private Router $router,
        private ?SecurityInterface $security,
        private PdoCollection $pdoCollection,
        private array $language
    ) {
    }

    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    final public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        $route = $this->route($request);
        if (false === $route) {
            Logger::write(
                ' -> Method {method} not allowed',
                LogLevel::DEBUG,
                ['method' => $request->getMethod()->value]
            );
            return ResponseFactory::methodNotAllowed($this->router->getErrorRoute()->getMethod()->value);
        }
        if (null === $route) {
            Logger::write(' -> No route matched', LogLevel::DEBUG);
            return ResponseFactory::notFound();
        }

        $controllerHandler = new ControllerHandler($this, $route);
        if (null !== ($redirect = $this->security?->protect($request))) {
            return $redirect;
        }
        return $controllerHandler->handle($request);
    }

    protected function route(ServerRequestInterface $request): Route|false|null
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'localhost';
        Logger::write("Routing request [{method} '{path}'] from $ip", context: [
            'method' => $request->getMethod()->value,
            'path'   => substr($request->getUri()->getPath(), strlen('/api.php')),
        ]);

        return $this->router->match($request, 'api.php');
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getRequester(string $name = 'default'): ?PdoRequester
    {
        return null !== ($handler = $this->getHandler($name)) ? new PdoRequester($handler) : null;
    }

    public function getHandler(string $name = 'default'): ?PdoHandler
    {
        return $this->pdoCollection->get($name);
    }

    public function getSecurity(): ?SecurityInterface
    {
        return $this->security;
    }

    public function getLanguage(): array
    {
        return $this->language;
    }

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
        return $this->redirectTo($this->generateUrl($routeName, $params, $query), $status);
    }

    public function redirectTo(UriInterface $uri, HttpStatus $status = HttpStatus::FOUND): ResponseInterface
    {
        return match ($status) {
            HttpStatus::FOUND => ResponseFactory::found($uri),
            HttpStatus::SEE_OTHER => ResponseFactory::seeOther($uri),
            HttpStatus::TEMPORARY_REDIRECT => ResponseFactory::temporaryRedirect($uri),
            HttpStatus::PERMANENT_REDIRECT => ResponseFactory::permanentRedirect($uri),
            default => throw new InvalidArgumentException()
        };
    }

    public function generateUrl(string $routeName, array $params = [], array $query = []): UriInterface
    {
        if ($this->router->getRoute($routeName) === null) {
            return Uri::create("#generate-url-error");
        }
        return $this->router->getUrl($routeName, $params, $query);
    }
}
