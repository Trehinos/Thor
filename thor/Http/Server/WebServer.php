<?php

namespace Thor\Http\Server;

use Twig\Environment;
use Thor\Security\Security;
use JetBrains\PhpStorm\Pure;
use Thor\Http\Routing\Router;
use Thor\Database\PdoExtension\PdoCollection;

class WebServer extends HttpServer
{

    #[Pure]
    public function __construct(
        Router $router,
        ?Security $security,
        PdoCollection $pdoCollection,
        array $language,
        private Environment $twig
    ) {
        parent::__construct($router, $security, $pdoCollection, $language);
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }

}
