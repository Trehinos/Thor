<?php

namespace Thor\Cli;

use Thor\Cli\Console\Color;
use Thor\Cli\Console\Console;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Base class for any Thor command.
 *
 * The command MUST extends this class and be defined in ``thor/res/static/commands.yml`.
 *
 * @package          Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
abstract class Command
{

    protected Console $console;

    /**
     * Construct a new Command in a CliKernel.
     *
     * @param string    $name
     * @param array     $args
     * @param CliKernel $cli
     */
    public function __construct(
        protected string $name,
        protected array $args,
        protected CliKernel $cli
    ) {
        $this->console = new Console();
    }

    /**
     * Get arguments of this command with values from the command line.
     *
     * @param array $argumentsFromCommandLine
     * @param array $argsSpec
     *
     * @return array
     */
    public static function getArgs(
        array $argumentsFromCommandLine,
        #[ArrayShape([['hasValue' => 'bool|null', 'description' => 'string|null', 'class' => 'string']])]
        array $argsSpec
    ): array {
        $args = [];
        $argName = '';
        $inValue = false;
        foreach ($argumentsFromCommandLine as $argument) {
            if (
                !$inValue && str_starts_with($argument, '-') && in_array(
                    $argKey = substr($argument, 1),
                    array_keys($argsSpec)
                )
            ) {
                if ($argsSpec[$argKey]['hasValue'] ?? false) {
                    $argName = $argKey;
                    $inValue = true;
                } else {
                    $args[$argKey] = true;
                }
            } elseif ($inValue) {
                $args[$argName] = $argument;
                $inValue = false;
            } else {
                $args[] = $argument;
            }
        }

        return $args;
    }

    /**
     * Set an argument of this command.
     *
     * @param string      $arg
     * @param string|bool $value
     *
     * @return void
     */
    public function set(string $arg, string|bool $value = true): void
    {
        $this->args[$arg] = $value;
    }

    /**
     * Get an argument of this command.
     *
     * @param string $arg
     *
     * @return string|bool|null
     */
    public function get(string $arg): string|bool|null
    {
        return $this->args[$arg] ?? null;
    }

    /**
     * Displays an error and stops the execution of the program.
     *
     * @param string $title
     * @param string $message
     * @param bool   $displayUsage
     * @param bool   $displayHelp
     *
     * @return never
     */
    public function error(
        string $title,
        string $message,
        bool $displayUsage = false,
        bool $displayHelp = false
    ): never {
        $this->console->fColor(Color::RED)->writeln('ERROR')->mode();
        $this->console->fColor(Color::YELLOW)->write($title)
                      ->fColor()->writeln($message)
                      ->mode();

        if ($displayUsage) {
            $this->console->writeln();
            $this->usage();
        }
        if ($displayHelp) {
            $this->console->writeln();
            $this->help();
        }
        exit;
    }

    /**
     * Display this command's usage.
     *
     * Uses CliKernel->displayCommandUsage().
     *
     * @param string|null $name
     *
     * @return void
     */
    public function usage(?string $name = null): void
    {
        $name ??= $this->name;
        $commandInfos = $this->cli->commands[$name];
        if (null === $commandInfos) {
            $this->console
                ->write('(usage error) Command ')
                ->fColor(Color::RED)
                ->write($name)
                ->fColor()
                ->writeln(" not found...\n");
            return;
        }
        $this->cli->displayCommandUsage($name, $commandInfos['arguments']);
    }

    /**
     * Display this command's help.
     *
     * Uses CliKernel->displayCommandDescription().
     *
     * @param string|null $name
     *
     * @return void
     */
    public function help(?string $name = null): void
    {
        $name ??= $this->name;
        $commandInfos = $this->cli->commands[$name];
        if (null === $commandInfos) {
            $this->console
                ->write('(usage error) Command ')
                ->fColor(Color::RED)
                ->write($name)
                ->fColor()
                ->writeln(" not found...\n");
            return;
        }
        $this->cli->displayCommandDescription($name, $commandInfos['description'], $commandInfos['arguments']);
    }

}
