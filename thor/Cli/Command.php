<?php

namespace Thor\Cli;

use JetBrains\PhpStorm\ArrayShape;

abstract class Command
{

    protected Console $console;

    public function __construct(protected string $name, private array $args)
    {
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
