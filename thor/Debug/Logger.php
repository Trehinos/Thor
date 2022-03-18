<?php

namespace Thor\Debug;

use Thor\Thor;
use Throwable;
use Stringable;
use JsonException;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
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
final class Logger extends AbstractLogger
{
    const LEVELS = [
        LogLevel::DEBUG     => 100,
        LogLevel::INFO      => 200,
        LogLevel::NOTICE    => 250,
        LogLevel::WARNING   => 300,
        LogLevel::ERROR     => 400,
        LogLevel::CRITICAL  => 500,
        LogLevel::ALERT     => 550,
        LogLevel::EMERGENCY => 600
    ];

    private static ?self $logger = null;

    public function __construct(
        private string $logLevel = LogLevel::DEBUG,
        private string $basePath = __DIR__ . '/../',
        private string $dateFormat = 'Y-m-d H:i:s.v',
        private ?string $filename = null,
    ) {
    }

    /**
     * Sets the static Logger.
     */
    public static function setDefaultLogger(
        string $level = LogLevel::DEBUG,
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
        string $level = LogLevel::INFO,
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
    public function log($level, Stringable|string $message, array $context = []): void
    {
        if ($this->shouldLogMessage($level)) {
            $strLevel = str_pad($level, 10);
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

    private function shouldLogMessage(string $level): bool
    {
        return self::LEVELS[$level] >= self::LEVELS[$this->logLevel];
    }

    /**
     * Gets the current static Logger.
     */
    public static function get(
        string $level = LogLevel::INFO,
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger ??= new self($level, $basePath, $dateFormat);
    }

    /**
     * Writes data encoded into JSON with the static Logger.
     */
    public static function writeData(string $dataName, mixed $data, string $level = LogLevel::INFO): void
    {
        try {
            $message = "DATA:$dataName= " . json_encode($data, JSON_THROW_ON_ERROR);
            self::get()->log($level, $message);
        } catch (JsonException) {
        }
    }
}
