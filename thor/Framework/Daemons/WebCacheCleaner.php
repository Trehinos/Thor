<?php

namespace Thor\Framework\Daemons;

use DateInterval;
use DateTime;
use Thor\Cli\Daemon\Daemon;
use Thor\Common\FileSystem\Folder;
use Thor\Framework\Configurations\TwigConfiguration;
use Thor\Framework\Globals;
use Thor\Framework\Thor;

final class WebCacheCleaner extends Daemon
{

    /**
     * @return void
     * @throws \Exception
     */
    public function execute(): void
    {
        $configuration = TwigConfiguration::get();

        $limit = (Thor::isDev())
            ? (new DateTime())->sub(new DateInterval('PT5M'))
            : (new DateTime())->sub(new DateInterval($configuration['assets_cache_ttl'] ?? 'P1D'));

        $assets_cache = Globals::WEB_DIR . $configuration['assets_cache'];
        if (file_exists($assets_cache)) {
            Folder::removeTree(
                $assets_cache,
                removeFirst: false,
                removeCondition: fn(string $filename) => $limit->format('YmdHis') >= date(
                        'YmdHis',
                        filemtime($filename)
                    )
            );
        }

        $cache_dir = Globals::VAR_DIR . $configuration['cache_dir'];
        if (file_exists($cache_dir)) {
            Folder::removeTree(
                $cache_dir,
                removeFirst: false,
                removeCondition: fn(string $filename) => $limit->format('YmdHis') >= date(
                        'YmdHis',
                        filemtime($filename)
                    )
            );
            Folder::removeTree(
                $cache_dir,
                removeCondition: fn(string $filename) => is_dir($filename) && empty(Folder::fileList($filename))
            );
        }
    }
}
