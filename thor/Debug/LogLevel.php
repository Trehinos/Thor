<?php

namespace Thor\Debug;

use Thor\Env;

enum LogLevel: int
{
    case INFO = 0;
    case NOTICE = 1;
    case DEBUG = 2;
    case WARNING = 3;
    case ERROR = 4;
    case CRITICAL = 5;
    case ALERT = 6;
    case EMERGENCY = 7;

    public static function fromEnv(Env $env): self
    {
        return match ($env) {
            Env::DEV => self::INFO,
            Env::DEBUG => self::DEBUG,
            Env::PROD => self::ERROR
        };
    }
}
