<?php

namespace June;

use June\D2\Node2D;
use June\Process\Node;
use Thor\Cli\CliKernel;
use Thor\Process\Command;

class Test extends Command
{
    public function __construct(
        string $command,
        string $description = '',
        array $arguments = [],
        array $options = [],
        ?CliKernel $kernel = null
    ) {
        parent::__construct($command, $description, $arguments, $options, $kernel);
    }

    public function execute(): void
    {
        $node = new Node('test');
        $node->addChild(new Node2D('test2d', 8, 11));
        echo $node->getDebug();
    }

}
