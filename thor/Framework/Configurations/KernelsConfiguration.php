<?php

namespace Thor\Framework\Configurations;

use Thor\Configuration\ConfigurationFromResource;

final class KernelsConfiguration extends ConfigurationFromResource
{

    public function __construct()
    {
        parent::__construct('kernels');
    }

}
