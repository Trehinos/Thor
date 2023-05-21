<?php

namespace Thor\Framework\Commands\Core;

use Exception;
use Thor\Common\Debug\Logger;
use Thor\Cli\Command\Command;
use Thor\Cli\Console\{Mode, Color};
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Framework\{Security\DbUser, Managers\UserManager};
use Thor\Database\PdoTable\{CrudHelper, Driver\MySql, SchemaHelper, Driver\Sqlite};

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Setup extends Command
{

    /**
     * @throws Exception
     */
    public function execute(): void
    {
        $databaseName = $this->get('database') ?? 'default';
        $requester = new PdoRequester($handler = $this->kernel->getHandler($databaseName));

        $driver = match ($driverName = $handler->getDriverName()) {
            'sqlite' => new Sqlite(),
            'mysql' => new MySql(),
            default => throw new Exception("Unsupported driver '$driverName' for PdoTable...")
        };
        Logger::write("SETUP : Creating table user...");
        $schema = new SchemaHelper($requester, $driver, DbUser::class);
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
