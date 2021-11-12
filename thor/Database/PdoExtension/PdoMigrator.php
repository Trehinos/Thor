<?php

namespace Thor\Database\PdoExtension;

use Thor\Thor;
use Thor\Globals;
use Symfony\Component\Yaml\Yaml;

final class PdoMigrator
{

    public function __construct(
        private PdoCollection $pdoCollection,
        private array $sqlMigrations,
        private int $migrationIndex
    ) {
    }

    public function migrate(?int $nextIndex): int
    {
        $requesters = [];
        $lastIndex = 0;
        foreach ($this->sqlMigrations as $handlerName => $migrations) {
            if (!array_key_exists($handlerName, $requesters)) {
                $requesters[$handlerName] = new PdoRequester($this->pdoCollection->get($handlerName));
            }
            ksort($migrations);
            foreach ($migrations as $migrationIndex => $migrationQueries) {
                if ($migrationIndex > $this->migrationIndex) {
                    if ($nextIndex !== null && $migrationIndex > $nextIndex) {
                        continue;
                    }
                    $lastIndex = max($migrationIndex, $lastIndex);
                    foreach ($migrationQueries as $query) {
                        $requesters[$handlerName]->execute($query, []);
                    }
                }
            }
        }
        return intval($lastIndex);
    }

    public static function createFromConfiguration(): self
    {
        $updateConfiguration = Thor::config('update');
        $databaseConfiguration = Thor::config('database');
        $migrations = [];
        $migrationFilelist = glob(
            (Globals::STATIC_DIR . ($updateConfiguration['migration-folder'] ?? '')) . '/migration_*.yml'
        );
        foreach ($migrationFilelist as $filename) {
            $yaml = Yaml::parseFile($filename);
            $basename = basename($filename);
            $num = substr($basename, strpos($basename, '_') + 1);
            $num = substr($num, 0, strrpos($basename, '.'));
            foreach ($databaseConfiguration as $key => $unused) {
                $migrations[$key][$num] = $yaml[$key] ?? null;
            }
        }

        return new self(
            PdoCollection::createFromConfiguration($databaseConfiguration),
            $migrations,
            $databaseConfiguration['migration-index'] ?? 0
        );
    }

}
