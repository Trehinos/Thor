<?php

namespace Thor\Cli\Commands;

use Thor\Cli\AbstractCommand;
use Thor\Cli\CommandArgument;

/**
 * Class ThorCommand: represents a base Thor command
 * @package Thor\Cli\Commands
 *
 * @since 2020-09
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
abstract class ThorCommand extends AbstractCommand
{

    /**
     * ThorCommand constructor.
     *
     * @param string $command
     * @param string $description
     * @param array $arguments
     */
    public function __construct(string $command, string $description = '', array $arguments = [])
    {
        parent::__construct($command, $description, $arguments);
    }

    /**
     * compileArguments(): Takes -x and --xxxx arguments and put the name and values in an array.
     *
     * @param array $argvCopy
     *
     * @return array
     */
    public function compileArguments(array $argvCopy): array
    {
        $argumentsValues = [];
        $parsingValue = false;
        $tmpName = '';

        foreach ($argvCopy as $commandLineArg) {
            if ($parsingValue) {
                $parsingValue = false;
                $argumentsValues[$tmpName] = $commandLineArg;
            } else {
                foreach ($this->getArguments() as $argument) {
                    $compiled = false;
                    foreach (['--', '-', null] as $token) {
                        if (!$compiled) {
                            $compiled = self::compileArgument(
                                $argumentsValues,
                                $parsingValue,
                                $tmpName,
                                $commandLineArg,
                                $argument,
                                $token
                            );
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

    private static function compileArgument(
        array &$argumentsValues,
        bool &$parsingValue,
        string &$tmpName,
        string $commandLineArg,
        CommandArgument $argument,
        ?string $token
    ): bool {
        if (
            (
                $token === null &&
                $argument->shortArgument === '' &&
                $argument->longArgument === '' &&
                !in_array($argument->name, array_keys($argumentsValues))
            ) || (
                $token === substr($commandLineArg, 0, strlen($token)) &&
                substr($commandLineArg, strlen($token)) === $argument->longArgument
            )
        ) {
            $argumentsValues[$argument->name] = true;
            $parsingValue = $argument[$argument->hasValue] ?? false;
            if ($parsingValue) {
                $tmpName = $argument->name;
            }
            return true;
        }

        return false;
    }
}

