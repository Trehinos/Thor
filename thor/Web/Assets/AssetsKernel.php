<?php

namespace Thor\Web\Assets;

use Twig\TwigFunction;
use Thor\Http\HttpKernel;
use Thor\Http\Server\HttpServer;

class AssetsKernel extends HttpKernel
{

    /**
     * @var Asset[]
     */
    private array $assets = [];

    public function __construct(HttpServer $server) {
        parent::__construct($server);
    }

    public function addAsset(Asset $asset): void
    {
        $this->assets[$asset->identifier] = $asset;
    }

    public function twigFunction(): TwigFunction {
        return new TwigFunction(
            'asset',
            function (string $identifier): string {
                return $this->assets[$identifier]?->getHtml() ?? '';
            }
        );
    }

}
