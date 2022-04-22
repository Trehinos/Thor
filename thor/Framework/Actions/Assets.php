<?php

namespace Thor\Framework\Actions;

use Thor\Web\WebServer;
use Thor\Web\WebController;
use Thor\Http\Routing\Route;
use Thor\Http\Response\Response;
use Thor\Http\Response\ResponseFactory;
use Thor\Framework\Factories\AssetsListFactory;

final class Assets extends WebController
{

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
    }

    #[Route('load-asset', '/asset/$asset', parameters: ['asset' => '.+'])]
    public function asset(string $identifier): Response
    {
        $asset = AssetsListFactory::listFromConfiguration()[$identifier] ?? null;
        if ($asset === null) {
            return ResponseFactory::notFound("Asset '$identifier' not found");
        }

        return ResponseFactory::ok($asset->getContent(), ['Content-Type' => $asset->type->getMimeType()]);
    }

}
