<?php

namespace Thor\Framework\Commands\Core;

use Thor\Globals;
use Thor\Debug\Logger;
use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\FileSystem\Folder;
use Thor\Process\Command;
use Thor\Framework\Security\DbUser;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\Driver\MySql;
use Thor\Database\PdoTable\SchemaHelper;
use Thor\Framework\Managers\UserManager;
use Thor\Database\PdoTable\Driver\Sqlite;
use Thor\Database\PdoExtension\PdoMigrator;
use Thor\Database\PdoExtension\PdoRequester;

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
