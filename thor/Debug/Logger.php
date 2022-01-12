<?php

namespace Thor\Debug;

use Thor\Thor;
use Throwable;
use Stringable;
use JsonException;
use Thor\Tools\Strings;
use Thor\FileSystem\Folder;

/**
 * Logger class for Thor.
 * Provides a static context to have a unique global Logger object.
 *
 * @package          Thor/Debug
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Logger implements LoggerInterface
{

    private static ?self $logger = null;

    public function __construct(
        private LogLevel $logLevel = LogLevel::DEBUG,
        private string $basePath = __DIR__ . '/../',
        private string $dateFormat = 'Y-m-d H:i:s.v',
        private ?string $filename = null,
    ) {
    }

    /**
     * Sets the static Logger.
     */
    public static function setDefaultLogger(
        LogLevel $level = LogLevel::DEBUG,
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger = new self($level, $basePath, $dateFormat);
    }

    /**
     * Logs a Throwable : the message is written with an LogLevel::ERROR level.
     * The details are written with the LogLevel::DEBUG level.
     */
    public static function logThrowable(Throwable $e): string
    {
        $pad = str_repeat(' ', 37);
        $traceStr = '';

        foreach ($e->getTrace() as $trace) {
            $traceStr .= "$pad  • Location : {$trace['file']}:{$trace['line']}\n$pad    Function : {$trace['function']}\n";
        }

        self::write(
            "ERROR THROWN IN FILE {$e->getFile()} LINE {$e->getLine()} : {$e->getMessage()}",
            LogLevel::ERROR
        );
        $message = <<<EOT
            $pad Trace :
            $traceStr                 
            EOT;
        self::write($message, LogLevel::DEBUG);

        return $message;
    }

    /**
     * Writes a message with the static Logger.
     */
    public static function write(
        Stringable|string $message,
        LogLevel $level = LogLevel::INFO,
        array $context = [],
        bool $print = false
    ): void {
        self::get()->log($level, $message, $context);
        if ($print) {
            echo Strings::interpolate($message, $context) . "\n";
        }
    }

    /**
     * @inheritDoc
     */
    public function log(LogLevel $level, Stringable|string $message, array $context = []): void
    {
        if ($level->value >= $this->logLevel->value) {
            $strLevel = str_pad($level->name, 10);
            $now = new \DateTime();
            $nowStr = $now->format($this->dateFormat);
            $message = "$nowStr $strLevel : " . Strings::interpolate($message, $context);

            if (null === $this->filename) {
                $nowFileName = $now->format('Ymd');
                $thorEnv = Thor::getEnv()->value;
                $this->filename = "{$this->basePath}{$thorEnv}_{$nowFileName}.log";
            }

            try {
                Folder::createIfNotExists(dirname($this->filename));
                file_put_contents($this->filename, "$message\n", FILE_APPEND);
            } catch (Throwable $t) {
            }
        }
    }

    /**
     * Gets the current static Logger.
     */
    public static function get(
        LogLevel $level = LogLevel::INFO,
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger ??= new self($level, $basePath, $dateFormat);
    }

    /**
     * Writes data encoded into JSON with the static Logger.
     */
    public static function writeData(string $dataName, mixed $data, LogLevel $level = LogLevel::INFO): void
    {
        try {
            $message = "DATA:$dataName= " . json_encode($data, JSON_THROW_ON_ERROR);
            self::get()->log($level, $message);
        } catch (JsonException) {
        }
    }

    /**
     * @inheritDoc
     */
    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
