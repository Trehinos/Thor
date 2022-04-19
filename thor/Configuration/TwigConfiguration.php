<?php

namespace Thor\Configuration;

use Thor\Globals;

final class TwigConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('twig');
    }

    public function getStaticPath(string $filename): string
    {
        return Globals::STATIC_DIR . "{$this['asset_dir']}";
    }

    public function getWebCachePath(string $filename): string
    {
        return Globals::WEB_DIR . "{$this['asset_cache']}";
    }

}
