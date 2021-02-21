<?php

namespace App\Daemons;

use Thor\Cli\Daemon;

final class ErrorTest extends Daemon
{

    public function execute(): void
    {
        throw new \Exception('Thrown Exception message');
    }

}
