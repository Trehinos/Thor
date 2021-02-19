<?php

namespace Thor\Api\Commands;

use Thor\Api\Managers\UserManager;
use Symfony\Component\Yaml\Yaml;
use Thor\Cli\CliKernel;
use Thor\Cli\Command;
use Thor\Cli\Console;
use Thor\Database\CrudHelper;
use Thor\Database\PdoExtension\Attributes\PdoAttributesReader;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\SchemaHelper;
use Thor\Debug\Logger;
use Thor\Globals;
use Thor\Http\HttpKernel;
use Thor\Http\Routing\Route;
use Thor\Api\Entities\User;
use Thor\Thor;

final class CoreCommand extends Command
{

    private array $routes;

    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
        $this->routes =
            HttpKernel::createRouterFromConfiguration(
                Thor::getInstance()->loadConfig('routes', true)
            )->getRoutes();
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

    public function setup()
    {
        $requester = new PdoRequester($this->cli->pdos->get());

        Logger::write("SETUP : Creating table user...", Logger::LEVEL_PROD);
        $schema = new SchemaHelper($requester, new PdoAttributesReader(User::class));
        $schema->createTable();

        $userManager = new UserManager(new CrudHelper(User::class, $requester));
        $pid = $userManager->createUser('admin', 'password');
        Logger::write("SETUP : Admin $pid created.", Logger::LEVEL_PROD);
    }

    public function routeList()
    {
        /** @var Route $route */
        foreach ($this->routes as $route) {
            $routeName = $route->getRouteName();

            $this->console
                ->fColor(Console::COLOR_YELLOW, Console::MODE_BRIGHT)
                ->write("$routeName : ")
                ->mode();

            $c = $route->getControllerClass();
            $m = $route->getControllerMethod();
            $this->console
                ->fColor(Console::COLOR_BLUE, Console::MODE_RESET)
                ->write($c)
                ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                ->write('::')
                ->fColor(Console::COLOR_BLUE, Console::MODE_RESET)
                ->write($m . '()')
                ->mode();

            $path = $route->getPath();
            if (null !== $path) {
                $parameters = $route->getParameters();
                foreach ($parameters as $pKey => $pValue) {
                    $path = str_replace("\$$pKey", "\e[0;32m\$$pKey\e[0m", $path);
                }
                $this->console
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write(' <- ')
                    ->fColor(Console::COLOR_GRAY, Console::MODE_DIM)
                    ->write('[')
                    ->fColor(Console::COLOR_CYAN, Console::MODE_BRIGHT)
                    ->write($route->getMethod() ?? '')
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
