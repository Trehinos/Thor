<?php

namespace App\Daemons;

use DateTime;
use Thor\Cli\Daemon;

final class Dummy extends Daemon
{

    public function execute(): void
    {
        $now = (new DateTime())->format('Ymd His');
        echo "$now I'm now waiting 6 minutes...\n";
        sleep(360);
    }

}
