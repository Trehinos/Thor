<?php

namespace Thor\Framework\Commands\Core;

use Thor\Cli\Command\Command;
use Thor\Cli\Console\Color;
use Thor\Common\Debug\Logger;
use Thor\Common\FileSystem\Folder;
use Thor\Framework\Globals;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class ClearLogs extends Command
{

    public function execute(): void
    {
        $env = strtoupper($this->get('env', 'dev'));
        $ttl = $this->get('ttl');

        if ($ttl !== null) {
            $this->error("The --ttl option is not supported yet. Please remove it form the command line...");
        }

        $this->console->fColor(Color::CYAN)
                      ->writeln("Clearing the $env logs...")
                      ->mode()
        ;
        $deleted = Folder::removeTree(
            Globals::VAR_DIR . 'logs',
            ".*[.]log",
            false,
            false,
            fn(string $path) => str_contains($path, $env)
        );
        foreach ($deleted as $file) {
            $this->console->writeln(" - $file deleted.");
        }
        Logger::write("Log cleared");
        $this->console->writeln(" -> Done");
    }

}
