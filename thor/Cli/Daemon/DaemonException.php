<?php

namespace Thor\Cli\Daemon;

use Throwable;
use Thor\Debug\ThorException;

class DaemonException extends ThorException
{

    public const NOT_FOUND = 0B0000_0001;
    public const THROWN_IN_DAEMON = 0B1000_0000;

    private string $name;

    public function __construct(
        string $message,
        ?Daemon $daemon = null,
        int $errorCode = 0,
        ?Throwable $previous = null
    ) {
        $this->name = $daemon->getName();
        parent::__construct(
            $errorCode,
            "DaemonException [{name}:{code}] : $message",
            ['name' => $this->getDaemonName() ?? ''],
            $previous
        );
    }

    public function getDaemonName(): string
    {
        return $this->name;
    }

    public static function notFound(string $name): self
    {
        return new self("Daemon $name not found", errorCode: self::NOT_FOUND);
    }

    public static function in(Throwable $throwable, Daemon $daemon, string $message = ""): self
    {
        return new self($message, $daemon, self::THROWN_IN_DAEMON, $throwable);
    }

}
