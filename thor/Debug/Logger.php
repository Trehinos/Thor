<?php

/**
 * @package          Trehinos/Thor/Debug
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Debug;

use Thor\Thor;
use Throwable;
use JsonException;
use Thor\FileSystem\Folder;

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

    public static function setDefaultLogger(
        LogLevel $level = LogLevel::DEBUG,
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger = new self($level, $basePath, $dateFormat);
    }

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

    public static function write(
        string $message,
        LogLevel $level = LogLevel::INFO,
        array $context = [],
        bool $print = false
    ): void {
        self::get()->log($level, $message, $context);
        if ($print) {
            echo self::interpolate($message, $context) . "\n";
        }
    }

    /**
     * @inheritDoc
     */
    public function log(LogLevel $level, string $message, array $context = []): void
    {
        if ($level->value >= $this->logLevel->value) {
            $strLevel = str_pad($level->name, 10);
            $now = new \DateTime();
            $nowStr = $now->format($this->dateFormat);
            $message = "$nowStr $strLevel : " . self::interpolate($message, $context);

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

    public static function get(
        LogLevel $level = LogLevel::INFO,
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger ??= new self($level, $basePath, $dateFormat);
    }

    public static function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }

    /**
     * @inheritDoc
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @throws JsonException
     */
    public static function writeData(string $dataName, array $data, LogLevel $level = LogLevel::INFO): void
    {
        $message = "DATA:$dataName= " . json_encode($data, JSON_THROW_ON_ERROR);
        self::get()->log($level, $message);
    }
}
