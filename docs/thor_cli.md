# Thor Cli

```Thor\Cli``` is the namespace of the CLI environment control classes.

This module consist of the CliKernel, the Command base class, the Daemon definition and a Console utility class.

## The CLI kernel

```Thor\Cli\CliKernel``` is the invoked kernel when the running entry point is ```thor/bin/thor.php```.

### Kernel execution

1. Read configuration in ```thor/app/res/static/commands.yml```.
2. Read arguments from command line.
3. Execute the right command or echo error.

#### How to define a command :

1. Declare the command in ```commands.yml``` :

```yml
module/command:
    class: App\Commands\Module
    command: command
    description: My awesome command
    arguments:
        argument:
            hasValue: true
            description: an interstesting argument description
```

2. Create the class ```App\Commands\Module``` :

```php
namespace App\Commands;

use Thor\Cli\Command;
use Thor\Cli\Console;
use Thor\Cli\CliKernel;

final class Module extends Command {
    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
    }
    
    public function command()
    {
        $argument = $this->get('argument');
        if (null === $argument) {
            $this->error('Usage error : ', 'argument is required');
        }
        
        $this->console
                    ->fColor(Console::COLOR_YELLOW)
                    ->write('My argument is : ')
                    ->fColor(Console::COLOR_CYAN)
                    ->writeln($argument)
                    ->mode()
        ;
    }
}
```

3. Execute the command :
   * Execute : ```php bin/thor.php module/command -argument "This is my argument""```

> Type ```php bin/thor.php -help``` to see the commands list.

## Abstract Command and Console utility class

A command to be executed by the CLI kernel **has to** expand the class ```Thor\Cli\Command```.  
**One CLI command = one method** (like one route = one method in controllers).

### Command ```class``` ```abstract```

* ```__construct(string $name, array $args, CliKernel $cli)```
* ```set(string $arg, string|bool $value = true): void```
* ```get(string $arg): string|bool|null```
* ```usage(?string $name = null): void```
* ```error(string $title, string $message, bool $displayUsage = false, bool $displayHelp = false): void```
* ```help(?string $name = null): void```
* ```php
    static function getArgs(
        array $argumentsFromCommandLine,
        #[ArrayShape([['hasValue' => 'bool|null', 'description' => 'string|null', 'class' => 'string']])]
        array $argsSpec
    ): array;
  ```

### Console ```class``` ```final```

```Thor\Cli\Console``` utility class. This class is a fluid interface to get an easy way to write
in colors and control the location of the printed text.

* Public constants

```php
 /** COLOR SEQUENCE */
const COLOR_START = "\033[";
const COLOR_END = 'm';

/** CURSOR/COLORS STATES */
const MODE_RESET = 0;
const MODE_BRIGHT = 1;
const MODE_DIM = 2;
const MODE_UNDERSCORE = 3;
const MODE_BLINK = 5;
const MODE_REVERSE = 7;
const MODE_HIDDEN = 8;

/** COLOR MASKS */
const COLOR_BLACK = 0;
const COLOR_RED = 1;
const COLOR_GREEN = 2;
const COLOR_YELLOW = 3;
const COLOR_BLUE = 4;
const COLOR_MAGENTA = 5;
const COLOR_CYAN = 6;
const COLOR_GRAY = 7;

/** CLEAR SCREEN SEQUENCE */
const CLEAR = "\033[H\033[J";

/** CURSOR CONTROL SEQUENCES */
const CURSOR_HOME = "\033[0K";
const CURSOR_UP = "\033[yA";
const CURSOR_DOWN = "\033[yB";
const CURSOR_RIGHT = "\033[xC";
const CURSOR_LEFT = "\033[xD";
const CURSOR_POS = "\033[y;xf";
const CURSOR_SAVE = "\033[s";
const CURSOR_UNSAVE = "\033[u";
const CURSOR_SAVEALL = "\0337"; 
const CURSOR_RESTORE = "\0338"; 
```

* ```__construct()```
* ```__destruct()```
* ```fColor(int $fc = self::COLOR_GRAY, ?int $mode = null): self```
* ```bColor(int $bc = self::COLOR_BLACK, ?int $mode = null): self```
* ```mode(int $mode = self::MODE_RESET): self```
* ```moveUp(int $y): self```
* ```moveDown(int $y): self```
* ```moveLeft(int $x): self```
* ```moveRight(int $x): self```
* ```locate(int $y, int $x): self```
* ```home(): self```
* ```clear(): self```
* ```write(string $text): self```
* ```writeFix(string $text, int $minSize, int $direction = STR_PAD_RIGHT): self```
* ```writeInline(string $text): self```
* ```writeln(string $text = ''): self```

## Daemons

In **Thor**, a daemon is defined as a piece of PHP code, embedded by the framework, which is *periodically* executed by
a **DaemonScheduler** as a *background task* during its *active period*.

### Daemon public API

#### Daemon ```class``` ```abstract```

* ```__construct(string $name, int $periodicityInMinutes, string $startHi = '0000', string $endHi = '2359', bool $enabled = false)```
* ```getName(): string```
* ```getPeriodicity(): int```
* ```isEnabled(): bool```
* ```isActive(): bool```
* ```isNowRunnable(?DateTime $lastTime = null): bool```
* ```final executeIfRunnable(DaemonState $state): void```
* ```final getStartToday(): DateTime```
* ```final getEndToday(): DateTime```
* ```final static instantiate(array $info): Daemon```

#### DaemonState ```class``` ```final```

* ```__construct(Daemon $daemon)```
* ```load(): void```
* ```write(): void```
* ```getFileName(): string```
* ```getPid(): ?string```
* ```setPid(?string $pid): void```
* ```error(?string $errorMessage): void```
* ```getError(): ?string```
* ```setRunning(bool $running): void```
* ```isRunning(): bool```
* ```getLastRun(): ?DateTime```
* ```setLastRun(?DateTime $lastRun): void```

### Daemon CLI commands :

Run a command with ```php bin/thor.php [command]``` :

* ```daemon/start -name [daemonName]``` : enables the daemon (can now be executed during its **active period**).
* ```daemon/stop -name [daemonName]``` : disables the daemon. It doesn't kill the daemon if it is running.
* ```daemon/status -name [daemonName]``` : displays the complete state of a daemon.
* ```daemon/status -all``` : displays every daemons status in a table.
* ```daemon/kill -name [daemonName]``` : kills a running daemon.
* ```daemon/reset -name [daemonName]``` : resets the state of a daemon. Sets ```lastRun``` and ```error``` to ```null```
  .
  **The daemon will be executed the next minute.**

### Example : Create a daemon and CRON DaemonScheduler

1. Create the class ```App\Daemons\MyDaemon``` in ```thor/app/src/Daemons/``` :
   ```php
    namespace App\Daemons;
    
    use Thor\Cli\Daemon;
    
    final class MyDaemon extends Daemon
    {
        public function execute(): void
        {
            echo "Hello World\n";
            sleep(60);
        }
    }
    ```
   > This daemon will output "Hello World" in ```thor/var/logs/my_daemon/output.log``` then wait 60 seconds then end.
2. In ```thor/app/res/static/daemons``` create the file ```my_daemon.yml``` :
    ```yaml
    name: my_daemon
    class: App\Daemons\MyDaemon
    periodicity: 5
    start: '0800'
    end: '2000'
    enabled: false
    ```
   > This daemon will be executed **every 5 minutes** between 08:00:00 and 20:00:59.

   **WARNING** : If the daemon run for more than 5 minutes, the DaemonScheduler will not execute the daemon a second
   time. The daemon will be executed the next time the DaemonScheduler is executed and the daemon is **not running**.

3. CRON **every minute** the command ```php thor/bin/daemon.php``` which executes the **DaemonScheduler**.
    - On **GNU/Linux**, run ```crontab -e``` and edit the file ([Cron](https://en.wikipedia.org/wiki/Cron)).
    - On **Windows**, type "Task Scheduler" in the start
      menu ([Windows Task Scheduler](https://en.wikipedia.org/wiki/Windows_Task_Scheduler)).  
      Be warned that on Windows, you may have to declare 5 triggers to have a 1 minute granularity.

4. In a terminal, run ```php thor/bin/thor.php daemon/start -name my_daemon``` to enable the daemon.
