<?php

namespace Thor\Framework\CliCommands\Core;

use Thor\Globals;
use Thor\Cli\Console\Color;
use Thor\FileSystem\Folder;
use Thor\Process\Command;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class ClearCache extends Command
{

    public function execute(): void
    {
        $this->console->fColor(Color::CYAN)
                      ->writeln('Clearing the cache...')
                      ->mode()
        ;
        $deleted = Folder::removeTree(Globals::VAR_DIR . 'cache', removeFirst: false);
        foreach ($deleted as $file) {
            $this->console->writeln(" - $file deleted.");
        }
        $this->console->writeln(" -> Done");
    }

}
