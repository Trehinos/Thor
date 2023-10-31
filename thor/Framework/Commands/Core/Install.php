<?php

namespace Thor\Framework\Commands\Core;

use Thor\Debug\Logger;
use Thor\Cli\Command\Command;
use Thor\Database\PdoExtension\Migrator;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Install extends Command
{

    public function execute(): void
    {
        $setup = new Setup('setup');
        $setup->execute();

        Logger::write('Migrate database...', print: true);
        $migrator = Migrator::createFromConfiguration();
        $migrator->migrate(null);
    }

}
