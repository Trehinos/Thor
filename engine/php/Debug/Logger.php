<?php

namespace Thor\Debug;

final class Logger
{

    private string $env;
    private string $basePath;
    private string $dateFormat;
    private ?string $filename = null;

    const DEV = 0;
    const DEBUG = 1;
    const VERBOSE = 2;
    const PROD = 3;

    private const LEVELS = [
        'DEV' => self::DEV,
        'DEBUG' => self::DEBUG,
        'VERBOSE' => self::VERBOSE,
        'PROD' => self::PROD,
    ];

    public function __construct(
        string $env = 'DEV',
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ) {
        $this->env = strtoupper($env);
        $this->basePath = $basePath;
        $this->dateFormat = $dateFormat;
    }

    public function log(string $message, int $level = 0): self
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
            $message = "$nowStr $env : $message";

            if (null === $this->filename) {
                $nowFileName = $now->format('Ymd');
                $this->filename = "{$this->basePath}{$this->env}_{$nowFileName}.log";
            }

            file_put_contents($this->filename, "$message\n", FILE_APPEND);
        }

        return $this;
    }

    private static ?self $logger = null;

    public static function getDefaultLogger(
        string $env = 'DEV',
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger ??= new self($env, $basePath, $dateFormat);
    }

    public static function write(string $message, int $level = 0)
    {
        self::getDefaultLogger()->log($message, $level);
    }

}
