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

    public function execute(string $commandLine): void;

}
