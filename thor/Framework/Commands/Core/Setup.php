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
use Thor\Database\PdoExtension\PdoRequester;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Setup extends Command
{

    public function execute(): void
    {
        $databaseName = $this->get('database') ?? 'default';
        $requester = new PdoRequester($handler = $this->kernel->getHandler($databaseName));

        $driver = match ($driverName = $handler->getDriverName()) {
            'sqlite' => new Sqlite(),
            'mysql' => new MySql(),
            default => throw new \Exception("Unsupported driver '$driverName' for PdoTable...")
        };
        Logger::write("SETUP : Creating table user...");
        $schema = new SchemaHelper($requester, $driver, DbUser::class);
        $this->console->write('Droping table ')
                      ->fColor(Color::BLUE, Mode::BRIGHT)->write('user')
                      ->mode()->writeln('...');
        $schema->dropTable();
        $this->console->write('Creating table ')
                      ->fColor(Color::BLUE, Mode::BRIGHT)->write('user')
                      ->mode()->writeln('...');
        $schema->createTable();

        $this->console->write('Creating user ')
                      ->fColor(Color::BLUE, Mode::BRIGHT)->write('admin')
                      ->mode()->writeln('...');
        $userManager = new UserManager(new CrudHelper(DbUser::class, $requester));
        $pid =
            $userManager->createUser(
                'admin',
                'password',
                ['manage-user', 'create-user', 'edit-user', 'remove-user', 'manage-permissions']
            );
        Logger::write("SETUP : Admin $pid created.");
    }

}
