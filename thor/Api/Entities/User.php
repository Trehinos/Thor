<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Entities;

use Thor\Database\PdoTable\AbstractPdoRow;
use Thor\Database\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\Attributes\PdoRow;
use Thor\Security\PasswordHasher;
use Thor\Security\UserInterface;

#[PdoRow('user', ['id'], 'id')]
#[PdoColumn('id', 'INTEGER', 'integer', false)]
#[PdoColumn('username', 'VARCHAR(255)', 'string', false)]
#[PdoIndex(['username'], true)]
#[PdoColumn('password', 'VARCHAR(255)', 'string', false)]
class User extends AbstractPdoRow implements UserInterface
{

    private static ?PasswordHasher $hashMaker = null;

    protected string $password;

    public function __construct(
        protected string $username = '',
        string $clearPassword = '',
        string $public_id = null,
        array $primary = [null]
    ) {
        parent::__construct($public_id, $primary);
        self::$hashMaker ??= new PasswordHasher();
        $this->password = self::$hashMaker->hash($clearPassword);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function hasPwdHashFor(string $clearPassword): bool
    {
        return PasswordHasher::verify($clearPassword, $this->password);
    }

    public function setPwdHashFrom(string $clearPassword): void
    {
        $this->password = self::$hashMaker->hash($clearPassword);
    }

}
