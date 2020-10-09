<?php

namespace Thor\Cli\Commands;

use Thor\Cli\AbstractCommand;

abstract class ThorCommand extends AbstractCommand
{

    public function __construct(string $command, string $description = '', array $arguments = [])
    {
        parent::__construct($command, $description, $arguments);
    }

    /**
     * @param array $argvCopy
     * @return array
     */
    public function parseArguments(array $argvCopy): array
    {
        $argumentsValues = [];
        $parsingValue = false;

        foreach ($argvCopy as $commandLineArg) {
            foreach ($this->getArguments() as $argument) {
                if ($parsingValue) {
                    $parsingValue = false;
                    $argumentsValues[$argument->name] = $commandLineArg;
                } else {
                    if ('--' === substr($commandLineArg, 0, 2)) {
                        if (substr($commandLineArg, 2) === $argument->longArgument) {
                            $argumentsValues[$argument->name] = true;
                            $parsingValue = $argument[$argument->hasValue] ?? false;
                        }
                    } elseif ('-' === substr($commandLineArg, 0, 1)) {
                        if (substr($commandLineArg, 1) === $argument->shortArgument) {
                            $argumentsValues[$argument->name] = true;
                            $parsingValue = $argument[$argument->hasValue] ?? false;
                        }
                    }
                }
            }
        }

        return $argumentsValues + array_filter(
                $this->getArguments(),
                fn(string $name): bool => !in_array($name, array_keys($argumentsValues)),
                ARRAY_FILTER_USE_KEY
            );
    }
}
