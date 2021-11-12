<?php

/**
 * @package          Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Api\Commands;

use Thor\Cli\Daemon;
use Thor\Debug\LogLevel;
use Thor\Cli\DaemonState;
use Thor\FileSystem\Folder;
use Thor\Api\Managers\UserManager;
use Symfony\Component\Yaml\Yaml;
use Thor\Cli\CliKernel;
use Thor\Cli\Command;
use Thor\Cli\Console;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoExtension\PdoMigrator;
use Thor\Database\PdoTable\Attributes\PdoAttributesReader;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\SchemaHelper;
use Thor\Debug\Logger;
use Thor\Globals;
use Thor\Http\HttpKernel;
use Thor\Http\Routing\Route;
use Thor\Api\Entities\User;
use Thor\Thor;

final class CoreCommand extends Command
{

    private array $routes;

    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
        $this->routes =
            HttpKernel::createRouterFromConfiguration(
                Thor::config('routes', true)
            )->getRoutes()
        ;
    }

    public function routeSet()
    {
        $name = $this->get('name');
        $path = $this->get('path');
        $method = $this->get('method');
        $cClass = $this->get('action-class');
        $cMethod = $this->get('action-method');

        if (in_array(null, [$name, $path, $method, $cClass, $cMethod])) {
            $this->error("Usage error\n", 'All parameters are required.', true, true);
        }

        $this->routes[$name] = [
            'path'   => $path,
            'method' => $method,
            'action' => [
                'class'  => $cClass,
                'method' => $cMethod
            ]
        ];

        file_put_contents(Globals::CODE_DIR . 'app/res/routes.yml', Yaml::dump($this->routes));
        $this->console
            ->fColor(Console::COLOR_GREEN, Console::MODE_BRIGHT)
            ->writeln("Done.")
            ->mode()
        ;
    }

    public function setup()
    {
        $requester = new PdoRequester($this->cli->pdos->get());

        Logger::write("SETUP : Creating table user...", LogLevel::MAJOR);
        $schema = new SchemaHelper($requester, new PdoAttributesReader(User::class));
        $schema->createTable();

        $userManager = new UserManager(new CrudHelper(User::class, $requester));
        $pid = $userManager->createUser('admin', 'password');
        Logger::write("SETUP : Admin $pid created.", LogLevel::MAJOR);
    }

    public function routeList()
    {
        /** @var Route $route */
        foreach ($this->routes as $route) {
            $routeName = $route->getRouteName();

            $this->console
                ->fColor(Console::COLOR_YELLOW, Console::MODE_BRIGHT)
                ->write("$routeName : ")
                ->mode()
            ;

            $c = $route->getControllerClass();
            $m = $route->getControllerMethod();
            $this->console
                ->fColor(Console::COLOR_BLUE, Console::MODE_RESET)
                ->write($c)
                ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                ->write('::')
                ->fColor(Console::COLOR_BLUE, Console::MODE_RESET)
                ->write($m . '()')
                ->mode()
            ;

            $path = $route->getPath();
            if (null !== $path) {
                $parameters = $route->getParameters();
                foreach ($parameters as $pKey => $pValue) {
                    $path = str_replace("\$$pKey", "\e[0;32m\$$pKey\e[0m", $path);
                }
                $this->console
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write(' <- ')
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write('[')
                    ->fColor(Console::COLOR_CYAN, Console::MODE_BRIGHT)
                    ->write($route->getMethod()->value ?? '')
                    ->fColor(Console::COLOR_GRAY, Console::MODE_RESET)
                    ->write(' ' . $path ?? '')
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write(']')
                    ->mode()
                ;
            }
            $this->console->writeln();
        }
    }

    public function clearCache(): void
    {
        $this->console->fColor(Console::COLOR_CYAN)
                      ->writeln('Clearing the cache...')
                      ->mode()
        ;
        $deleted = Folder::removeTree(Globals::VAR_DIR . 'cache', removeFirst: false);
        foreach ($deleted as $file) {
            $this->console->writeln(" - $file deleted.");
        }
        $this->console->writeln(" -> Done");
    }

    public function clearLogs(): void
    {
        $env = $this->get('env') ?? 'dev';

        $this->console->fColor(Console::COLOR_CYAN)
                      ->writeln("Clearing the $env logs...")
                      ->mode()
        ;
        $deleted = Folder::removeTree(Globals::VAR_DIR . 'logs', "{$env}_.*[.]log", false, false);
        foreach ($deleted as $file) {
            $this->console->writeln(" - $file deleted.");
        }
        Logger::write("Log cleared");
        $this->console->writeln(" -> Done");
    }

    public function update(): void
    {
        $updateConf = Thor::config('update');
        $source = $updateConf['source'] ?? '';
        $afterUpdate = $updateConf['after-update'] ?? null;
        $updateFolder = Globals::VAR_DIR . 'update/';
        $resourcesBackupFolder = $updateFolder . 'resources/';
        $target = $updateFolder . 'repo/';

        // 1. Copy static files
        Logger::write('[1/11] Backup resources', LogLevel::VERBOSE, print: true);
        Folder::copyTree(Globals::RESOURCES_DIR, $resourcesBackupFolder);

        // 2. Disable all daemons
        Logger::write('[2/11] Disable daemons', LogLevel::VERBOSE, print: true);
        $daemons = self::loadDaemons();
        /**
         * @var Daemon      $daemon
         * @var DaemonState $state
         */
        $oldStates = [];
        foreach ($daemons as ['daemon' => $daemon, 'state' => $state]) {
            $daemonFile = Globals::STATIC_DIR . "daemons/{$daemon->getName()}.yml";
            $daemonInfo = Yaml::parseFile($daemonFile);
            $oldStates[$daemon->getName()] = $daemonInfo['enabled'];
            $daemonInfo['enabled'] = false;
            file_put_contents($daemonFile, Yaml::dump($daemonInfo));
        }

        // 3. Git clone
        Folder::removeTree($updateFolder);
        Logger::write('[3/11] Git clone', LogLevel::VERBOSE, print: true);
        CliKernel::executeProgram("git clone $source $target");

        // 4. Copy last version
        Logger::write('[4/11] Copy new files', LogLevel::VERBOSE, print: true);
        foreach (
            [
                $target . 'thor'    => Globals::CODE_DIR . 'thor',
                $target . 'bin'     => Globals::CODE_DIR . 'bin',
                $target . 'docs'    => Globals::CODE_DIR . 'docs',
                $target . 'app/src' => Globals::CODE_DIR . 'app/src',
            ] as $sourceFolder => $targetFolder
        ) {
            Folder::createIfNotExists($targetFolder);
            Folder::copyTree($sourceFolder, $targetFolder);
        }

        // 5. Restore instance files
        Logger::write('[5/11] Restore resources', LogLevel::VERBOSE, print: true);
        $restoreResource = function (string $file, string $targetPath, string $restorePrefix) {
            $basename = basename($file);
            $dirname = '';
            if (strlen(dirname($file)) > strlen($targetPath)) {
                $dirname = basename(dirname($file));
            }
            $restorePath = "$restorePrefix/$dirname/$basename";
            $restoreYml = Yaml::parseFile($restorePath);
            $instanceYml = Yaml::parseFile($file);
            file_put_contents(
                $restorePath,
                Yaml::dump($instanceYml + $restoreYml)
            );
        };
        $configBackup = $resourcesBackupFolder . 'config/';
        $staticBackup = $resourcesBackupFolder . 'static/';
        Folder::mapFiles($configBackup, $restoreResource, $configBackup, Globals::CONFIG_DIR);
        Folder::mapFiles($staticBackup, $restoreResource, $staticBackup, Globals::STATIC_DIR);

        // 6. Migrate DB
        Logger::write('[6/11] Migrate database', LogLevel::VERBOSE, print: true);
        $migrator = PdoMigrator::createFromConfiguration();
        $migrator->migrate(null);

        // 7. Run after-update
        Logger::write('[7/11] Run after-update', LogLevel::VERBOSE, print: true);
        if ($afterUpdate !== null) {
            CliKernel::executeCommand($afterUpdate);
        }

        // 8. Composer update
        Logger::write('[8/11] Composer update', LogLevel::VERBOSE, print: true);
        chdir(Globals::CODE_DIR);
        CliKernel::executeProgram('composer update');

        // 9. Clear cache
        Logger::write('[9/11] Composer update', LogLevel::VERBOSE, print: true);
        foreach (['dev', 'debug', 'verbose', 'prod'] as $env) {
            CliKernel::executeCommand('clear/cache', ['env' => $env]);
        }

        // 10. Restore daemons state
        Logger::write('[10/11] Restore daemons', LogLevel::VERBOSE, print: true);
        $daemons = self::loadDaemons();
        /**
         * @var Daemon      $daemon
         * @var DaemonState $state
         */
        foreach ($daemons as ['daemon' => $daemon, 'state' => $state]) {
            $daemonFile = Globals::STATIC_DIR . "daemons/{$daemon->getName()}.yml";
            $daemonInfo = Yaml::parseFile($daemonFile);
            $daemonInfo['enabled'] = $oldStates[$daemon->getName()] ?? false;
            file_put_contents($daemonFile, Yaml::dump($daemonInfo));
        }

        // 11. Clear update folder
        Logger::write('[11/11] Clear update folder', LogLevel::VERBOSE, print: true);
        Folder::removeTree($updateFolder);

    }

    private static function loadDaemons(): array
    {
        return array_map(
            fn(string $daemonFilename) => [
                'daemon' => $daemon = Daemon::instantiate(Yaml::parseFile($daemonFilename)),
                'state'  => new DaemonState($daemon)
            ],
            glob(Globals::STATIC_DIR . "daemons/*.yml")
        );
    }

}
