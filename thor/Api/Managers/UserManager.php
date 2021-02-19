<?php

namespace Thor\Api\Managers;

use Exception;

use Thor\Api\Entities\User;
use Thor\Database\CrudHelper;
use Thor\Database\Sql\Criteria;
use Thor\Debug\Logger;

final class UserManager
{

    private CrudHelper $userCrud;

    public function __construct(CrudHelper $userCrud)
    {
        $this->userCrud = $userCrud;
    }

    /**
     * @param string $username
     * @param string $clearPassword
     *
     * @return string public_id
     */
    public function createUser(string $username, string $clearPassword): string
    {
        $public_id = $this->userCrud->createOne(
            new User($username, $clearPassword)
        );
        Logger::write("User $public_id created.", Logger::LEVEL_VERBOSE);

        return $public_id;
    }

    public function updateUser(string $public_id, string $username): bool
    {
        $state = false;
        $user = $this->userCrud->readOneFromPid($public_id);
        if ($user) {
            $user->setUsername($username);
            $state = $this->userCrud->updateOne($user);
            Logger::write("User $public_id updated !", Logger::LEVEL_VERBOSE);
        }

        return $state;
    }

    public function setPassword(string $public_id, string $password): bool
    {
        $state = false;
        $user = $this->userCrud->readOneFromPid($public_id);
        if ($user) {
            $user->setPwdHashFrom($password);
            $state = $this->userCrud->updateOne($user);
            Logger::write("User $public_id updated !", Logger::LEVEL_VERBOSE);
        }

        return $state;
    }

    public function deleteOne(string $public_id): bool
    {
        $state = false;
        $user = $this->userCrud->readOneFromPid($public_id);
        if ($user) {
            $state = $this->userCrud->deleteOne($user);
            Logger::write("User $public_id deleted !", Logger::LEVEL_VERBOSE);
        }

        return $state;
    }

    public function getFromPublicId(string $public_id): ?User
    {
        return $this->userCrud->readOneFromPid($public_id);
    }

    public function getFromUsername(string $username): ?User
    {
        return $this->userCrud->readOneBy(new Criteria(['username' => $username]));
    }

    public function verifyUserPassword(string $public_id, string $clearPassword): bool
    {
        $user = $this->getFromPublicId($public_id);

        return (null === $user) ? false : $user->hasPwdHashFor($clearPassword);
    }

    public function getUserCrud(): CrudHelper
    {
        return $this->userCrud;
    }

    /**
     * @param int $size
     *
     * @return string
     *
     * @throws Exception
     */
    public static function generatePassword(int $size = 16): string
    {
        return str_replace(
            ['/', '+'],
            ['#', '$'],
            trim(base64_encode(random_bytes($size)), '=')
        );
    }

}
