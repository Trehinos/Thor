<?php

namespace Thor\Cli;

/**
 * Interface CommandInterface
 * @package Thor\Cli
 *
 * @since 2020-09
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
interface CommandInterface
{

    /**
     * @return string the command name.
     */
    public function getCommand(): string;

    /**
     * @return CommandArgument[]
     */
    public function getArguments(): array;

    /**
     * @return string an optional description.
     */
    public function getDescription(): string;

    /**
     * what the command do when executed.
     */
    public function execute(): void;

    /**
     * @param array $argvCopy a copy or constructed array
     *
     * @return array ['arg' => value]
     */
    public function compileArguments(array $argvCopy): array;

}
