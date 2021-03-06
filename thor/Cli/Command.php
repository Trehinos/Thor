<?php

/**
 * @package Trehinos/Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Cli;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\NoReturn;

abstract class Command
{

    protected Console $console;

    public function __construct(
        protected string $name,
        private array $args,
        protected CliKernel $cli
    ) {
        $this->console = new Console();
    }

    public function set(string $arg, string|bool $value = true): void
    {
        $this->args[$arg] = $value;
    }

    public function get(string $arg): string|bool|null
    {
        return $this->args[$arg] ?? null;
    }

    public function usage(?string $name = null): void
    {
        $name ??= $this->name;
        $commandInfos = $this->cli->commands[$name];
        if (null === $commandInfos) {
            $this->console
                ->write('(usage error) Command ')
                ->fColor(Console::COLOR_RED)
                ->write($name)
                ->fColor()
                ->writeln(" not found...\n");
            return;
        }
        $this->cli->displayCommandUsage($name, $commandInfos['arguments']);
    }

    #[NoReturn]
    public function error(
        string $title,
        string $message,
        bool $displayUsage = false,
        bool $displayHelp = false
    ): void {
        $this->console->fColor(Console::COLOR_RED)->writeln('ERROR')->mode();
        $this->console->fColor(Console::COLOR_YELLOW)->write($title)
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

    public function help(?string $name = null): void
    {
        $name ??= $this->name;
        $commandInfos = $this->cli->commands[$name];
        if (null === $commandInfos) {
            $this->console
                ->write('(usage error) Command ')
                ->fColor(Console::COLOR_RED)
                ->write($name)
                ->fColor()
                ->writeln(" not found...\n");
            return;
        }
        $this->cli->displayCommandDescription($name, $commandInfos['description'], $commandInfos['arguments']);
    }

    public static function getArgs(
        array $argumentsFromCommandLine,
        #[ArrayShape([['hasValue' => 'bool|null', 'description' => 'string|null', 'class' => 'string']])]
        array $argsSpec
    ): array {
        $args = [];
        $argName = '';
        $inValue = false;
        foreach ($argumentsFromCommandLine as $argument) {
            if (!$inValue && str_starts_with($argument, '-') && in_array(
                    $argKey = substr($argument, 1),
                    array_keys($argsSpec)
                )) {
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

}
