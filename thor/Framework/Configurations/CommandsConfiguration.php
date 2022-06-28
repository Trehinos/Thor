<?php

namespace Thor\Framework\Configurations;

use Thor\Configuration\ConfigurationFromFile;

/**
 *
 */

/**
 *
 */
final class CommandsConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('cli-commands', true);
    }

}
