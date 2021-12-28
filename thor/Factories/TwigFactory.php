<?php

namespace Thor\Factories;

use Thor\Thor;
use Thor\Globals;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Thor\Http\{Routing\Router, Server\WebServer};
use Thor\Configuration\Configuration;
use Thor\Configuration\ThorConfiguration;

/**
 * A factory to create the Twig Environment from configuration.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class TwigFactory
{

    private Environment $twig;

    public function __construct(Configuration $twigConfig)
    {
        $this->twig = self::defaultTwigEnvironment($twigConfig);
    }

    private static function defaultTwigEnvironment(Configuration $twig_config): Environment
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
        Configuration $twig_config
    ): Environment {
        return (new self($twig_config))->addDefaults($server, $server->getRouter())->produce();
    }

    /**
     * @return Environment
     */
    public function produce(): Environment
    {
        return $this->twig;
    }

    public function addDefaults(WebServer $server, Router $router): self
    {
        $this->twig->addGlobal('server', $server);
        $this->twig->addGlobal('appName', Thor::appName());
        $this->twig->addGlobal('appVendor', Thor::vendor());
        $this->twig->addGlobal('version', Thor::version());
        $this->twig->addGlobal('versionName', Thor::versionName());
        $this->twig->addGlobal('lang', ThorConfiguration::get()->lang());
        $this->twig->addGlobal('DICT', $server->getLanguage());

        $this->twig->addFunction(TwigFunctionFactory::url($router));
        $this->twig->addFunction(TwigFunctionFactory::icon());
        $this->twig->addFunction(TwigFunctionFactory::render($server));
        $this->twig->addFunction(TwigFunctionFactory::dump());
        $this->twig->addFunction(TwigFunctionFactory::uuid());

        $this->twig->addFilter(TwigFilterFactory::classname());
        $this->twig->addFilter(TwigFilterFactory::_($server));
        $this->twig->addFilter(TwigFilterFactory::date2date());
        $this->twig->addFilter(TwigFilterFactory::datetimeRelative());
        $this->twig->addFilter(TwigFilterFactory::toUtf8());
        $this->twig->addFilter(TwigFilterFactory::fromUtf8());
        $this->twig->addFilter(TwigFilterFactory::format());

        return $this;
    }


}
