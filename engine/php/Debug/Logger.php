<?php

namespace Thor\Debug;

final class Logger
{

    private string $env;
    private string $basePath;
    private string $dateFormat;
    private ?string $filename;

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

    const NOTICE = 0;
    const WARNING = 1;
    const ERROR = 2;

    private const SEVERITY = [
        self::NOTICE => '',
        self::WARNING => 'W',
        self::ERROR => 'ERR',
    ];

    /**
     * Logger constructor.
     *
     * @param string $env
     * @param string $basePath
     * @param string $dateFormat
     * @param string|null $filename
     */
    public function __construct(
        string $env = 'DEV',
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v',
        ?string $filename = null
    ) {
        $this->env = strtoupper($env);
        $this->basePath = $basePath;
        $this->dateFormat = $dateFormat;
        $this->filename = $filename;
    }

    /**
     * log(): writes log message in the log file if level is over the default log level.
     *
     * @param string $message
     * @param int $level
     * @param int $severity
     *
     * @return $this
     */
    public function log(string $message, int $level = self::DEBUG, int $severity = self::NOTICE): self
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

            file_put_contents($this->filename, "$message\n", FILE_APPEND);
        }

        return $this;
    }

    private static ?self $logger = null;

    /**
     * getDefaultLogger(): returns the static logger or create one with specified parameters.
     *
     * @param string $env
     * @param string $basePath
     * @param string $dateFormat
     *
     * @return self
     */
    public static function getDefaultLogger(
        string $env = 'DEV',
        string $basePath = __DIR__ . '/../',
        string $dateFormat = 'Y-m-d H:i:s.v'
    ): self {
        return self::$logger ??= new self($env, $basePath, $dateFormat);
    }

    /**
     * logThrowable(): logs an exception with the static logger.
     *
     * @param \Throwable $e
     */
    public static function logThrowable(\Throwable $e)
    {
        $pad = str_repeat(' ', 37);
        $traceStr = '';

        foreach ($e->getTrace() as $trace) {
            $traceStr .= "$pad  • Location : {$trace['file']}:{$trace['line']}\n$pad    Function : {$trace['function']}\n";
        }

        $message = <<<EOT
            ERROR THROWN IN FILE {$e->getFile()} LINE {$e->getLine()} : {$e->getMessage()}
            $pad Trace :
            $traceStr                 
            EOT;
        self::write($message, Logger::DEBUG, Logger::ERROR);
    }

    /**
     * log(): writes log message with the static logger if level is over the static log level.
     *
     * @param string $message
     * @param int $level
     * @param int $severity
     */
    public static function write(string $message, int $level = self::DEV, int $severity = self::NOTICE)
    {
        self::getDefaultLogger()->log($message, $level, $severity);
    }

}
