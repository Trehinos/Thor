<?php

namespace Thor\Ngine\Action;

use Thor\Ngine\Node;
use Thor\Http\Routing\Route;
use Thor\Process\Executable;

class Action extends Node implements Executable
{

    public function __construct(
        string $name
    ) {
        parent::__construct($name);
    }

    public function execute(): void
    {
    }

}
