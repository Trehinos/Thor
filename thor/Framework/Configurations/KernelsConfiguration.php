<?php

namespace Thor\Framework\Configurations;

use Thor\Configuration\ConfigurationFromFile;

/**
 *
 */

/**
 *
 */
final class KernelsConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('kernels', true);
    }

}
