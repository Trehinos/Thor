<?php

namespace Thor\Framework\Commands\Http;

use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Cli\Command\Command;
use Thor\Framework\Factories\RouterFactory;
use Thor\Framework\Configurations\RoutesConfiguration;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class RoutesList extends Command
{

    public function execute(): void
    {
        $routes = RouterFactory::createRoutesFromConfiguration(RoutesConfiguration::get('web'));

        foreach ($routes as $route) {
            $routeName = $route->getRouteName();

            $this->console
                ->mode()
                ->write("$routeName : ")
            ;

            $c = $route->getControllerClass();
            $m = $route->getControllerMethod();
            $this->console
                ->mode()
                ->fColor(Color::YELLOW)
                ->write($c)
                ->mode()
                ->fColor(Color::GRAY, Mode::DIM)
                ->write('::')
                ->mode()
                ->fColor(Color::BLUE, Mode::BRIGHT)
                ->write($m)
                ->mode()
                ->mode(Mode::DIM)
                ->write('()')
                ->mode()
            ;

            $path = $route->getPath();
            if (null !== $path) {
                $parameters = $route->getParameters();
                foreach ($parameters as $pKey => $pValue) {
                    $path = str_replace("\$$pKey", "\e[3;1;32m\$$pKey\e[0m", $path);
                }
                $this->console
                    ->fColor()
                    ->write(' <- ')
                    ->fColor(Color::GRAY, Mode::DIM)
                    ->write('[')
                    ->fColor(Color::CYAN, Mode::BRIGHT)
                    ->write($route->getMethod()->value ?? '')
                    ->fColor(Color::GRAY, Mode::RESET)
                    ->write(' ' . $path ?? '')
                    ->fColor(Color::GRAY, Mode::DIM)
                    ->write(']')
                    ->mode()
                ;
            }
            $this->console->writeln();
        }
    }

}
