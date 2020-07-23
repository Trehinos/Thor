<?php

namespace Thor\Http;

use Thor\Database\PdoExtension\PdoCollection;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Database\PdoExtension\PdoRequester;

use Thor\Debug\Logger;
use Thor\Http\Routing\Router;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Server
{

    const DEV = 'dev';
    const DEBUG = 'debug';
    const PROD = 'prod';

    const ENV = self::DEV;

    private ?Environment $twig;

    private ?PdoCollection $databases;

    private ?Router $router;

    private array $language;

    private ?string $current_routeName = null;

    public function __construct(
        ?Environment $twig = null,
        ?PdoCollection $databases = null,
        ?Router $router = null,
        $language = []
    ) {
        $this->twig = $twig;
        $this->databases = $databases;
        $this->router = $router;
        $this->language = $language;
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
            Logger::VERBOSE
        );
        $route = $this->router->match($request);

        if (null === $route) {
            Logger::write(' -> No route matched', Logger::DEBUG, Logger::WARNING);
            return new Response404($this->twig->render('errors/404.html.twig'));
        }
        if (false === $route) {
            Logger::write(
                " -> Method not allowed for route '{$this->current_routeName}'",
                Logger::DEBUG,
                Logger::WARNING
            );
            return new Response(
                'METHOD NOT ALLOWED',
                Response::STATUS_METHOD_NOT_ALLOWED,
                ['Allow' => $this->router->getErrorRoute()->getMethod()]
            );
        }

        $this->current_routeName = $this->router->getMatchedRouteName();
        Logger::write(" -> Matched with route '{$this->current_routeName}'", Logger::DEBUG);

        $params = $route->getFilledParams();
        $cClass = $route->getControllerClass();
        $cMethod = $route->getControllerMethod();

        Logger::write("  • Instantiate controller of type '$cClass'", Logger::DEBUG);
        $controller = new $cClass($this);
        Logger::write("  • Execute '$cClass::$cMethod()'", Logger::DEBUG);
        return $controller->$cMethod(...array_values($params));
    }

    public function getCurrentRouteName(): string
    {
        return $this->current_routeName ?? '';
    }

    public function getTwig(): ?Environment
    {
        return $this->twig;
    }

    public function getHandler(string $connectionName = 'default'): ?PdoHandler
    {
        Logger::write("Loading handler $connectionName", Logger::DEBUG);
        return $this->databases->get($connectionName);
    }

    public function getRequester(string $connectionName = 'default'): ?PdoRequester
    {
        $handler = $this->getHandler($connectionName);
        if (null === $handler) {
            return null;
        }

        return new PdoRequester($handler);
    }

    public function getRouter(): ?Router
    {
        return $this->router;
    }

    public function getLanguage(): array
    {
        return $this->language;
    }

    static function post(string $name, $default = null, ?int $filter = null, array $filter_options = [])
    {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_POST, $name, $filter, $filter_options)))
                ? $default
                : $filtered;
        }

        return $_POST[$name] ?? $default;
    }

    static function get(string $name, $default = null, ?int $filter = null, array $filter_options = [])
    {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_GET, $name, $filter, $filter_options)))
                ? $default
                : $filtered;
        }

        return $_GET[$name] ?? $default;
    }

    static function readCookie(string $name, string $default = '', ?int $filter = null, array $filter_options = [])
    {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_COOKIE, $name, $filter, $filter_options)))
                ? $default
                : $filtered;
        }

        return $_COOKIE[$name] ?? $default;
    }

    static function writeCookie(string $name, string $value)
    {
        setcookie($name, $value);
    }

    static function writeCookieArray(string $name, array $value)
    {
        foreach ($value as $key => $v) {
            self::writeCookie("$name[$key]", $v);
        }
    }

    static function readSession(string $name, $default = null, ?int $filter = null)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_SESSION, $name, $filter)))
                ? $default
                : $filtered;
        }

        return $_SESSION[$name] ?? $default;
    }

    static function writeSession(string $name, $value)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION[$name] = $value;
    }

}
