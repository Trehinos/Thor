<?php

namespace Thor\Framework\Commands;

use Exception;
use Thor\Framework\{Managers\UserManager};
use Thor\Framework\Security\DbUser;
use Thor\Cli\{Command, CliKernel, Console\Mode, Console\Color};
use Thor\Database\{PdoTable\Criteria, PdoTable\CrudHelper, PdoExtension\PdoRequester};

/**
 * This Command contains user management Thor-Api commands :
 *  - user/create
 *  - user/edit
 *  - user/delete
 *  - user/list
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class UserCommand extends Command
{

    private UserManager $userManager;

    /**
     * @param string    $command
     * @param array     $args
     * @param CliKernel $kernel
     */
    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
        $this->userManager = new UserManager(
            new CrudHelper(
                DbUser::class,
                new PdoRequester($kernel->pdos->get())
            )
        );
    }

    /**
     * Thor\Framework user/create -username USERNAME -password CLEAR_PASSWORD
     *
     * @throws Exception
     */
    public function createUser(): void
    {
        $username = $this->get('username');
        $password = $this->get('password');

        if (null === $username || null === $password) {
            $this->console->fColor(Color::RED)
                          ->writeln('ERROR');
            $this->console->fColor()
                          ->writeln('Username or password not provided...');
            throw new Exception("Command error : create-user : username or password not provided by user.");
        }

        $pid = $this->userManager->createUser($username, $password);
        $this->console->fColor(Color::GREEN)
                      ->writeln('Success');
        $this->console->fColor()->writeln("User PID=$pid created.");
    }

    /**
     * Thor\Framework user/create -pid PID [-username NEW_USERNAME] [-password NEW_CLEAR_PASSWORD]
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

        $this->console->fColor(Color::GREEN)->writeln('Success');
        $this->console->fColor()->writeln("User $pid edited.");
    }

    /**
     * @param string|null $pid
     *
     * @throws Exception
     */
    public function guardPid(?string $pid)
    {
        if (null === $pid) {
            $this->console->fColor(Color::RED)->writeln('ERROR');
            $this->console->fColor()->writeln('PID not provided...');
            throw new Exception("Command error : edit-user : PID not provided by user.");
        }

        $user = $this->userManager->getFromPublicId($pid);
        if (null === $user) {
            $this->console->fColor(Color::RED)->writeln('ERROR');
            $this->console->fColor()->writeln('User not found...');
            throw new Exception("Command error : edit-user : User ($pid) not found.");
        }
    }

    /**
     * Thor\Framework user/delete -pid PID
     *
     * @throws Exception
     */
    public function deleteUser(): void
    {
        $pid = $this->get('pid');
        $this->guardPid($pid);
        $this->userManager->deleteOne($pid);

        $this->console->fColor(Color::GREEN)->writeln('Success');
        $this->console->fColor()->writeln("User $pid deleted.");
    }

    /**
     * Thor\Framework user/list [-search SEARCH_STRING]
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
         * @var DbUser $user
         */
        foreach ($users as $user) {
            $pid = $user->getPublicId();
            $username = $user->getIdentifier();
            $hash = $user->toPdoArray()['password'];
            $this->console->fColor(mode: Mode::DIM)
                          ->write("[$pid] ")
                          ->mode()->fColor()->write('username:')
                          ->fColor(Color::CYAN)
                          ->writeln($username)
                          ->mode()->fColor()->write("password:")
                          ->fColor(Color::MAGENTA)
                          ->writeln($hash)
                          ->mode();
        }

        $this->console->writeln()->fColor(Color::GREEN)->write(count($users))
                      ->fColor()->writeln(" user(s) listed");
    }

}
