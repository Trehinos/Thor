<?php

namespace App\Commands;

use Symfony\Component\Yaml\Yaml;
use Thor\Cli\CliKernel;
use Thor\Cli\Command;
use Thor\Cli\Console;
use Thor\Globals;
use Thor\Thor;

final class CoreCommand extends Command
{

    private array $routes;

    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
        $this->routes = Thor::getInstance()->loadConfig('routes', true);
    }

    public function routeSet()
    {
        $name = $this->get('name');
        $path = $this->get('path');
        $method = $this->get('method');
        $cClass = $this->get('action-class');
        $cMethod = $this->get('action-method');

        if (in_array(null, [$name, $path, $method, $cClass, $cMethod])) {
            $this->error("Usage error\n", 'All parameters are required.', true, true);
        }

        $this->routes[$name] = [
            'path' => $path,
            'method' => $method,
            'action' => [
                'class' => $cClass,
                'method' => $cMethod
            ]
        ];

        file_put_contents(Globals::CODE_DIR . 'app/res/routes.yml', Yaml::dump($this->routes));
        $this->console
            ->fColor(Console::COLOR_GREEN, Console::MODE_BRIGHT)
            ->writeln("Done.")
            ->mode();
    }

    public function routeList()
    {
        foreach ($this->routes as $routeName => $route) {
            if ($routeName === 'load') {
                continue;
            }

            $this->console
                ->fColor(Console::COLOR_YELLOW, Console::MODE_BRIGHT)
                ->write("$routeName : ")
                ->mode();

            if ($route['action'] ?? false) {
                $c = $route['action']['class'];
                $m = $route['action']['method'];
                $this->console
                    ->fColor(Console::COLOR_BLUE, Console::MODE_RESET)
                    ->write($c)
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write('::')
                    ->fColor(Console::COLOR_BLUE, Console::MODE_RESET)
                    ->write($m . '()')
                    ->mode();
            }

            if ($route['path'] ?? false) {
                $path = $route['path'] ?? null;
                $parameters = $route['parameters'] ?? [];
                foreach ($parameters as $pKey => $pValue) {
                    $path = str_replace("\$$pKey", "\e[0;32m\$$pKey\e[0m", $path);
                }
                $this->console
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write(' <- ')
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write('[')
                    ->fColor(Console::COLOR_CYAN, Console::MODE_BRIGHT)
                    ->write($route['method'] ?? '')
                    ->fColor(Console::COLOR_GRAY, Console::MODE_RESET)
                    ->write(' ' . $path ?? '')
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write(']')
                    ->mode();
            }

            $this->console->writeln();
        }
    }

}
