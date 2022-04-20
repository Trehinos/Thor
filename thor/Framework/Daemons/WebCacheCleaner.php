<?php

namespace Thor\Framework\Daemons;

use DateTime;
use Thor\Thor;
use DateInterval;
use Thor\Globals;
use Thor\Cli\Daemon;
use Thor\FileSystem\Folder;
use Thor\Configuration\TwigConfiguration;

final class WebCacheCleaner extends Daemon
{

    public function execute(): void
    {
        $configuration = TwigConfiguration::get();

        $limit = (Thor::isDev())
            ? (new DateTime())->sub(new DateInterval('PT5M'))
            : (new DateTime())->sub(new DateInterval($configuration['assets_cache_ttl'] ?? 'PT24H'));

        if (file_exists(Globals::WEB_DIR . $configuration['assets_cache'])) {
            Folder::removeTree(
                             Globals::WEB_DIR . $configuration['assets_cache'],
                removeFirst: false,
                removeCondition: fn(string $filename) => $limit->format('YmdHis') >= date(
                        'YmdHis',
                        filemtime($filename)
                    )
            );
        }
        if (file_exists(Globals::VAR_DIR . $configuration['cache_dir'])) {
            Folder::removeTree(
                             Globals::VAR_DIR . $configuration['cache_dir'],
                removeFirst: false,
                removeCondition: fn(string $filename) => $limit->format('YmdHis') >= date(
                        'YmdHis',
                        filemtime($filename)
                    )
            );
        }
    }
}
