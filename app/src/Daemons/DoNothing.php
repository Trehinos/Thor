<?php

namespace App\Daemons;

use Thor\Cli\Daemon;

final class DoNothing extends Daemon
{

    public function execute(): void
    {
    }

}
