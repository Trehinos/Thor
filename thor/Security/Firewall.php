<?php

namespace Thor\Security;

use JetBrains\PhpStorm\Immutable;
use Thor\Http\Response\ResponseFactory;
use Thor\Framework\Factories\SecurityFactory;
use Thor\Http\{Routing\Router,
    Response\ResponseInterface,
    Request\ServerRequestInterface,
    Server\RequestHandlerInterface};

/**
 * Thor firewall.
 *
 * @see              SecurityFactory to instantiate from configuration.
 *
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Firewall implements RequestHandlerInterface
{

    public bool $isAuthenticated = false;

    /**
     * @param SecurityInterface $security
     * @param Router            $router
     * @param string            $pattern
     * @param string|null       $redirect
     * @param string|null       $loginRoute
     * @param string|null       $logoutRoute
     * @param string|null       $checkRoute
     * @param array             $excludedRoutes
     * @param array             $excludedPaths
     */
    public function __construct(
        private SecurityInterface $security,
        private Router $router,
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
        public array $excludedRoutes = [],
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public array $excludedPaths = []
    ) {
    }

    /**
     * True if the Request will cause a redirection.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function redirect(ServerRequestInterface $request): bool
    {
        if (!str_starts_with($request->getUri()->getPath(), $this->pattern)) {
            return false;
        }

        if ($this->pathIsExcluded($request) || $this->routeIsExcluded()) {
            return false;
        }

        if (!$this->isAuthenticated) {
            return true;
        }
        $routeName = $this->router->getMatchedRouteName();
        if ($routeName !== null) {
            $route = $this->router->getRoute($routeName);
            if ($route->authorization !== null) {
                return !$route->authorization->isAuthorized($this->security->getCurrentIdentity());
            }
        }
        return false;
    }

    /**
     * True if the Request is excluded with its path.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function pathIsExcluded(ServerRequestInterface $request): bool
    {
        return array_reduce(
            $this->excludedPaths,
            fn(bool $carry, string $excludePath) => $carry
                                                    || str_starts_with($request->getUri()->getPath(), $excludePath),
            false
        );
    }

    /**
     * True if the matched route of the router is in this firewall's excluded routes.
     *
     * @return bool
     */
    public function routeIsExcluded(): bool
    {
        return in_array(
            $this->router->getMatchedRouteName(),
            [
                $this->loginRoute,
                $this->logoutRoute,
                $this->checkRoute,
                ...$this->excludedRoutes,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ResponseFactory::found($this->router->getUrl($this->redirect));
    }
}
