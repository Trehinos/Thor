<?php

namespace Thor\Framework\CliCommands\User;

use Thor\Cli\Console\Color;
use Thor\Process\CliCommand;
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
final class Create extends CliCommand
{

    public function execute(): void
    {
        $manager = new UserManager(
            new CrudHelper(
                DbUser::class,
                new PdoRequester(PdoCollection::createFromConfiguration(DatabasesConfiguration::get())->get())
            )
        );

        $username = $this->get('username');
        $password = $this->get('password', null);

        if ($password === null) {
            $password = base64_encode(random_bytes(16));
            $this->console->echoes(
                "User's password [",
                Color::FG_YELLOW,
                $password,
                Color::FG_GRAY,
                "] has been generated...\n"
            );
        }
        $pid = $manager->createUser($username, $password);
        $this->console->echoes("User ", Color::FG_YELLOW, $pid, Color::FG_GRAY, " has been created.\n");
        $this->console->echoes("Done\n");
    }

}
