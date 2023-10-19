<?php

namespace Thor\Framework\Commands\Core;

use Thor\Globals;
use Thor\Cli\Console\Color;
use Thor\FileSystem\Folders;
use Thor\Cli\Command\Command;

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
        $deleted = Folders::removeTree(Globals::VAR_DIR . 'cache', removeRoot: false);
        foreach ($deleted as $file) {
            $this->console->writeln(" - $file deleted.");
        }
        $this->console->writeln(" -> Done");
    }

}
