<?php

namespace Thor\Database\PdoExtension;

use Symfony\Component\Yaml\Yaml;
use Thor\{Globals, Configuration\ConfigurationFromFile, Framework\Configurations\DatabasesConfiguration};

/**
 * Defines a class which updates current schema with a set of DQL queries.
 *
 * @package Thor/Database/PdoExtension
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
final class PdoMigrator
{

    /**
     * @param PdoCollection $pdoCollection
     * @param array         $sqlMigrations ['handler_name' => [migrationIndex1 => [...sql], migrationIndex2 => [...sql]]]
     * @param int           $migrationIndex current migration index
     */
    public function __construct(
        private PdoCollection $pdoCollection,
        private array $sqlMigrations,
        private int $migrationIndex
    ) {
    }

    /**
     * Migrates all databases to the specified migrationIndex.
     *
     * If $nextIndex is explicitly null, it will migrate to the last index.
     *
     * @return int the last index migrated.
     */
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

    /**
     * Create a PdoMigrator from `thor/res/config/update.yml` and `thor/res/config/database.yml`.
     *
     * The migration files are loaded from `thor/res/static/{update.migration-folder}/migration_{migrationIndex}.yml`
     */
    public static function createFromConfiguration(): self
    {
        $updateConfiguration = ConfigurationFromFile::get('update');
        $databaseConfiguration = DatabasesConfiguration::get();
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
