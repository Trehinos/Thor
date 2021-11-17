<?php

/**
 * @package          Trehinos/Thor/Factories
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Factories;

use Thor\Globals;
use Thor\Http\Server\WebServer;
use Thor\Http\Routing\Router;
use Thor\Thor;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigFactory
{

    private Environment $twig;

    public function __construct(array $twigConfig = [])
    {
        $this->twig = self::defaultTwigEnvironment($twigConfig);
    }

    private static function defaultTwigEnvironment(array $twig_config = []): Environment
    {
        return new Environment(
            new FilesystemLoader(
                array_map(
                    fn(string $folderName) => Globals::CODE_DIR . $folderName,
                    $twig_config['views_dir'] ?? ['']
                )
            ),
            [
                'cache' => Globals::VAR_DIR . ($twig_config['cache_dir'] ?? ''),
                'debug' => Thor::isDev(),
            ]
        );
    }

    public static function createTwigFromConfiguration(
        WebServer $server,
        array $twig_config
    ): Environment {
        return (new self($twig_config))->addDefaults($server, $server->getRouter())->produce()
        ;
    }

    /**
     * @param array $options unused
     *
     * @return Environment
     */
    public function produce(array $options = []): Environment
    {
        return $this->twig;
    }

    public function addDefaults(WebServer $server, Router $router): self
    {
        $this->twig->addGlobal('server', $server);
        $this->twig->addGlobal('appName', Thor::appName());
        $this->twig->addGlobal('version', Thor::VERSION);
        $this->twig->addGlobal('versionName', Thor::VERSION_NAME);
        $this->twig->addGlobal('DICT', $server->getLanguage());

        $this->twig->addFunction(TwigFunctionFactory::url($router));
        $this->twig->addFunction(TwigFunctionFactory::icon());
        $this->twig->addFunction(TwigFunctionFactory::render($server));
        $this->twig->addFunction(TwigFunctionFactory::dump());

        $this->twig->addFilter(TwigFilterFactory::classname());
        $this->twig->addFilter(TwigFilterFactory::_($server));

        return $this;
    }


}
