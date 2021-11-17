<?php

/**
 * TODO ...
 *
 * @package          Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Api\Managers;

use Exception;
use Thor\Api\Entities\User;
use Thor\Database\PdoTable\{Criteria, CrudHelper};
use Thor\Debug\{Logger, LogLevel};

final class UserManager
{


    public function __construct(private CrudHelper $userCrud)
    {
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

    /**
     * @param string $username
     * @param string $clearPassword
     *
     * @return string public_id
     *
     * @throws Exception
     */
    public function createUser(string $username, string $clearPassword): string
    {
        $public_id = $this->userCrud->createOne(
            new User($username, $clearPassword)
        );
        Logger::write("User $public_id created.", LogLevel::NOTICE);

        return $public_id;
    }

    public function updateUser(string $public_id, string $username): bool
    {
        $state = false;
        $user = $this->userCrud->readOneFromPid($public_id);
        if ($user) {
            $user->setUsername($username);
            $state = $this->userCrud->updateOne($user);
            Logger::write("User $public_id updated !", LogLevel::NOTICE);
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
            Logger::write("User $public_id updated !", LogLevel::NOTICE);
        }

        return $state;
    }

    public function deleteOne(string $public_id): bool
    {
        $state = false;
        $user = $this->userCrud->readOneFromPid($public_id);
        if ($user) {
            $state = $this->userCrud->deleteOne($user);
            Logger::write("User $public_id deleted !", LogLevel::NOTICE);
        }

        return $state;
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

    public function getFromPublicId(string $public_id): ?User
    {
        return $this->userCrud->readOneFromPid($public_id);
    }

    public function getUserCrud(): CrudHelper
    {
        return $this->userCrud;
    }

}
