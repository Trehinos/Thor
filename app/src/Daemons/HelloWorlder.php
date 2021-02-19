<?php

namespace App\Daemons;

use DateTime;
use Thor\Cli\Daemon;

final class HelloWorlder extends Daemon
{

    public function execute(): void
    {
        $now = (new DateTime())->format('Ymd His');
        echo "$now Hello world\n";
    }

}
