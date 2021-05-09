<?php

/**
 * @package Trehinos/Thor/Http
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\Http;

use Twig\Environment;
use Thor\Debug\Logger;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use JetBrains\PhpStorm\Pure;
use Twig\Error\RuntimeError;
use Thor\Http\Routing\Router;
use Thor\Security\UserInterface;
use Thor\Security\SecurityContext;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoExtension\PdoCollection;

class Server
{

    private ?string $current_routeName = null;

    public function __construct(
        private array $config,
        private ?Environment $twig = null,
        private ?PdoCollection $databases = null,
        private ?Router $router = null,
        private ?SecurityContext $security = null,
        private array $language = []
    ) {
    }

    #[Pure] public static function post(
        string $name,
        array|string|null $default = null,
        ?int $filter = null,
        array $filter_options = [],

    ): array|string|null {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_POST, $name, $filter, $filter_options)))
                ? $default
                : $filtered;
        }

        return $_POST[$name] ?? $default;
    }

    #[Pure] public static function get(
        string $name,
        array|string|null $default = null,
        ?int $filter = null,
        array $filter_options = []
    ): array|string|null {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_GET, $name, $filter, $filter_options)))
                ? $default
                : $filtered;
        }

        return $_GET[$name] ?? $default;
    }

    #[Pure] public static function readCookie(
        string $name,
        string $default = '',
        ?int $filter = null,
        array $filter_options = []
    ): string {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_COOKIE, $name, $filter, $filter_options)))
                ? $default
                : $filtered;
        }

        return $_COOKIE[$name] ?? $default;
    }

    public static function writeCookie(string $name, array|string $value): void
    {
        if (is_array($value)) {
            foreach ($value as $key => $v) {
                self::writeCookie("$name[$key]", $v);
            }
            return;
        }
        setcookie($name, $value);
    }

    public static function readSession(string $name, mixed $default = null, ?int $filter = null): mixed
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (null !== $filter) {
            return (false === ($filtered = filter_var($_SESSION[$name], $filter)))
                ? $default
                : $filtered;
        }

        return $_SESSION[$name] ?? $default;
    }

    public static function writeSession(string $name, mixed $value): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION[$name] = $value;
    }

    public function getAppName(): string
    {
        return $this->config['app_name'] ?? '';
    }

    public function getUser(): UserInterface
    {
        return $this->getSecurity()->getUser($this->getSecurity()->getCurrentUsername());
    }

    public function getSecurity(): ?SecurityContext
    {
        return $this->security;
    }

    /**
     * Server->handle
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(Request $request): Response
    {
        if (null === $this->router) {
            return new Response('No router', 500);
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        Logger::write(
            "Routing request [{$request->getMethod()} '{$request->getPathInfo()}'] from $ip",
            Logger::LEVEL_VERBOSE
        );
        $route = $this->router->match($request);

        if ($this->security?->isActive()) {
            if (
                !$this->security->isAuthenticated($request->getHeader($this->security->tokenKey)) &&
                !in_array(
                    $this->router->getMatchedRouteName(),
                    [
                        $this->security->loginRoute,
                        $this->security->logoutRoute,
                        $this->security->checkRoute,
                    ]
                )
            ) {
                return $this->redirect($this->security?->loginRoute ?? 'login');
            }
        }

        if (null === $route) {
            Logger::write(' -> No route matched', Logger::LEVEL_DEBUG, Logger::SEVERITY_WARNING);
            if ($this->twig) {
                return new Response404($this->twig?->render('errors/404.html.twig') ?? 'Not found...');
            }
        }
        if (false === $route) {
            Logger::write(
                " -> Method not allowed for route '{$this->current_routeName}'",
                Logger::LEVEL_DEBUG,
                Logger::SEVERITY_WARNING
            );
            return new Response(
                'METHOD NOT ALLOWED',
                Response::STATUS_METHOD_NOT_ALLOWED,
                ['Allow' => $this->router->getErrorRoute()->getMethod()]
            );
        }

        $this->current_routeName = $this->router->getMatchedRouteName();
        Logger::write(" -> Matched with route '{$this->current_routeName}'", Logger::LEVEL_DEBUG);

        $params = $route->getFilledParams();
        $cClass = $route->getControllerClass();
        $cMethod = $route->getControllerMethod();

        Logger::write("  • Instantiate controller of type '$cClass'", Logger::LEVEL_DEBUG);
        $controller = new $cClass($this);
        Logger::write("  • Execute '$cClass::$cMethod()'", Logger::LEVEL_DEBUG);
        return $controller->$cMethod(...array_values($params));
    }

    public function redirect(string $routeName, array $params = [], string $queryString = ''): Response
    {
        return new Response('', 302, ['Location' => $this->generateUrl($routeName, $params, $queryString)]);
    }

    public function generateUrl(string $routeName, array $params = [], string $queryString = ''): string
    {
        if ($this->getRouter()->getRoute($routeName) === null) {
            return '#generate-url-error';
        }

        return $this->getRouter()->getUrl($routeName, $params, $queryString);
    }

    public function getRouter(): ?Router
    {
        return $this->router;
    }

    public function getCurrentRouteName(): string
    {
        return $this->current_routeName ?? '';
    }

    public function getTwig(): ?Environment
    {
        return $this->twig;
    }

    public function getRequester(string $connectionName = 'default'): ?PdoRequester
    {
        $handler = $this->getHandler($connectionName);
        if (null === $handler) {
            return null;
        }

        return new PdoRequester($handler);
    }

    public function getHandler(string $connectionName = 'default'): ?PdoHandler
    {
        Logger::write("Loading handler $connectionName", Logger::LEVEL_DEBUG);
        return $this->databases->get($connectionName);
    }

    public function getLanguage(): array
    {
        return $this->language;
    }
}
