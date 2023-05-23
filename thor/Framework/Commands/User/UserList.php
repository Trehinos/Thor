<?php

namespace Thor\Framework\Commands\User;

use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Cli\Command\Command;
use Thor\Framework\Security\DbUser;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoExtension\Criteria;
use Thor\Framework\Managers\UserManager;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Framework\Configurations\DatabasesConfiguration;

/**
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class UserList extends Command
{

    public function execute(): void
    {
        $manager = new UserManager(
            new CrudHelper(
                DbUser::class,
                new PdoRequester(PdoCollection::createFromConfiguration(DatabasesConfiguration::get())->get())
            )
        );

        $search = $this->get('search');

        if (null !== $search) {
            $users = $manager->getUserCrud()->readMultipleBy(new Criteria(['username' => "%$search"]));
        } else {
            $users = $manager->getUserCrud()->listAll();
        }

        /**
         * @var DbUser $user
         */
        foreach ($users as $user) {
            $pid = $user->getPublicId();
            $username = $user->getIdentifier();
            $this->console->fColor(mode: Mode::DIM)
                          ->write("[$pid] ")
                          ->mode()->fColor()->write('username:')
                          ->fColor(Color::CYAN)
                          ->writeln($username)
                          ->mode()->fColor()->write("permissions:");
            if (empty($user->getPermissions())) {
                $this->console->echoes(
                    Color::BG_GRAY,
                    Color::FG_BLACK,
                    'none'
                );
            }
            foreach ($user->getPermissions() as $permission) {
                $this->console->echoes(
                    Mode::BRIGHT,
                    Mode::REVERSE,
                    Color::FG_MAGENTA,
                    "$permission",
                    Mode::RESET,
                    Color::BG_BLACK,
                    ' '
                );
            }
            $this->console->writeln()->writeln();
        }

        $this->console->writeln()->fColor(Color::GREEN)->write(count($users))
                      ->fColor()->writeln(" user(s) listed");
    }

}
