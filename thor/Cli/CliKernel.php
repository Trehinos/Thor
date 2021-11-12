<?php

/**
 * @package          Trehinos/Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Cli;

use Thor\Thor;
use Thor\Globals;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\Debug\Severity;
use Thor\KernelInterface;
use JetBrains\PhpStorm\ArrayShape;
use Thor\Database\PdoExtension\PdoCollection;

final class CliKernel implements KernelInterface
{
    public PdoCollection $pdos;

    public Console $console;

    public array $commands;

    /**
     * CliKernel constructor.
     *
     * @param array $configuration ['database' => ... ]
     */
    public function __construct(
        #[ArrayShape(['database' => 'array', 'commands' => 'array'])]
        array $configuration
    ) {
        $this->pdos = PdoCollection::createFromConfiguration($configuration['database'] ?? []);
        $this->console = new Console();
        $this->commands = $configuration['commands'];
        Logger::write('Instantiate CliKernel');
    }

    public static function executeBackgroundProgram(string $cmd, ?string $outputFile = null): void
    {
        if (substr(php_uname(), 0, 7) === "Windows") {
            $cmd = preg_replace('/^php/', 'php-win', $cmd);
            $outputFile = $outputFile ? ">> $outputFile" : '';
            pclose(popen("start /B $cmd $outputFile", "r"));
        } else {
            $outputFile ??= '/dev/null';
            exec("$cmd >> $outputFile &");
        }
    }

    public static function create(): static
    {
        self::guardCli();
        Logger::write('Start CLI context');
        return self::createFromConfiguration(Thor::getConfiguration()->getConsoleConfiguration());
    }

    public static function guardCli(): void
    {
        if ('cli' !== php_sapi_name()) {
            Logger::write(
                "PANIC ABORT : CLI kernel tried to be executed from not-CLI context.",
                LogLevel::MAJOR,
                Severity::ERROR
            );
            exit;
        }
    }

    public static function createFromConfiguration(array $config = []): static
    {
        return new self($config);
    }

    public static function executeCommand(string $commandName, array $args = []): void
    {
        $command = $commandName;
        foreach ($args as $argName => $argValue) {
            $command .= " -$argName \"$argValue\"";
        }
        CliKernel::executeProgram('php ' . Globals::BIN_DIR . "thor.php $command");
    }

    public static function executeProgram(string $cmd): void
    {
        if (substr(php_uname(), 0, 7) === "Windows") {
            pclose(popen($cmd, "r"));
        } else {
            exec($cmd);
        }
    }

    public function displayCommandUsage(
        string $command,
        #[ArrayShape(
            [
                [
                    'arg' => 'string',
                    'description' => 'string',
                    'hasValue' => 'boolean',
                ],
            ]
        )] array $args = []
    ): self {
        $this->console
            ->home()
            ->fColor(Console::COLOR_YELLOW)->write("$command")
            ->fColor()->writeln(" usage :\n")
            ->mode()
        ;

        $this->console
            ->home()
            ->fColor(Console::COLOR_GREEN)->write("\t$command ")
            ->mode()
        ;

        foreach ($args as $argName => $arg) {
            $argName = "-$argName";
            $value = ($arg['hasValue'] ?? false) ? ' value' : '';
            $this->console
                ->write('[')
                ->fColor(Console::COLOR_YELLOW)->write("$argName")
                ->fColor()->write($value)
                ->write('] ')
                ->mode()
            ;
        }

        $this->console->writeln("\n");

        return $this;
    }

    public function execute(): void
    {
        $args = self::getArgs();
        $command = $args[0] ?? '';
        if ('-help' === $command) {
            $displayCommand = null;
            if (($args[1] ?? null) !== null) {
                $displayCommand = $args[1];
            }

            if ($displayCommand === null) {
                $this->console
                    ->clear()
                    ->fColor(Console::COLOR_GREEN, Console::MODE_BRIGHT)->writeln('Thor v' . Thor::VERSION)
                    ->mode()->fColor()->write('Console help. ')
                    ->fColor(Console::COLOR_CYAN)->write('bin/thor.php')
                    ->fColor()->writeln(" command usage :")
                    ->fColor(Console::COLOR_CYAN)->write("\tbin/thor.php ")
                    ->fColor()->write("-help ")
                    ->fColor(Console::COLOR_YELLOW)->write("[command]")
                    ->fColor(mode: Console::MODE_DIM)->write(" | ")
                    ->mode()->fColor(Console::COLOR_GREEN)->write("command ")
                    ->fColor(Console::COLOR_YELLOW)->writeln("[-options]\n")
                ;
            } else {
                foreach ($this->commands as $commandName => $command) {
                    if ($displayCommand !== $commandName) {
                        continue;
                    }
                    $this->displayCommandDescription(
                        $commandName,
                        $command['description'] ?? '',
                        $command['arguments'] ?? []
                    );
                    return;
                }
                $this->console->mode()->writeln("Command $displayCommand not found");
                return;
            }

            $this->displayCommandDescription('-help [command]', 'display this screen');
            foreach ($this->commands as $commandName => $command) {
                $this->displayCommandDescription($commandName, $command['description'] ?? '');
            }
            return;
        }

        $commandSpecs = $this->commands[$command] ?? null;
        if (null === $commandSpecs) {
            return;
        }
        $commandClass = $commandSpecs['class'] ?? null;
        $commandAction = $commandSpecs['command'] ?? null;
        if (null === $commandClass || $commandAction === null) {
            return;
        }

        array_shift($args);
        $arguments = Command::getArgs($args, $commandSpecs['arguments'] ?? []);
        $commandObject = new $commandClass($command, $arguments, $this);
        $commandObject->$commandAction();
    }

    /**
     * @return array Commandline arguments without bin/ombre.php
     */
    public static function getArgs(): array
    {
        global $argv;
        $args = [];
        if (count($argv) > 1) {
            $args = $argv;
            array_shift($args);
        }

        return $args;
    }

    public function displayCommandDescription(
        string $command,
        string $description,
        #[ArrayShape(
            [
                [
                    'arg' => 'string',
                    'description' => 'string',
                    'hasValue' => 'boolean',
                ],
            ]
        )] array $args = []
    ): self {
        $spanCommand = str_repeat(' ', max(16 - strlen($command), 1));
        $span16 = str_repeat(' ', 16);
        $this->console
            ->home()
            ->fColor(Console::COLOR_GREEN)->write("\t$command" . $spanCommand)
            ->fColor(mode: Console::MODE_UNDERSCORE)->writeln($description)
            ->mode()
        ;

        foreach ($args as $argName => $arg) {
            $argName = "-$argName";
            $spanArg = str_repeat(
                ' ',
                max(
                    20 - strlen(
                        $argName . ($vSpan = ($arg['hasValue'] ?? false ? ' value' : ''))
                    ), 1)
            );
            $this->console
                ->write("\t$span16")
                ->fColor(Console::COLOR_YELLOW)->write("$argName$vSpan")
                ->fColor(mode: Console::MODE_UNDERSCORE)->writeln("$spanArg{$arg['description']}")
                ->mode()
            ;
        }

        $this->console->writeln();

        return $this;
    }
}
