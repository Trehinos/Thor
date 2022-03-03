<?php

namespace Thor\Web\Assets;

use Thor\Http\Routing\Route;
use Thor\Http\Server\WebServer;
use Thor\Http\Response\Response;
use Thor\Http\Controllers\WebController;

final class AssetController extends WebController
{

    public function __construct(WebServer $webServer, private AssetsManager $assetsManager)
    {
        parent::__construct($webServer);
    }

    #[Route('load-asset', '/$asset', parameters: ['asset' => '.+'])]
    public function asset(string $identifier): Response
    {
        $asset = $this->assetsManager->getAsset($identifier);
    }



}
