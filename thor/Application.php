<?php

namespace Thor;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use Thor\Cli\CliKernel;
use Thor\Debug\Logger;
use Thor\Http\HttpKernel;
use Throwable;

final class Application implements KernelInterface
{

    public function __construct(private ?KernelInterface $kernel = null)
    {
    }

    public function execute(): void
    {
        $this->kernel?->execute();
    }

    public static function init(
        #[ExpectedValues(['DEV', 'DEBUG', 'VERBOSE', 'PROD'])]
        string $thor_env,
        string $logPath
    ): void {
        Logger::getDefaultLogger($thor_env, Globals::CODE_DIR . $logPath);

        if ('PROD' === $thor_env) {
            ini_set('display_errors', 0);
        } elseif ('DEBUG' === $thor_env) {
            ini_set('display_errors', E_ERROR | E_WARNING | E_PARSE);
        } else {
            ini_set('display_errors', E_ALL);
        }
        ini_set('date.timezone', $config['timezone'] ?? 'Europe/Paris');
    }

    public static function getKernel(
        ?string $thor_kernel = null,
        #[ArrayShape(['databases' => 'array', 'config' => 'array'])]
        array $config = []
    ): ?KernelInterface {
        try {
            $kernel = null;
            switch ($thor_kernel ?? null) {
                case 'http':
                    $kernel = HttpKernel::create($config);
                    break;

                case 'cli':
                    $kernel = CliKernel::create($config);
                    break;

                default:
                    $thor_kernel ??= '(null)';
                    Logger::write("PANIC ABORT : kernel $thor_kernel not defined.", Logger::LEVEL_PROD, Logger::SEVERITY_ERROR);
                    echo "Error :\nKernel not selected.\n";
                    exit;
            }
        } catch (Throwable $e) {
            Logger::logThrowable($e);
            echo "UNRECOVERABLE ERROR THROWN\n";
        }

        return $kernel;
    }

}
