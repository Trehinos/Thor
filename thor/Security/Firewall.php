<?php

namespace Thor\Security;

use Thor\Http\Routing\Router;
use Thor\Http\Response\Response;
use JetBrains\PhpStorm\Immutable;
use Thor\Http\Server\MiddlewareInterface;
use Thor\Http\Response\ResponseInterface;
use Thor\Http\Request\ServerRequestInterface;
use Thor\Http\Server\RequestHandlerInterface;

class Firewall implements MiddlewareInterface
{

    public ?Router $router = null;
    public bool $isAuthenticated = false;

    public function __construct(
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public string $pattern = '/',
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $redirect = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $loginRoute = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $logoutRoute = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $checkRoute = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public array $exclude = []
    ) {
    }

    public function redirect(ServerRequestInterface $request): bool
    {
        if (!str_starts_with($request->getUri()->getPath(), $this->pattern)) {
            return false;
        }

        if ($this->pathIsExcluded($request) || $this->routeIsExcluded()) {
            return false;
        }

        // TODO is Authenticated, verify authorization
        return !$this->isAuthenticated;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return Response::create($this->redirect);
    }

    public function pathIsExcluded(ServerRequestInterface $request): bool
    {
        return array_reduce(
            $this->exclude,
            fn(bool $carry, string $excludePath) => $carry
                || str_starts_with($request->getUri()->getPath(), $excludePath),
            false
        );
    }

    public function routeIsExcluded(): bool
    {
        return !in_array(
            $this->router?->getMatchedRouteName(),
            [
                $this->loginRoute,
                $this->logoutRoute,
                $this->checkRoute,
            ]
        );
    }
}
