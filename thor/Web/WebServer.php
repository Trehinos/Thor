<?php

namespace Thor\Web;

use Twig\Environment;
use Thor\Debug\Logger;
use JetBrains\PhpStorm\Pure;
use Thor\Http\Routing\Route;
use Thor\Http\Routing\Router;
use Thor\Http\Server\HttpServer;
use Thor\Security\SecurityInterface;
use Thor\Configuration\Configuration;
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

    /**
     * @param Router                 $router
     * @param SecurityInterface|null $security
     * @param PdoCollection          $pdoCollection
     * @param Configuration          $language
     * @param Environment|null       $twig
     */
    #[Pure]
    public function __construct(
        Router $router,
        ?SecurityInterface $security,
        PdoCollection $pdoCollection,
        Configuration $language,
        public ?Environment $twig = null
    ) {
        parent::__construct($router, $security, $pdoCollection, $language);
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

}
