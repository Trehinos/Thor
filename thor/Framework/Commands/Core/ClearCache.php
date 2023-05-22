<?php

namespace Thor\Framework\Commands\Core;

use Thor\Cli\Command\Command;
use Thor\Cli\Console\Color;
use Thor\Common\FileSystem\Folder;
use Thor\Framework\Globals;

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
