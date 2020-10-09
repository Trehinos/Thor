<?php

namespace Thor\Cli;

interface CommandInterface
{

    public function getCommand(): string;

    /**
     * @return CommandArgument[]
     */
    public function getArguments(): array;

    public function getDescription(): string;

    public function execute(): void;

    /**
     * @param array $argvCopy a copy or constructed array
     *
     * @return array ['arg' => value]
     */
    public function parseArguments(array $argvCopy): array;

}
