<?php

namespace Thor\Cli\Daemon;

use Thor\Debug\ThorException;

class DaemonException extends ThorException
{

    private string $name;

    public function __construct(Daemon $daemon, string $message, int $errorCode = 0)
    {
        $this->name = $daemon->getName();
        parent::__construct($errorCode, "DaemonException [{name}:{code}] : $message", ['name' => $this->getDaemonName()]);
    }

    public function getDaemonName(): string
    {
        return $this->name;
    }

    public static function notFound(Daemon $daemon): self
    {
        return new self($daemon, "Daemon file not found", 1);
    }

}
