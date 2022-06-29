<?php

namespace Thor\Cli;

use Thor\Globals;
use Thor\Debug\Logger;
use Thor\Process\Option;
use Thor\Debug\LogLevel;
use Thor\Process\Argument;
use Thor\Process\CommandError;
use Thor\Process\KernelInterface;
use Thor\Configuration\Configuration;
use Thor\Framework\Factories\Configurations;

class NewCliKernel implements KernelInterface
{

    private function __construct(private array $commands)
    {
    }

    /**
     * Run the terminal command specified in the background.
     *
     * An output file can be specified to catch echoes.
     *
     * @param string      $cmd
     * @param string|null $outputFile
     *
     * @return void
     */
    public static function executeBackgroundProgram(string $cmd, ?string $outputFile = null): void
    {
        if (self::isWindows()) {
            $cmd = preg_replace('/^php/', 'php-win', $cmd);
            $outputFile = $outputFile ? ">> $outputFile" : '';
            pclose(popen("start /B $cmd $outputFile", "r"));
        } else {
            $outputFile ??= '/dev/null';
            exec("$cmd >> $outputFile &");
        }
    }

    /**
     * Returns true if PHP is run by q Windows system, false otherwise.
     *
     * @return bool
     */
    public static function isWindows(): bool
    {
        return str_starts_with(php_uname(), "Windows");
    }

    /**
     * @inheritDoc
     */
    public static function create(): static
    {
        self::guardCli();
        Logger::write('Start CLI context');
        return self::createFromConfiguration(Configurations::getConsoleConfiguration());
    }

    /**
     * This function exit the program if PHP is not run from Cli context.
     *
     * @return void
     */
    public static function guardCli(): void
    {
        if ('cli' !== php_sapi_name()) {
            Logger::write(
                "PANIC ABORT : CLI kernel tried to be executed from not-CLI context.",
                LogLevel::CRITICAL,
            );
            exit;
        }
    }

    /**
     * Run the Thor command specified in a terminal with its specified arguments values.
     *
     * @param string $commandName
     * @param array  $args
     *
     * @return void
     */
    public static function executeCommand(string $commandName, array $args = []): void
    {
        $command = $commandName;
        foreach ($args as $argValue) {
            $command .= " \"$argValue\"";
        }
        CliKernel::executeProgram('php ' . Globals::BIN_DIR . "thor.php $command");
    }

    /**
     * Run the terminal command specified in a terminal.
     *
     * @param string $cmd
     *
     * @return void
     */
    public static function executeProgram(string $cmd): void
    {
        if (self::isWindows()) {
            pclose(popen($cmd, "r"));
        } else {
            $output = [];
            exec($cmd, $output);
            if (!empty($output)) {
                echo implode("\n", $output);
            } else {
                echo "Ok\n";
            }
        }
    }

    public function execute(): void
    {
        global $argv;
        array_shift($argv);
        foreach ($this->commands as $command) {
            if ($command->matches($argv)) {
                $input = $command->parse($argv);
                $command->kernel = $this;
                $command->setContext([...$input['arguments'], ...$input['options']]);
                $command->execute();
                return;
            }
        }

        throw CommandError::notFound($argv[0] ?? '');
    }

    public static function createFromConfiguration(Configuration $config): static
    {
        $yaml = $config['commands']?->getArrayCopy() ?? [];
        return new self(
            array_map(
                fn(string $command, array $specifications) => new ($specifications['class'])(
                    $command,
                    $specifications['description'] ?? '',
                    array_map(
                        fn(string $name, array $argumentArray) => Argument::fromArray(
                            ['name' => $name] + $argumentArray
                        ),
                        array_keys($specifications['arguments'] ?? []),
                        $specifications['arguments'] ?? []
                    ),
                    array_map(
                        fn(string $name, array $optionArray) => Option::fromArray(['name' => $name] + $optionArray),
                        array_keys($specifications['options'] ?? []),
                        $specifications['options'] ?? []
                    )
                ),
                array_keys($yaml),
                array_values($yaml)
            )
        );
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

}
