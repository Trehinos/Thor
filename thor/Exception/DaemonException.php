<?php

namespace Thor\Exception;

use Thor\Cli\Daemon\Daemon;

class DaemonException extends \Exception
{

    private string $name;

    public function __construct(Daemon $daemon, string $message, int $errorCode = 0) {
        $this->name = $daemon->getName();
        parent::__construct("DaemonException [{$this->name}:{$errorCode}] : $message", $errorCode);
    }

    public function getDaemonName(): string
    {
        return $this->name;
    }

}
