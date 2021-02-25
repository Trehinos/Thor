<?php

namespace Thor;

use JetBrains\PhpStorm\ExpectedValues;
use Thor\Cli\CliKernel;
use Thor\Cli\DaemonScheduler;
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
        try {
            $this->kernel?->execute();
        } catch (Throwable $e) {
            Logger::logThrowable($e);
            echo "UNRECOVERABLE ERROR THROWN";
            global $thor_kernel;
            $message = " : {$e->getMessage()}";
            echo ('http' === $thor_kernel) ? "<strong style='font-family: monospace;'>$message</strong><br>" : "$message\n";
            if (in_array(Thor::getEnv(), ['dev', 'debug'])) {
                $traceStr = '';
                foreach ($e->getTrace() as $trace) {
                    $traceLine = " • Location : {$trace['file']}:{$trace['line']}\n   -> Function : {$trace['function']}\n";
                    if ('http' === $thor_kernel) {
                        $traceLine = nl2br($traceLine);
                    }
                    $traceStr .= $traceLine;
                }
                echo $traceStr;
            }
        }
    }

    public static function setLoggerLevel(
        #[ExpectedValues(['dev', 'debug', 'verbose', 'prod'])]
        string $thor_env,
        string $logPath
    ): void {
        Logger::setDefaultLogger($thor_env, $logPath);

        if ('dev' === $thor_env) {
            ini_set('display_errors', E_ALL);
        } elseif ('debug' === $thor_env) {
            ini_set('display_errors', E_ERROR | E_WARNING | E_PARSE);
        } else {
            ini_set('display_errors', 0);
        }
        ini_set('date.timezone', $config['timezone'] ?? 'Europe/Paris');
    }

    public static function getKernel(
        ?string $thor_kernel = null
    ): ?KernelInterface {
        $kernel = null;

        // STATIC KERNELS HERE
        switch ($thor_kernel ?? null) {
            case 'http':
                $kernel = HttpKernel::create();
                break;

            case 'cli':
                $kernel = CliKernel::create();
                break;

            case 'daemon':
                $kernel = DaemonScheduler::create();
                break;

            default:
                $thor_kernel ??= '(null)';
                Logger::write(
                    "PANIC ABORT : kernel $thor_kernel not defined.",
                    Logger::LEVEL_PROD,
                    Logger::SEVERITY_ERROR
                );
                echo "Error :\nKernel not selected.\n";
                exit;
        }


        return $kernel;
    }

    public static function createFromConfiguration(array $config = []): static
    {
        Application::setLoggerLevel(
            in_array(
                $env = strtolower($config['env'] ?? ''),
                ['dev', 'debug', 'verbose', 'prod']
            ) ? $env : 'debug',
            Globals::VAR_DIR . ($config['log_path'] ?? '')
        );

        global $thor_kernel;
        return new self(Application::getKernel($thor_kernel ?? ''));
    }

}
