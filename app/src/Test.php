<?php

namespace App;

use Thor\Cli\Command\Command;
use Thor\Tools\Timeline;

final class Test extends Command
{
    public function execute(): void
    {
        $timeline = new Timeline();
        sleep(1);
        $timeline->mark("After one second...");
        usleep(40000);
        $timeline->mark("After 40 ms...");
        $timeline->mark("Just next !");

        $timeline->dumpCli();
    }
}
