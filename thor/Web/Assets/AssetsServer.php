<?php

namespace Thor\Web\Assets;

use Thor\Globals;
use Twig\TwigFunction;
use Thor\Http\Routing\Router;
use Thor\Http\Server\HttpServer;
use Thor\Security\SecurityInterface;
use Thor\Configuration\Configuration;
use Thor\Database\PdoExtension\PdoCollection;

class AssetsServer extends HttpServer /** Todo : kernel and entry points */
{

    public function __construct(
        Router $router,
        ?SecurityInterface $security,
        PdoCollection $pdoCollection,
        Configuration $language
    ) {
        parent::__construct($router, $security, $pdoCollection, $language);
    }

    public function twigFunction(
        string $cacheDirectory = Globals::STATIC_DIR,
        string $targetDirectory = Globals::WEB_DIR,
        string $webVendors = Globals::WEB_DIR . 'vendors/',
        string $type = null
    ): TwigFunction {
        return new TwigFunction(
            'asset',
            function (string $filename, bool $isVendor = false) use ($type): string {
                if ($type === null) {
                    $chunks = explode('.', $filename);
                    $type = $chunks[count($chunks) - 1];
                }
                return match ($type) {
                    'css' => ""
                };
            }
        );
    }

}
