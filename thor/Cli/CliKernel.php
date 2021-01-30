<?php

namespace Thor\Cli;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Yaml\Yaml;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Debug\Logger;
use Thor\Globals;
use Thor\KernelInterface;

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
        #[ArrayShape(['databases' => 'array', 'commands' => 'array'])]
        array $configuration
    ) {
        $this->pdos = self::createDatabasesFromConfiguration($configuration['databases'] ?? []);
        $this->console = new Console();
        $this->commands = $configuration['commands'];
        Logger::write('Instantiate CliKernel');
    }

    private static function createDatabasesFromConfiguration(array $db_config): PdoCollection
    {
        $pdos = new PdoCollection();

        foreach ($db_config as $connectionName => $config) {
            $pdos->add(
                $connectionName,
                new PdoHandler(
                    $config['dsn'] ?? '',
                    $config['user'] ?? '',
                    $config['password'] ?? ''
                )
            );
        }

        return $pdos;
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


    private function displayCommandDescription(
        string $command,
        string $description,
        #[ArrayShape(
            [
                [
                    'arg' => 'string',
                    'description' => 'string',
                    'hasValue' => 'boolean'
                ]
            ]
        )] array $args = []
    ): self {
        $spanCommand = str_repeat(' ', 16 - strlen($command));
        $span16 = str_repeat(' ', 16);
        $this->console
            ->home()
            ->fColor(Console::COLOR_RED)->write("\t$command" . $spanCommand)
            ->fColor(mode: Console::MODE_UNDERSCORE)->writeln($description)
            ->mode();

        foreach ($args as $argName => $arg) {
            $argName = "-$argName";
            $spanArg = str_repeat(
                ' ',
                20 - strlen(
                    $argName . ($vSpan = ($arg['hasValue'] ?? false ? ' value' : ''))
                )
            );
            $this->console
                ->write("\t$span16")
                ->fColor(Console::COLOR_YELLOW)->write("$argName$vSpan")
                ->fColor(mode: Console::MODE_UNDERSCORE)->writeln("$spanArg{$arg['description']}")
                ->mode();
        }

        $this->console->writeln();

        return $this;
    }

    public function execute(): void
    {
        $args = self::getArgs();
        $command = $args[0] ?? '';
        if ('-help' === $command) {
            $this->console
                ->clear()
                ->fColor(Console::COLOR_GREEN, Console::MODE_BRIGHT)->writeln('Ombre v' . Globals::VERSION)
                ->mode()->fColor()->write('Console help. ')
                ->fColor(Console::COLOR_CYAN)->write('bin/ombre.php')
                ->fColor()->writeln(" command usage :")
                ->fColor(Console::COLOR_CYAN)->write("\tbin/ombre.php ")
                ->fColor()->write("-help ")
                ->fColor(mode: Console::MODE_DIM)->write("| ")
                ->mode()->fColor(Console::COLOR_RED)->write("command ")
                ->fColor(Console::COLOR_YELLOW)->writeln("[-options]\n");

            $this->displayCommandDescription('-help', 'display this screen');
            foreach ($this->commands as $commandName => $command) {
                $this->displayCommandDescription(
                    $commandName,
                    $command['description'] ?? '',
                    $command['arguments'] ?? []
                );
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
        $commandObject = new $commandClass($this, $arguments);
        $commandObject->$commandAction();
    }

    public static function create(array $config = []): self
    {
        if ('cli' !== php_sapi_name()) {
            Logger::write(
                "PANIC ABORT : CLI kernel tried to be executed from not-CLI context.",
                Logger::LEVEL_PROD,
                Logger::SEVERITY_ERROR
            );
            exit;
        }

        $commands = Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'commands.yml'));

        return new self(
            [
                'databases' => $config['databases'] ?? [],
                'commands' => $commands ?? []
            ]
        );
    }
}