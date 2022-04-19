<?php

namespace Thor\Framework\Daemons;

use DateTime;
use Thor\Thor;
use DateInterval;
use Thor\Globals;
use Thor\Cli\Daemon;
use Thor\FileSystem\Folder;
use Thor\Configuration\TwigConfiguration;

final class AssetsCacheCleaner extends Daemon
{

    public function execute(): void
    {
        $configuration = TwigConfiguration::get();

        $limit = (Thor::isDev())
            ? (new DateTime())->sub(new DateInterval('PT5M'))
            : (new DateTime())->sub(new DateInterval($configuration['assets_cache_ttl'] ?? 'PT24H'));

        $deleteFile = fn($file_path) => $limit->format('YmdHis') > date('YmdHis', filemtime($file_path))
            ? Folder::removeTree($file_path)
            : null;

        Folder::mapFiles(
            Globals::WEB_DIR . $configuration['assets_cache'],
            function (string $file_path) use ($deleteFile) {
                if (file_exists($file_path)) {
                    $deleteFile($file_path);
                }
            }
        );
        Folder::mapFiles(
            Globals::VAR_DIR . $configuration['cache_dir'],
            function (string $file_path) use ($deleteFile) {
                if (file_exists($file_path)) {
                    $deleteFile($file_path);
                }
            }
        );
    }

}
