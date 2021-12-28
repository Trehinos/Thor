<?php

namespace Thor\Configuration;

final class KernelsConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('kernels', true);
    }

}