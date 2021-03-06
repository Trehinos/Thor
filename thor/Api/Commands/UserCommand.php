<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Commands;

use Exception;
use Thor\Api\Managers\UserManager;
use Thor\Cli\CliKernel;
use Thor\Cli\Command;
use Thor\Cli\Console;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\Criteria;
use Thor\Api\Entities\User;

final class UserCommand extends Command
{

    private UserManager $userManager;

    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
        $this->userManager = new UserManager(
            new CrudHelper(
                User::class,
                new PdoRequester($kernel->pdos->get('default'))
            )
        );
    }

    /**
     * @param string|null $pid
     *
     * @throws Exception
     */
    public function guardPid(?string $pid)
    {
        if (null === $pid) {
            $this->console->fColor(Console::COLOR_RED)->writeln('ERROR');
            $this->console->fColor()->writeln('PID not provided...');
            throw new Exception("Command error : edit-user : PID not provided by user.");
        }

        $user = $this->userManager->getFromPublicId($pid);
        if (null === $user) {
            $this->console->fColor(Console::COLOR_RED)->writeln('ERROR');
            $this->console->fColor()->writeln('User not found...');
            throw new Exception("Command error : edit-user : User ($pid) not found.");
        }
    }

    /**
     * Thor\Api user/create -username USERNAME -password CLEAR_PASSWORD
     *
     * @throws Exception
     */
    public function createUser(): void
    {
        $username = $this->get('username');
        $password = $this->get('password');

        if (null === $username || null === $password) {
            $this->console->fColor(Console::COLOR_RED)->writeln('ERROR');
            $this->console->fColor()->writeln('Username or password not provided...');
            throw new Exception("Command error : create-user : username or password not provided by user.");
        }

        $pid = $this->userManager->createUser($username, $password);
        $this->console->fColor(Console::COLOR_GREEN)->writeln('Success');
        $this->console->fColor()->writeln("User PID=$pid created.");
    }

    /**
     * Thor\Api user/create -pid PID [-username NEW_USERNAME] [-password NEW_CLEAR_PASSWORD]
     *
     * @throws Exception
     */
    public function editUser(): void
    {
        $pid = $this->get('pid');
        $username = $this->get('username');
        $password = $this->get('password');

        $this->guardPid($pid);
        if (null !== $username) {
            $this->userManager->updateUser($pid, $username);
        }
        if (null !== $password) {
            $this->userManager->setPassword($pid, $password);
        }

        $this->console->fColor(Console::COLOR_GREEN)->writeln('Success');
        $this->console->fColor()->writeln("User $pid edited.");
    }

    /**
     * Thor\Api user/delete -pid PID
     *
     * @throws Exception
     */
    public function deleteUser(): void
    {
        $pid = $this->get('pid');
        $this->guardPid($pid);
        $this->userManager->deleteOne($pid);

        $this->console->fColor(Console::COLOR_GREEN)->writeln('Success');
        $this->console->fColor()->writeln("User $pid deleted.");
    }

    /**
     * Thor\Api user/list [-search SEARCH_STRING]
     *
     * @throws Exception
     */
    public function listUsers(): void
    {
        $search = $this->get('search');

        if (null !== $search) {
            $users = $this->userManager->getUserCrud()->readMultipleBy(new Criteria(['username' => "%$search"]));
        } else {
            $users = $this->userManager->getUserCrud()->listAll();
        }

        /**
         * @var User $user
         */
        foreach ($users as $user) {
            $pid = $user->getPublicId();
            $username = $user->getUsername();
            $hash = $user->toPdoArray()['password'];
            $this->console->fColor(mode: Console::MODE_DIM)
                ->write("[$pid] ")
                ->mode()->fColor()->write('username:')
                ->fColor(Console::COLOR_CYAN)
                ->writeln($username)
                ->mode()->fColor()->write("password:")
                ->fColor(Console::COLOR_MAGENTA)
                ->writeln($hash)
                ->mode();
        }

        $this->console->writeln()->fColor(Console::COLOR_GREEN)->write(count($users))
            ->fColor()->writeln(" user(s) listed");
    }

}
