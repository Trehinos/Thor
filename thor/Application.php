<?php

namespace Thor;

use Exception;

use Symfony\Component\Yaml\Yaml;
use Thor\Cli\CliKernel;
use Thor\Debug\Logger;
use Thor\Http\HttpKernel;
use Throwable;

final class Application implements KernelInterface
{

    private ?KernelInterface $kernel;

    public function __construct(?KernelInterface $kernel = null)
    {
        $this->kernel = $kernel;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        if (null === $this->kernel) {
            Logger::write("Application fatal error : kernel not defined.");
            echo "Error : Kernel not selected.\n";

            throw new Exception("Application fatal error : kernel not defined.");
        }
        $this->kernel->execute();
    }

    public static string $thor_env = 'dev';
    public static array $config = [];
    public static array $language = [];
    public static array $databases = [];

    /**
     * Loads $config, $databases and $language
     */
    public static function loadMainConfiguration(): void
    {
        Logger::getDefaultLogger('prod', Globals::CODE_DIR);
        ['config' => self::$config, 'database' => self::$databases] = ConfigurationLoader::loadConfig(
            'config',
            'database'
        );
        self::$thor_env = self::$config['env'] ?? 'debug';
        $lang = $config['lang'] ?? 'fr';
        self::$language = Yaml::parseFile(Globals::STATIC_DIR . "langs/$lang.yml");
        ini_set('date.timezone', $config['timezone'] ?? 'Europe/Paris');
    }

    /**
     * $thor_env: 'dev' => E_ALL, 'debug' => E_ERROR|E_WARNING|E_PARSE, 'prod' => 0
     */
    public static function setErrorReporting(): void
    {
        if ('prod' === self::$thor_env) {
            ini_set('display_errors', 0);
        } elseif ('debug' === self::$thor_env) {
            ini_set('display_errors', E_ERROR | E_WARNING | E_PARSE);
        } else {
            ini_set('display_errors', E_ALL);
        }
    }

    /**
     * executeKernel(): loads and executes a kernel
     *
     * @param string|null $thor_kernel
     *
     * @throws Throwable
     */
    public static function executeKernel(?string $thor_kernel): void
    {
        $kernel = null;
        Logger::write('APP_START ### APP_START', Logger::DEV);
        try {
            switch ($thor_kernel) {
                case 'http':
                    $kernel = new HttpKernel(
                        ['databases' => self::$databases, 'language' => self::$language] +
                        ConfigurationLoader::loadConfig('twig', 'security') +
                        ConfigurationLoader::loadStatic('routes')
                    );
                    break;

                case 'cli':
                    $kernel = CliKernel::createCli(ConfigurationLoader::loadStatic('commands'));
                    break;

                case 'repl':
                case 'automaton':
                    echo "Error : Not implemented $thor_kernel kernel.\n";
                    break;
            }

            $app = new Application($kernel);
            Logger::write('Execute application');
            $app->execute();
            Logger::write('Application executed !');
        } catch (Throwable $e) {
            // Catches all uncatched exception to log them in log file.
            Logger::logThrowable($e);
            echo "UNRECOVERABLE ERROR\n";
            if (in_array(self::$thor_env, ['dev', 'debug'])) {
                echo "<pre>";
                throw $e;
            }
        }
        Logger::write('APP_END ### APP_END', Logger::DEV);
    }

}
