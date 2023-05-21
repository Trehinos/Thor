<?php

namespace Thor\Framework\Database;

use Thor\{Framework\Globals,
    Database\PdoExtension\PdoRequester,
    Database\PdoExtension\PdoCollection,
    Common\Configuration\ConfigurationFromFile,
    Framework\Configurations\DatabasesConfiguration};

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
        $lastIndex = 0;
        foreach ($this->sqlMigrations as $handlerName => $migrations) {
            ksort($migrations);
            $requester = new PdoRequester($this->pdoCollection->get($handlerName));
            foreach ($migrations as $migrationIndex => $migrationQueries) {
                $migrationQueries = explode(";\n", $migrationQueries);
                if ($migrationIndex > $this->migrationIndex) {
                    if ($nextIndex !== null && $migrationIndex > $nextIndex) {
                        break;
                    }
                    $lastIndex = max($migrationIndex, $lastIndex);
                    foreach ($migrationQueries as $query) {
                        if (trim($query) === '') {
                            continue;
                        }
                        $requester->execute($query);
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
        $folder = Globals::STATIC_DIR . ($updateConfiguration['migration-folder'] ?? '');
        $migrationFilelist = glob($folder . '/*.sql');
        sort($migrationFilelist, SORT_NATURAL);

        $migrations = [];
        foreach ($migrationFilelist as $filename) {
            $basename = basename($filename);
            $num = substr($basename, ($pos1 = strpos($basename, '-') + 1));
            $connectionName = substr($basename, strpos($basename, '-', $pos1) + 1);
            $content = file_get_contents($filename);
            $migrations[$connectionName][$num] = $content;
        }

        return new self(
            PdoCollection::createFromConfiguration($databaseConfiguration),
            $migrations,
            $databaseConfiguration['migration-index'] ?? 0
        );
    }

}
