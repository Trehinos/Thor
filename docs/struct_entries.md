# Thor' entry points

The **Thor** main script is ```thor/application.php```. This script is responsible to load the configuration, create the
application with a **Kernel** corresponding the entry point then execute the application with its context.

## Entry point and Kernel

An entry point file is overly simple. For example ```thor/web/index.php``` :

```php
$thor_kernel = 'http';
require_once __DIR__ . '/../application.php';
```

This file's role is to tell the application which kernel we want to use.

### Custom kernels

For example, add a Custom Kernel which prints "Hello world" in a terminal :

Add the kernel in the kernels list. They are listed in ```thor/app/res/static/kernels.yml``` :

```yaml
cli: Thor\Cli\CliKernel
daemon: Thor\Cli\DaemonScheduler
http: Thor\Http\HttpKernel
custom: App\Kernels\CustomKernel
```

Create ```thor/app/src/Kernels/CustomKernel.php``` :

```php
namespace App\Kernels;

use Thor\KernelInterface;

class CustomKernel implements KernelInterface
{

    public function __construct(private string $name)
    {
    }
    
    public function execute() : void
    {
        echo "Hello {$this->name}\n";
    }
    
    public static function createFromConfiguration(array $config = []) : static
    {
        return new self($config['name'] ?? 'world');
    }
    
    public static function create() : static
    {
        return static::createFromConfiguration(['name' => 'world']);
    }

}
```

#### Custom entry point

To run the custom kernel, we have to create a new entry point.

Create an entry point in ```thor/entry/custom.php``` :
```php
$thor_kernel = 'custom';
require_once __DIR__ . '/../application.php';
```

Run the entry point in a terminal : ```php entry/custom.php```.

## CLI Entry point

## The DaemonScheduler

## The Http Entry point
