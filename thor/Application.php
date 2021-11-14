<?php

/**
 * @package          Trehinos/Thor
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor;

use Throwable;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;

final class Application implements KernelInterface
{

    public function __construct(private ?KernelInterface $kernel = null)
    {
    }

    public static function create(): static
    {
        global $thor_kernel;
        $config = Thor::config('config');
        return self::createFromConfiguration(['thor_kernel' => $thor_kernel] + $config);
    }

    public static function createFromConfiguration(array $config = []): static
    {
        Application::setLoggerLevel(
            LogLevel::fromEnv(Env::tryFrom(strtoupper($config['env']))) ?? LogLevel::DEBUG,
            Globals::VAR_DIR . ($config['log_path'] ?? '')
        );
        return new self(Application::getKernel($config['thor_kernel'] ?? ''));
    }

    public static function setLoggerLevel(LogLevel $logLevel, string $logPath): void
    {
        Logger::setDefaultLogger($logLevel, $logPath);
        if (LogLevel::INFO === $logLevel) {
            ini_set('display_errors', E_ALL);
        } elseif (LogLevel::DEBUG === $logLevel) {
            ini_set('display_errors', E_ERROR | E_WARNING | E_PARSE);
        } else {
            ini_set('display_errors', 0);
        }
        ini_set('date.timezone', $config['timezone'] ?? 'Europe/Paris');
    }

    public static function getKernel(?string $thor_kernel = null): ?KernelInterface
    {
        $kernel = null;

        if (null !== ($thor_kernel ?? null)) {
            foreach (Thor::config('kernels', true) as $kernelName => $kernelClass) {
                if ($kernelName === $thor_kernel) {
                    if (!class_exists($kernelClass)) {
                        return null;
                    }
                    $kernel = $kernelClass::create();
                    break;
                }
            }
        }

        return $kernel;
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
            echo ('http' === $thor_kernel) ? "<strong style='font-family: monospace;'>$message</strong><br>"
                : "$message\n";
            if (in_array(Thor::getEnv(), [Env::DEV, Env::DEBUG])) {
                $traceStr = '';
                foreach ($e->getTrace() as $trace) {
                    $traceLine =
                        " • Location : {$trace['file']}:{$trace['line']}\n   -> Function : {$trace['function']}\n";
                    if ('http' === $thor_kernel) {
                        $traceLine = nl2br($traceLine);
                    }
                    $traceStr .= $traceLine;
                }
                echo $traceStr;
            }
        }
    }

}
