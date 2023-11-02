<?php

namespace App;

use Thor\Cli\Command\Command;
use Thor\Process\Dispatcher;

final class Test extends Command
{
    public function execute(): void
    {
        $dispatcher = new Dispatcher();
        $dispatcher->on('start', fn () => $this->console->writeln('START'));
        $dispatcher->on('count', fn (int $n) => $this->console->writeln("COUNT $n"));
        $dispatcher->on('mul', fn (int $n, int $n2) => $this->console->writeln('MULTIPLE ' . ($n * $n2)));
        $dispatcher->on('end', fn () => $this->console->writeln('END'));

        $dispatcher->trigger('start');
        foreach (range(1, 4) as $n) {
            $dispatcher->trigger('count', $n);
            foreach (range(1, 4) as $n2) {
                $dispatcher->trigger('mul', $n, $n2);
            }
        }
        $dispatcher->trigger('end');
    }
}
