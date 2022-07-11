<?php

namespace Thor\Framework\Commands\Core;

use Thor\Process\Command;
use Thor\Cli\Console\Color;
use Thor\Database\PdoExtension\PdoMigrator;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Migrate extends Command
{

    public function execute(): void
    {
        $index = $this->get('index');
        $migrator = PdoMigrator::createFromConfiguration();
        $index = $migrator->migrate($index);
        $this->console->echoes(
            Color::FG_GREEN,
            "Done",
            Color::FG_GRAY,
            " : migrated to index ",
            Color::FG_YELLOW,
            $index,
            Color::FG_GRAY,
            ".\n"
        );
    }

}
