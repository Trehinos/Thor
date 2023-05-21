<?php

namespace Thor\Framework\Factories;

use Thor\Http\{Routing\Router};
use Twig\Environment;
use Thor\Framework\Thor;
use Thor\Framework\Globals;
use Thor\Http\Web\WebServer;
use Twig\Loader\FilesystemLoader;
use Thor\Common\Configuration\Configuration;
use Thor\Framework\Configurations\ThorConfiguration;

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

    /**
     * @param \Thor\Common\Configuration\Configuration $twigConfig
     */
    public function __construct(Configuration $twigConfig)
    {
        $this->twig = self::defaultTwigEnvironment($twigConfig);
    }

    /**
     * @param \Thor\Common\Configuration\Configuration $twig_config
     *
     * @return Environment
     */
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

    /**
     * @param WebServer                                $server
     * @param \Thor\Common\Configuration\Configuration $twig_config
     *
     * @return Environment
     * @throws \Exception
     * @throws \Exception
     */
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

    /**
     * @param WebServer $server
     * @param Router    $router
     *
     * @return $this
     * @throws \Exception
     * @throws \Exception
     */
    public function addDefaults(WebServer $server, Router $router): self
    {
        $this->twig->addGlobal('server', $server);
        $this->twig->addGlobal('appName', Thor::appName());
        $this->twig->addGlobal('appVendor', Thor::vendor());
        $this->twig->addGlobal('version', Thor::version());
        $this->twig->addGlobal('env', Thor::getEnv()->value);
        $this->twig->addGlobal('versionName', Thor::versionName());
        $this->twig->addGlobal('_lang', ThorConfiguration::get()->lang());
        $this->twig->addGlobal('DICT', $server->getLanguage());

        $this->twig->addFunction(TwigFunctionFactory::uuid());
        $this->twig->addFunction(TwigFunctionFactory::url($router));
        $this->twig->addFunction(TwigFunctionFactory::icon());
        $this->twig->addFunction(TwigFunctionFactory::dump());
        $this->twig->addFunction(TwigFunctionFactory::render($server));
        $this->twig->addFunction(TwigFunctionFactory::asset(AssetsListFactory::listFromConfiguration()));
        $this->twig->addFunction(TwigFunctionFactory::option());
        $this->twig->addFunction(TwigFunctionFactory::toast());

        $this->twig->addFilter(TwigFilterFactory::classname());
        $this->twig->addFilter(TwigFilterFactory::_($server));
        $this->twig->addFilter(TwigFilterFactory::date2date());
        $this->twig->addFilter(TwigFilterFactory::datetimeRelative());
        $this->twig->addFilter(TwigFilterFactory::toUtf8());
        $this->twig->addFilter(TwigFilterFactory::fromUtf8());
        $this->twig->addFilter(TwigFilterFactory::money('money'));
        $this->twig->addFilter(TwigFilterFactory::euro());
        $this->twig->addFilter(TwigFilterFactory::dollar());

        return $this;
    }

}
