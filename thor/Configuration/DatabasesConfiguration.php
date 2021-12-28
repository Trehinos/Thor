<?php

namespace Thor\Configuration;

final class DatabasesConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('database');
    }

    public function getDefault(): Configuration
    {
        return new Configuration($this['default'] ?? []);
    }

}