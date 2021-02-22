<?php

namespace Thor\Debug;

use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Pure;
use Thor\FileSystem\Folder;

final class Logger
{

    private string $env;

    public const LEVEL_DEV = 0;
    public const LEVEL_DEBUG = 1;
    public const LEVEL_VERBOSE = 2;
    public const LEVEL_PROD = 3;

    private const LEVELS = [
        'dev' => self::LEVEL_DEV,
        'debug' => self::LEVEL_DEBUG,
        'verbose' => self::LEVEL_VERBOSE,
        'prod' => self::LEVEL_PROD,
    ];

    public const SEVERITY_NOTICE = 0;
    public const SEVERITY_WARNING = 1;
    public const SEVERITY_ERROR = 2;

    private const SEVERITY = [
        self::SEVERITY_NOTICE => '',
        self::SEVERITY_WARNING => 'W',
        self::SEVERITY_ERROR => 'ERR',
    ];

    #[Pure]
    public function __construct(
        #[ExpectedValues(['dev', 'debug', 'verbose', 'prod'])]
        string $env = 'dev',
        private string $basePath = __DIR__ . '/../',
        private string $dateFormat = 'Y-m-d H:i:s.v',
        private ?string $filename = null
    ) {
        $this->env = strtolower($env);
    }

    public function log(string $message, int $level = self::LEVEL_DEBUG, int $severity = self::SEVERITY_NOTICE): self
    {
        if ($level >= self::LEVELS[$this->env]) {
            $env = str_pad(
                array_search($level, self::LEVELS),
                7,
                ' ',
                STR_PAD_RIGHT
            );
            $now = new \DateTime();
            $nowStr = $now->format($this->dateFormat);
            $sev = str_pad(self::SEVERITY[$severity], 3, ' ', STR_PAD_LEFT);
            $message = "$nowStr $env $sev: $message";

            if (null === $this->filename) {
                $nowFileName = $now->format('Ymd');
                $this->filename = "{$this->basePath}{$this->env}_{$nowFileName}.log";
            }

            Folder::createIfNotExists(dirname($this->filename));
            file_put_contents($this->filename, "$message\n", FILE_APPEND);
        }

        return $this;
    }

    private static ?self $logger = null;

    public static function getDefaultLogger(
        #[ExpectedValues(['dev', 'debug', 'verbose', 'prod'])]
        string $env = 'dev',
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger ??= new self($env, $basePath, $dateFormat);
    }

    public static function setDefaultLogger(
        #[ExpectedValues(['dev', 'debug', 'verbose', 'prod'])]
        string $env = 'dev',
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger = new self($env, $basePath, $dateFormat);
    }

    public static function logThrowable(\Throwable $e): string
    {
        $pad = str_repeat(' ', 37);
        $traceStr = '';

        foreach ($e->getTrace() as $trace) {
            $traceStr .= "$pad  • Location : {$trace['file']}:{$trace['line']}\n$pad    Function : {$trace['function']}\n";
        }

        self::write(
            "ERROR THROWN IN FILE {$e->getFile()} LINE {$e->getLine()} : {$e->getMessage()}",
            Logger::LEVEL_PROD,
            Logger::SEVERITY_ERROR
        );
        $message = <<<EOT
            $pad Trace :
            $traceStr                 
            EOT;
        self::write($message, Logger::LEVEL_DEBUG, Logger::SEVERITY_ERROR);

        return $message;
    }

    public static function write(
        string $message,
        int $level = self::LEVEL_DEV,
        int $severity = self::SEVERITY_NOTICE
    ): void {
        self::getDefaultLogger()->log($message, $level, $severity);
    }

    public static function writeData(
        string $dataName,
        array $data,
        int $level = self::LEVEL_DEV,
        int $severity = self::SEVERITY_NOTICE
    ): void {
        $message = "DATA:$dataName= " . json_encode($data);
        self::getDefaultLogger()->log($message, $level, $severity);
    }

}
