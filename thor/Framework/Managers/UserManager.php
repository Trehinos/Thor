<?php

/**
 * TODO ...
 *
 * @package          Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Framework\Managers;

use Exception;
use Thor\Database\PdoTable\{Criteria, CrudHelper};
use Thor\Debug\{Logger, LogLevel};
use Thor\Security\Identity\DbUser;

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
        $user = new DbUser($username, $clearPassword);
        $user->generatePublicId();
        $public_id = $user->getPublicId();
        $this->userCrud->createOne($user);
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

    public function getFromUsername(string $username): ?DbUser
    {
        return $this->userCrud->readOneBy(new Criteria(['username' => $username]));
    }

    public function verifyUserPassword(string $public_id, string $clearPassword): bool
    {
        $user = $this->getFromPublicId($public_id);

        return (null === $user) ? false : $user->isPassword($clearPassword);
    }

    public function getFromPublicId(string $public_id): ?DbUser
    {
        return $this->userCrud->readOneFromPid($public_id);
    }

    public function getUserCrud(): CrudHelper
    {
        return $this->userCrud;
    }

}
