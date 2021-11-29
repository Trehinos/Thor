<?php

namespace Thor\Framework\Managers;

use Exception;
use Thor\Database\PdoTable\{Criteria, CrudHelper};
use Thor\Debug\{Logger, LogLevel};
use Thor\Security\Identity\DbUser;


/**
 * User manager class.
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class UserManager
{


    /**
     * @param CrudHelper<DbUser> $userCrud
     */
    public function __construct(private CrudHelper $userCrud)
    {
    }

    /**
     * Generates a random password.
     *
     * @param int $size (default 16)
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
     * Creates a user with specified username and password and returns public ID.
     *
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

    /**
     * Updates a user in the database from its public ID.
     *
     * Returns `false` if the query fail or the user has not been persisted yet.
     *
     * @param string $public_id
     * @param string $username
     *
     * @return bool
     */
    public function updateUser(string $public_id, string $username): bool
    {
        $state = false;
        $user = $this->userCrud->readOneBy(new Criteria(['public_id' => $public_id]));
        if ($user) {
            $user->setUsername($username);
            $state = $this->userCrud->updateOne($user);
            Logger::write("User $public_id updated !", LogLevel::NOTICE);
        }

        return $state;
    }

    /**
     * Sets the password of an existing user identified by the specified public ID.
     *
     * Returns `false` if the query fail or the user has not been persisted yet.
     *
     * @param string $public_id
     * @param string $password
     *
     * @return bool
     */
    public function setPassword(string $public_id, string $password): bool
    {
        $state = false;
        $user = $this->userCrud->readOneBy(new Criteria(['public_id' => $public_id]));
        if ($user) {
            $user->setPwdHashFrom($password);
            $state = $this->userCrud->updateOne($user);
            Logger::write("User $public_id updated !", LogLevel::NOTICE);
        }

        return $state;
    }

    /**
     * Deletes a user from its public ID.
     *
     * Returns `false` if the query fail or the user has not been persisted yet.
     *
     * @param string $public_id
     *
     * @return bool
     */
    public function deleteOne(string $public_id): bool
    {
        $state = false;
        $user = $this->userCrud->readOneBy(new Criteria(['public_id' => $public_id]));
        if ($user) {
            $state = $this->userCrud->deleteOne($user);
            Logger::write("User $public_id deleted !", LogLevel::NOTICE);
        }

        return $state;
    }

    /**
     * Gets a user from Database from its username.
     *
     * Returns `null` if the user is not found.
     *
     * @param string $username
     *
     * @return DbUser|null
     */
    public function getFromUsername(string $username): ?DbUser
    {
        return $this->userCrud->readOneBy(new Criteria(['username' => $username]));
    }

    /**
     * Verify if the clear password specified corresponds the user found with the specified public ID.
     *
     * Returns true if it corresponds, false if not and null if the user is not found.
     *
     * @param string $public_id
     * @param string $clearPassword
     *
     * @return bool|null
     */
    public function verifyUserPassword(string $public_id, string $clearPassword): ?bool
    {
        $user = $this->getFromPublicId($public_id);

        return $user?->isPassword($clearPassword);
    }

    /**
     * Gets a user from Database from its public ID.
     *
     * Returns `null` if the user is not found.
     *
     * @param string $public_id
     *
     * @return DbUser|null
     */
    public function getFromPublicId(string $public_id): ?DbUser
    {
        return $this->userCrud->readOneBy(new Criteria(['public_id' => $public_id]));
    }

    /**
     * Gets the CrudHelper of this manager.
     *
     * @return CrudHelper<DbUser>
     */
    public function getUserCrud(): CrudHelper
    {
        return $this->userCrud;
    }

}
