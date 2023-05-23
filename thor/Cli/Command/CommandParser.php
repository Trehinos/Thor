<?php

namespace Thor\Cli\Command;

use JetBrains\PhpStorm\ArrayShape;

final class CommandParser
{

    /**
     * @param Option[]   $optionsSpecification
     * @param Argument[] $argumentsSpecification
     */
    private function __construct(private array $optionsSpecification, private array $argumentsSpecification)
    {
    }

    /**
     * @throws CommandError
     *
     * @see CommandParser
     */
    #[ArrayShape(['command' => "mixed|null|string", 'arguments' => "array", 'options' => "array"])]
    public function parse(Command $command, array $commandLineArguments): array
    {
        $commandFromLine = array_shift($commandLineArguments);
        if ($commandFromLine !== $command->command) {
            throw CommandError::mismatch($command, $commandFromLine);
        }

        $waitingForValue = false;
        $options = [];
        $arguments = [];
        $nextArg = 0;
        foreach ($commandLineArguments as $argumentFromLine) {
            if ($waitingForValue !== false) {
                $options[$waitingForValue->name] = $argumentFromLine;
                $waitingForValue = false;
                continue;
            }

            if ($this->isOption($argumentFromLine)) {
                $option = $this->parseOption($argumentFromLine);
                foreach (
                match ($option['type'] ?? '') {
                    'short' => $option['option'],
                    'long'  => [$option['option']]
                } as $opt
                ) {
                    if ($opt === null) {
                        continue;
                    }
                    if ($opt?->cumulative) {
                        $options[$opt->name] = ($options[$opt->name] ?? 0) + 1;
                    } else {
                        $options[$opt->name] = true;
                    }
                }
                if (array_key_exists('value', $option) && is_string($option['value'] ?? null)) {
                    $options[$option['option']?->name] = $option['value'];
                } elseif ($option['waiting'] ?? false) {
                    $waitingForValue = $option['for'];
                }
                continue;
            }

            if ($nextArg !== null) {
                $arguments[$command->arguments[$nextArg++]->name] = $argumentFromLine;
            }
        }

        return [
            'command'   => $commandFromLine,
            'arguments' => $arguments,
            'options'   => $options,
        ];
    }

    public static function with(Command $command): self
    {
        return new self($command->options, $command->arguments);
    }

    public function option(string $option): ?Option
    {
        foreach ($this->optionsSpecification as $opt) {
            if ($opt->long === $option || $opt->short === $option) {
                return $opt;
            }
        }

        return null;
    }

    public function parseOption(string $commandLineArgument): array
    {
        if (!str_starts_with($commandLineArgument, '-')) {
            return [];
        }

        $value = null;
        if (str_starts_with($commandLineArgument, '--')) {
            $commandLineArgument = trim($commandLineArgument, '-');
            $option = $commandLineArgument;
            if ($this->option($option)?->cumulative === true) {
                $value = 1;
            }
            if (str_contains($commandLineArgument, '=')) {
                [$option, $value] = explode('=', $commandLineArgument);
            } elseif ($this->option($option)?->hasValue === false) {
                $value = true;
            }
            return [
                'type'    => 'long',
                'option'  => $this->option($option),
                'value'   => $value,
                'waiting' => $value === null && ($this->option($option)?->hasValue ?? false) && !($this->option(
                        $option
                    )?->cumulative),
            ];
        }
        $commandLineArgument = trim($commandLineArgument, '-');
        $options = array_map(
            fn(string $o) => $this->option($o),
            str_split(trim($commandLineArgument, '-'))
        );
        $last = $options[array_key_last($options)];
        return [
            'type'    => 'short',
            'option'  => $options,
            'waiting' => $last?->hasValue ?? false,
            'for'     => $last,
        ];
    }

    public function isOption(string $commandLineArgument): bool
    {
        return str_starts_with($commandLineArgument, '-');
    }

}
