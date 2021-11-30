<?php

namespace Thor\Http\Server;

use Twig\Environment;
use Thor\Debug\Logger;
use Thor\Security\Security;
use JetBrains\PhpStorm\Pure;
use Thor\Http\Routing\Route;
use Thor\Http\Routing\Router;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Http\Request\ServerRequestInterface;

/**
 * Handles a ServerRequestInterface and send a ResponseInterface.
 *
 * In comparison with an HttpServer, this server handles a Twig Environment.
 *
 * @package          Thor/Http/Server
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class WebServer extends HttpServer
{

    #[Pure]
    public function __construct(
        Router $router,
        ?Security $security,
        PdoCollection $pdoCollection,
        array $language,
        public ?Environment $twig = null
    ) {
        parent::__construct($router, $security, $pdoCollection, $language);
    }

    /**
     * @inheritDoc
     */
    protected function route(ServerRequestInterface $request): Route|false|null
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'localhost';
        Logger::write("Routing request [{method} '{path}'] from $ip", context: [
            'method' => $request->getMethod()->value,
            'path'   => substr($request->getUri()->getPath(), strlen('/index.php')),
        ]);

        return $this->getRouter()->match($request, 'index.php');
    }

    /**
     * Gets the Twig Environment of the server.
     *
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

}
