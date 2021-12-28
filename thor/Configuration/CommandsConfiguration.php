<?php

namespace Thor\Configuration;

final class CommandsConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('commands', true);
    }

}