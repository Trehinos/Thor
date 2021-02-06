# Thor Cli
```Thor\Cli``` is the namespace of the CLI environment control classes.

This module consist of the CliKernel, the Command base class,
the Daemon definition and a Console utility class.

## The CLI kernel
```Thor\Cli\CliKernel``` is the invoked kernel when the running
entry point is ```thor/bin/thor.php```.

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
*Not implemented yet*