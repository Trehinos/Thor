<?php

namespace Thor\Framework\CliCommands;

use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Process\Command;
use Thor\Cli\Console\Console;
use Thor\Cli\Console\FixedOutput;
use Thor\Configuration\ThorConfiguration;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Help extends Command
{


    /**
     * @return void
     */
    public function execute(): void
    {
        $console = new Console();
        $command = $this->get('command');
        $commands = $this->kernel->getCommands();
        $config = ThorConfiguration::get();

        if ($command === null) {
            $console->echoes(
                Mode::BRIGHT,
                Color::FG_RED,
                new FixedOutput('', 21),
                $config->appVendor() . '/' . $config->appName() . ' '
            );
            $console->echoes(Mode::BRIGHT, Color::FG_YELLOW, $config->appVersion());
            $console->writeln()->writeln();
        }

        foreach ($commands as $cliCommand) {
            if ($command) {
                if ($command === $cliCommand->command) {
                    $cliCommand->usage();
                }
            } else {
                $cliCommand->usage(false);
            }
        }
        $console->writeln();
    }

}
