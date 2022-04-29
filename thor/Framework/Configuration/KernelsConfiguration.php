<?php

namespace Thor\Framework\Configuration;

use Thor\Configuration\ConfigurationFromFile;

final class KernelsConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('kernels', true);
    }

}