<?php

namespace Thor\Debug;

use Psr\Log\LogLevel as PsrLogLevel;
use Thor\Env;

class LogLevel extends PsrLogLevel
{
    /**
     * Returns **minimum** LogLevel according to En :
     *  - DEV --> INFO
     *  - DEBUG --> DEBUG
     *  - PROD --> ERROR.
     *
     * @param Env $env
     *
     * @return string
     */
    public static function fromEnv(Env $env): string
    {
        return match ($env) {
            Env::DEV   => self::INFO,
            Env::DEBUG => self::DEBUG,
            Env::PROD  => self::ERROR
        };
    }
}
