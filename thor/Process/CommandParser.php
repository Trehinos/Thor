<?php

namespace Thor\Process;

final class CommandParser
{

    /**
     * @param Option[]   $optionsSpecification
     * @param Argument[] $argumentsSpecification
     */
    private function __construct(private array $optionsSpecification, private array $argumentsSpecification)
    {
    }

    public static function with(CliCommand $command): self
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
                'waiting' => $value === null && ($this->option($option)?->hasValue ?? false) && !($this->option($option)?->cumulative),
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
