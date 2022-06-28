<?php


namespace Thor\Cli;

use Thor\Thor;
use Thor\Globals;
use Thor\Process\KernelInterface;
use Thor\Debug\{Logger, LogLevel};
use JetBrains\PhpStorm\ArrayShape;
use Thor\Configuration\Configuration;
use Thor\Framework\Factories\Configurations;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Cli\Console\{Mode, Color, Console, CursorControl};

/**
 * This class is the Kernel launched by the `thor.php` entry point.
 *
 * It is made to run the command defined in `thor/res/static/commands.yml` and specified in the command line.
 *
 * @package           Thor/Cli
 * @copyright (2021)  Sébastien Geldreich
 * @license           MIT
 */
final class CliKernel implements KernelInterface
{
    /**
     * @var PdoCollection collection of PdoHandler accessible from the CliKernel.
     */
    public PdoCollection $pdos;

    /**
     * @var Console public console object to control Cli output.
     */
    public Console $console;

    /**
     * @var array commands list defined in `thor/res/static/commands.yml`
     */
    public array $commands;

    /**
     * Construct a CliKernel with database and commands configuration.
     *
     * @param Configuration $configuration ['database' => 'array', 'commands' => 'array']
     */
    public function __construct(
        Configuration $configuration
    ) {
        $this->pdos = PdoCollection::createFromConfiguration($configuration['database'] ?? []);
        $this->console = new Console();
        $this->commands = $configuration['commands']->getArrayCopy();
        Logger::write('Instantiate CliKernel');
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
     * Construct a CliKernel with database and commands configuration.
     *
     * @param Configuration $config ['database' => 'array', 'commands' => 'array']
     *
     * @return CliKernel
     */
    public static function createFromConfiguration(
        Configuration $config
    ): static {
        return new self($config);
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

    /**
     * Executes a command from the command line.
     *
     * @return void
     */
    public function execute(): void
    {
        $args = self::getArgs();
        $command = $args[0] ?? '';
        if ('help' === $command) {
            $displayCommand = null;
            if (($args[1] ?? null) !== null) {
                $displayCommand = $args[1];
            }

            if ($displayCommand === null) {
                $this->console
                    ->clear()
                    ->fColor(Color::GREEN, Mode::BRIGHT)->writeln(
                        Thor::appName() . ' v' . Thor::version()
                    )
                    ->mode()->fColor()->write('Console help. ')
                    ->fColor(Color::CYAN)->write('bin/thor.php')
                    ->fColor()->writeln(" command usage :")
                    ->fColor(Color::CYAN)->write("\tbin/thor.php ")
                    ->fColor()->write("-help ")
                    ->fColor(Color::YELLOW)->write("[command]")
                    ->fColor(mode: Mode::DIM)->write(" | ")
                    ->mode()->fColor(Color::GREEN)->write("command ")
                    ->fColor(Color::YELLOW)->writeln("[-options]\n");
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
            $this->console
                ->mode(Mode::BRIGHT)
                ->fColor(Color::YELLOW)->write("The command ")
                ->fColor(Color::BLUE)->write($command)
                ->fColor(Color::YELLOW)->writeln(" doesn't exist...")
                ->writeln()
                ->mode()
                ->mode(Mode::UNDERSCORE)
                ->write('Write ')
                ->fColor(Color::GREEN)->write('-help')
                ->fColor()->writeln(' to see a list of valid commands.')
                ->writeln();
            return;
        }
        $commandClass = $commandSpecs['class'] ?? null;
        $commandAction = $commandSpecs['command'] ?? null;
        if (null === $commandClass || $commandAction === null) {
            $this->console
                ->mode(Mode::BRIGHT)
                ->fColor(Color::YELLOW)->write("The command ")
                ->fColor(Color::RED)->write($command)
                ->fColor(Color::YELLOW)->writeln(" doesn't have a corresponding class...")
                ->writeln()
                ->mode();
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

    /**
     * This method prints a command description in a terminal.
     *
     * @param string $command
     * @param string $description
     * @param array  $args
     *
     * @return $this
     */
    public function displayCommandDescription(
        string $command,
        string $description,
        #[ArrayShape([
            [
                'arg'         => 'string',
                'description' => 'string',
                'hasValue'    => 'boolean',
            ],
        ])] array $args = []
    ): self {
        $spanCommand = str_repeat(' ', max(16 - strlen($command), 1));
        $span16 = str_repeat(' ', 16);
        $this->console
            ->echoes(CursorControl::home(), Color::FG_GREEN, "\t$command$spanCommand")
            ->echoes(Mode::UNDERSCORE, "$description\n");

        foreach ($args as $argName => $arg) {
            $argName = "-$argName";
            $spanArg = str_repeat(
                ' ',
                max(
                    20 - strlen(
                        $argName . ($vSpan = ($arg['hasValue'] ?? false ? ' value' : ''))
                    ),
                    1
                )
            );
            $this->console
                ->write("\t$span16")
                ->fColor(Color::YELLOW)->write("$argName$vSpan")
                ->fColor(mode: Mode::UNDERSCORE)->writeln("$spanArg{$arg['description']}")
                ->mode();
        }

        $this->console->writeln();

        return $this;
    }

    /**
     * This method prints a command usage in a terminal.
     *
     * @param string $command
     * @param array  $args
     *
     * @return $this
     */
    public function displayCommandUsage(
        string $command,
        #[ArrayShape(
            [
                [
                    'arg'         => 'string',
                    'description' => 'string',
                    'hasValue'    => 'boolean',
                ],
            ]
        )] array $args = []
    ): self {
        $this->console
            ->echoes(CursorControl::home(), Color::FG_YELLOW, $command, Color::FG_GRAY, " usage :\n\n")
            ->mode();

        $this->console
            ->echoes(CursorControl::home())
            ->fColor(Color::GREEN)->write("\t$command ")
            ->mode();

        foreach ($args as $argName => $arg) {
            $argName = "-$argName";
            $value = ($arg['hasValue'] ?? false) ? ' value' : '';
            $this->console
                ->write('[')
                ->fColor(Color::YELLOW)->write("$argName")
                ->fColor()->write($value)
                ->write('] ')
                ->mode();
        }

        $this->console->writeln("\n");

        return $this;
    }
}
