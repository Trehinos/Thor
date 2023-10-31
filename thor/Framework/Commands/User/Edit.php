<?php

namespace Thor\Framework\Commands\User;

use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Cli\Command\Command;
use Thor\Framework\Security\DbUser;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Framework\Managers\UserManager;
use Thor\Database\PdoExtension\Requester;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Framework\Configurations\DatabasesConfiguration;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Edit extends Command
{

    public function execute(): void
    {
        $manager = new UserManager(
            new CrudHelper(
                DbUser::class,
                new Requester(PdoCollection::createFromConfiguration(DatabasesConfiguration::get())->get())
            )
        );

        $pid = $this->get('pid');
        $username = $this->get('username');
        $password = $this->get('password');

        if ($pid === null) {
            $this->error('Invalid usage, PID is required.');
        }

        if ($username !== null) {
            $manager->updateUser($pid, $username);
            $this->console->echoes(Color::FG_GREEN, "User's username has been modified.\n");
        }
        if ($password !== null) {
            $manager->setPassword($pid, $password);
            $this->console->echoes(Color::FG_GREEN, "User's password has been modified.\n");
        }
        if ($username === null && $password === null) {
            $this->console->echoes(Mode::DIM, Color::FG_GRAY, "Nothing to do.\n");
        }
        $this->console->echoes("Done\n");
    }

}
