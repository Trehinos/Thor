<?php

namespace Thor\Framework\Commands\Core;

use Thor\Database\PdoExtension\PdoMigrator;
use Thor\Debug\Logger;
use Thor\Process\Command;

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
        $migrator = PdoMigrator::createFromConfiguration();
        $migrator->migrate(null);
    }

}
