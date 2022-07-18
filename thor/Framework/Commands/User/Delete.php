<?php

namespace Thor\Framework\Commands\User;

use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Process\Command;
use Thor\Framework\Security\DbUser;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Framework\Managers\UserManager;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Framework\Configurations\DatabasesConfiguration;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Delete extends Command
{

    public function execute(): void
    {
        $manager = new UserManager(
            new CrudHelper(
                DbUser::class,
                new PdoRequester(PdoCollection::createFromConfiguration(DatabasesConfiguration::get())->get())
            )
        );

        $pid = $this->get('pid');
        if (null === $pid) {
            $this->error('PID not provided...');
        }
        $user = $manager->getFromPublicId($pid);
        if (null === $user) {
            $this->error('User not found...');
        }

        $manager->deleteOne($pid);
        $this->console->fColor(Color::GREEN)->writeln('Success');
        $this->console->fColor()->writeln("User $pid deleted.");
    }

}
