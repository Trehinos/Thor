<?php

namespace Thor\Framework\Configurations;

use Thor\Globals;
use Thor\Configuration\ConfigurationFromFile;

/**
 *
 */

/**
 *
 */
final class TwigConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('twig');
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function getStaticPath(string $filename): string
    {
        return Globals::STATIC_DIR . "{$this['asset_dir']}";
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function getWebCachePath(string $filename): string
    {
        return Globals::WEB_DIR . "{$this['asset_cache']}";
    }

}
