<?php

namespace Thor\Framework\Configurations;

use Thor\Configuration\ConfigurationFromResource;

final class CommandsConfiguration extends ConfigurationFromResource
{

    public function __construct()
    {
        parent::__construct('commands');
    }

}
