<?php

namespace Thor;

use Thor\Debug\Logger;

final class Application implements KernelInterface
{

    private ?KernelInterface $kernel;

    public function __construct(?KernelInterface $kernel = null)
    {
        $this->kernel = $kernel;
    }

    public function execute()
    {
        if (null == $this->kernel) {
            Logger::write("Application fatal error : kernel not defined.");
            echo "Error :\nKernel not selected.\n";
            exit;
        }
        $this->kernel->execute();
    }

}
