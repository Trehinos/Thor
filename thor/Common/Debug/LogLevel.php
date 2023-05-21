<?php

namespace Thor\Common\Debug;

use Thor\Env;

/**
 * * `INFO` (dev) : Interesting events.
 * * `NOTICE` (dev) : Normal but significant events.
 * * `DEBUG` : Detailed debug information.
 * * `WARNING` : Exceptional occurrences that are not errors.
 * * `ERROR` : Runtime errors to be logged
 * * `CRITICAL` : Critical condition. Application component unavailable, unexpected exception.
 * * `ALERT` : Action must be taken immediately. Example: Entire website down, database unavailable, etc. This should
 * trigger the SMS alerts and wake you up.
 * * `EMERGENCY` : System is unusable.
 *
 */
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

    public const MINIMAL = self::INFO;

    public const MAXIMAL = self::EMERGENCY;

    /**
     * Returns **minimum** LogLevel according to Env :
     *  - DEV --> INFO
     *  - DEBUG --> DEBUG
     *  - PROD --> ERROR.
     *
     * @param Env $env
     *
     * @return static
     */
    public static function fromEnv(Env $env): self
    {
        return match ($env) {
            Env::DEV   => self::INFO,
            Env::DEBUG => self::DEBUG,
            Env::PROD  => self::ERROR
        };
    }

}
