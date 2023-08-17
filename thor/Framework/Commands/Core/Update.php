<?php

namespace Thor\Framework\Commands\Core;

use Thor\Globals;
use Thor\Debug\Logger;
use Thor\Cli\CliKernel;
use Thor\Cli\Daemon\Daemon;
use Thor\FileSystem\Folder;
use Thor\Cli\Command\Command;
use Thor\Cli\Daemon\DaemonState;
use Symfony\Component\Yaml\Yaml;
use Thor\Database\PdoExtension\PdoMigrator;
use Thor\Configuration\ConfigurationFromFile;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Update extends Command
{

    public function execute(): void
    {
        $updateConf = new ConfigurationFromFile('update');
        $source = $updateConf['source'] ?? '';
        $afterUpdate = $updateConf['after-update'] ?? null;
        $updateFolder = Globals::VAR_DIR . 'update/';
        $resourcesBackupFolder = $updateFolder . 'resources/';
        $target = $updateFolder . 'repo/';
        $composer = $updateConf['composer-command'] ?? 'composer';
        $composerOptions = $updateConf['composer-options'] ?? '';

        // 1. Copy static files
        Logger::write('[1/11] Backup resources', print: true);
        Folder::createIfNotExists($resourcesBackupFolder);
        Folder::copyTree(Globals::RESOURCES_DIR, $resourcesBackupFolder);

        // 2. Disable all daemons
        Logger::write('[2/11] Disable daemons', print: true);
        $daemons = self::loadDaemons();
        /**
         * @var Daemon $daemon
         * @var DaemonState             $state
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
        Logger::write('[3/11] Git clone', print: true);
        CliKernel::executeProgram("git clone $source $target");

        // 4. Copy last version
        Logger::write('[4/11] Copy new files', print: true);
        foreach (
            [
                $target . 'thor'    => Globals::CODE_DIR . 'thor',
                $target . 'bin'     => Globals::CODE_DIR . 'bin',
                $target . 'app/src' => Globals::CODE_DIR . 'app/src',
            ] as $sourceFolder => $targetFolder
        ) {
            Folder::createIfNotExists($targetFolder);
            Folder::copyTree($sourceFolder, $targetFolder);
        }

        // 5. Restore instance files
        Logger::write('[5/11] Restore resources', print: true);
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
                Yaml::dump(
                            $instanceYml + $restoreYml,
                    inline: 5,
                    flags:  Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_NULL_AS_TILDE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
                )
            );
        };
        $configBackup = $resourcesBackupFolder . 'config/';
        $staticBackup = $resourcesBackupFolder . 'static/';
        Folder::mapFiles($configBackup, $restoreResource, $configBackup, Globals::CONFIG_DIR);
        Folder::mapFiles($staticBackup, $restoreResource, $staticBackup, Globals::STATIC_DIR);

        // 6. Migrate DB
        Logger::write('[6/11] Migrate database', print: true);
        $migrator = PdoMigrator::createFromConfiguration();
        $migrator->migrate(null);

        // 7. Run after-update
        Logger::write('[7/11] Run after-update', print: true);
        if ($afterUpdate !== null) {
            CliKernel::executeCommand($afterUpdate);
        }

        // 8. Composer update
        Logger::write('[8/11] Composer update', print: true);
        chdir(Globals::CODE_DIR);
        CliKernel::executeProgram("$composer update $composerOptions");

        // 9. Clear cache
        Logger::write('[9/11] Composer update', print: true);
        foreach (['dev', 'debug', 'verbose', 'prod'] as $env) {
            CliKernel::executeCommand('clear/cache', ['env' => $env]);
        }

        // 10. Restore daemons state
        Logger::write('[10/11] Restore daemons', print: true);
        $daemons = self::loadDaemons();

        /**
         * @var \Thor\Cli\Daemon\Daemon $daemon
         * @var DaemonState             $state
         */
        foreach ($daemons as ['daemon' => $daemon, 'state' => $state]) {
            $daemonFile = Globals::STATIC_DIR . "daemons/{$daemon->getName()}.yml";
            $daemonInfo = Yaml::parseFile($daemonFile);
            $daemonInfo['enabled'] = $oldStates[$daemon->getName()] ?? false;
            file_put_contents($daemonFile, Yaml::dump($daemonInfo));
        }

        // 11. Clear update folder
        Logger::write('[11/11] Clear update folder', print: true);
        sleep(2);
        Folder::removeTree($updateFolder);
    }

    /**
     * @return array
     */
    private static function loadDaemons(): array
    {
        return array_map(
            fn(string $daemonFilename) => [
                'daemon' => $daemon = Daemon::instantiate(Yaml::parseFile($daemonFilename)),
                'state'  => new DaemonState($daemon),
            ],
            glob(Globals::STATIC_DIR . "daemons/*.yml")
        );
    }

}
