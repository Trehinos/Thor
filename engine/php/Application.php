<?php

namespace Thor;

final class Application implements KernelInterface
{

    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function execute()
    {
        $this->kernel->execute();
    }

}
