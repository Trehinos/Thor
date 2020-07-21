<?php

namespace Thor\App\Managers;

use Thor\App\Entities\User;
use Thor\Database\CrudHelper;
use Thor\Database\PdoExtension\PdoRowInterface;
use Thor\Debug\Logger;

final class UserManager
{

    private CrudHelper $userCrud;

    public function __construct(CrudHelper $userCrud)
    {
        $this->userCrud = $userCrud;
    }

    public function createUser(string $username, string $password): string
    {
        $public_id = $this->userCrud->createOne(
            new User($username, $password)
        );
        Logger::write("User $public_id created.", Logger::VERBOSE);

        return $public_id;
    }

    public function updateUser(string $public_id, string $username): bool
    {
        $state = false;
        $user = $this->userCrud->readOneFromPid($public_id);
        if ($user) {
            $user->setUsername($username);
            $state = $this->userCrud->updateOne($user);
        }

        return $state;
    }

    public function getUserCrud(): CrudHelper
    {
        return $this->userCrud;
    }

}
