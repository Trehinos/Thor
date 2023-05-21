<?php

namespace Thor\Cli\Command;

use Thor\Common\Debug\ThorException;

class CommandError extends ThorException
{

    public const NOT_FOUND = 1;
    public const MISMATCH = 2;
    public const MISUSAGE = 3;

    public static function notFound(string $input): self
    {
        return new self(self::NOT_FOUND, "Command \"$input\" not found");
    }

    public static function mismatch(Command $cliCommand, string $input): self
    {
        return new self(self::MISMATCH, "The command line \"$input\" mismatches the \"{$cliCommand->command}\" command.");
    }

    public static function misusage(Command $cliCommand): self
    {
        $cliCommand->usage();
        return new self(self::MISUSAGE, "Invalid usage of \"{$cliCommand->command}\"");
    }

}
