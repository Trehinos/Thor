# Thor Cli

```Thor\Cli``` is the namespace of the CLI environment control classes.

This module consist of the CliKernel, the Command base class, the Daemon definition and a Console utility class.

## The CLI kernel

```Thor\Cli\CliKernel``` is the invoked kernel when the running entry point is ```thor/bin/thor.php```.

### Kernel execution

1. Read configuration in ```thor/app/res/static/commands.yml```.
2. Read arguments from command line.
3. Execute the right command or echo error.

## Abstract Command and Console utility class

A command to be executed by the CLI kernel **has to** expand the class ```Thor\Cli\Command```.  
**One CLI command = one method** (like one route = one method in controllers).

> You can use the ```console``` property to control the terminal output :
> ```php
> use Thor\Cli\Console;
>
> // in a command, use $this->console-> : 
> 
> function fColor(int $fc = Console::COLOR_GRAY, ?int $mode = null) {} // Change Foreground color.
> 
> function bColor(int $bc = Console::COLOR_BLACK, ?int $mode = null) {} // Change Background color. 
> 
> function mode(int $mode = Console::MODE_RESET) {} // Change mode
> 
> // Locate the cursor at the specified row ($y) and column ($x).
> function locate(int $y, int $x) {} 
> 
> function clear() {} // Clear the screen
> 
> function write(string $text) {} // Write text.
> 
> // Write text and places cursor at the begining of the same line.
> function writeInline(string $text) {}
> 
> // Write text and a new line. 
> function writeln(string $text = '') {}
> 
> ```

## Daemons

In **Thor**, a daemon is defined as a piece of PHP code, embedded by the framework, which is *periodically* executed by
a **DaemonScheduler** as a *background task* during its *active period*.

### Daemon public API

#### Daemon ```class``` ```abstract```

* ```__construct(string $name, int $periodicityInMinutes, string $startHi = '000000', string $endHi = '235959', bool $enabled = false)```
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
* ```daemon/stop -name [daemonName]``` : disables the daemon (not executed even during its **active period**). Doesn't
  kill the daemon if it is running.
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

4. In a terminal, run ```php thor/bin/thor.php daemon/start -name my_daemon``` to enable the daemon.
