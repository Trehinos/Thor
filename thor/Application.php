<?php

namespace Thor;

use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Throwable;
use Thor\Configuration\Configuration;
use Thor\Configuration\ThorConfiguration;
use Thor\Configuration\KernelsConfiguration;

/**
 * Main class of the framework. It loads the global configuration, sets the logger level
 * and executes the kernel corresponding the calling entry point.
 *
 * @package          Thor
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Application implements KernelInterface
{

    public function __construct(private ?KernelInterface $kernel = null)
    {
    }

    /**
     * Creates the application with the configuration.
     *
     * @return static
     */
    public static function create(): static
    {
        global $thor_kernel;
        $config = new ThorConfiguration($thor_kernel);
        return self::createFromConfiguration($config);
    }

    /**
     * Creates the application with given configuration.
     *
     * @param ThorConfiguration $config
     *
     * @return static
     */
    public static function createFromConfiguration(Configuration $config): static
    {
        $kernel = $config->thorKernel() ?? '';
        Application::setLoggerLevel(
            LogLevel::fromEnv($config->env()) ?? LogLevel::DEBUG,
            Globals::VAR_DIR . ($config->logPath()) . "{$kernel}/"
        );
        return new self(Application::getKernel($config->thorKernel()));
    }

    /**
     * Sets the static logger level.
     *
     * @param LogLevel $logLevel
     * @param string   $logPath
     *
     * @return void
     */
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

    /**
     * Gets the kernel corresponding the $thor_kernel string.
     *
     * @param string|null $thor_kernel
     *
     * @return KernelInterface|null
     */
    public static function getKernel(?string $thor_kernel = null): ?KernelInterface
    {
        if (null !== $thor_kernel) {
            foreach (KernelsConfiguration::get() as $kernelName => $kernelClass) {
                if ($kernelName === $thor_kernel) {
                    if (!class_exists($kernelClass)) {
                        return null;
                    }
                    return $kernelClass::create();
                }
            }
        }

        return null;
    }

    /**
     * Executes the kernel.
     *
     * This function catches any Throwable not cached by a nested function to log it and displays it if
     *
     * `Thor::isDev() === true`
     *
     * @return void
     */
    public function execute(): void
    {
        try {
            $this->kernel?->execute();
        } catch (Throwable $e) {
            Logger::logThrowable($e);
            echo "UNRECOVERABLE ERROR THROWN";
            global $thor_kernel;
            $message = " : {$e->getMessage()}";
            echo ($thor_kernel === 'web')
                ? "<strong style='font-family: monospace;'>$message</strong><br>"
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
                echo $thor_kernel === 'web' ? '<pre>' : '';
                echo $traceStr;
                echo $thor_kernel === 'web' ? '</pre>' : '';
            }
        }
    }

}
