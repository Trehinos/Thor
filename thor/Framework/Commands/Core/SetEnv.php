<?php

namespace Thor\Framework\Commands\Core;

use Thor\Env;
use Thor\Globals;
use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Process\Command;
use Symfony\Component\Yaml\Yaml;
use Thor\Configuration\ThorConfiguration;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class SetEnv extends Command
{

    public function execute(): void
    {
        $env = strtoupper($this->get('env', ''));

        if (!in_array($env, [Env::PROD->value, Env::DEBUG->value, Env::DEV->value])) {
            $this->error("Env MUST be one of : PROD, DEBUG or DEV.");
        }

        $config = ThorConfiguration::get()->getArrayCopy();
        $config['env'] = $env;
        $config['thor_kernel'] = null;
        unset($config['thor_kernel']);

        file_put_contents(Globals::CONFIG_DIR . 'config.yml', Yaml::dump($config));
        $this->console
            ->fColor(Color::GREEN, Mode::BRIGHT)
            ->writeln("Done.")
            ->mode()
        ;
    }

}
