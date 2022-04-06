<?php

namespace Thor\Web\Assets;

use Thor\Web\WebServer;
use Thor\Web\WebController;
use Thor\Http\Routing\Route;
use Thor\Http\Response\Response;
use Thor\Factories\ResponseFactory;
use Thor\Factories\AssetsListFactory;

final class AssetController extends WebController
{

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
    }

    #[Route('load-asset', '/$asset', parameters: ['asset' => '.+'])]
    public function asset(string $identifier): Response
    {
        $list = AssetsListFactory::listFromConfiguration();
        $asset = $list[$identifier] ?? null;
        if ($asset === null) {
            return ResponseFactory::notFound();
        }

        return ResponseFactory::ok(); // TODO
    }

}
