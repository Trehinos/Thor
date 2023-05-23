<?php

namespace Tests;

use Thor\Cli\Command\Option;
use Thor\Cli\Command\Command;
use Thor\Cli\Command\Argument;
use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{

    public function testHelloCommand(): void
    {
        $commandLine = ['message', '-u', 'World', 'Hello'];
        $command = $this->helloCommand();
        $input = $command->parse($commandLine);
        $command->setContext([...$input['arguments'], ...$input['options']]);
        $command->execute();
        $this->assertSame('Hello World', $command->output);

        $commandLineLong = ['message', '--use=World', 'Hello'];
        $this->assertSame($input, $command->parse($commandLineLong));
    }

    private function helloCommand(): Command
    {
        return new class () extends Command {
            public string $output = '';

            public function __construct()
            {
                parent::__construct(
                    'message',
                    '',
                    [new Argument('salutation', '', true)],
                    [new Option('use', '')]
                );
            }

            public function execute(): void
            {
                $s = $this->get('salutation');
                $u = $this->get('use');
                $this->output = "$s $u";
            }
        };
    }

}
