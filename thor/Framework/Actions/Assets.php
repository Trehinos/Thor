<?php

namespace Thor\Framework\Actions;

use Thor\Http\Web\WebServer;
use Thor\Http\Routing\Route;
use Thor\Http\Web\WebController;
use Thor\Http\Response\Response;
use Thor\Http\Response\ResponseFactory;
use Thor\Framework\Factories\AssetsListFactory;

/**
 *
 */

/**
 *
 */
final class Assets extends WebController
{

    /**
     * @param \Thor\Http\Web\WebServer $webServer
     */
    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
    }

    /**
     * @param string $identifier
     *
     * @return Response
     * @throws \Exception
     * @throws \Exception
     */
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
