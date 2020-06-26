<?php

namespace Thor\Security;

use Thor\Database\PdoRowInterface;
use Thor\Database\PdoRowTrait;

final class User implements PdoRowInterface
{

    use PdoRowTrait;

    private string $username;
    private string $pwd_hash;

    public function __construct(string $username = '', string $clearPwd = '')
    {
        $hashMaker = new PasswordHash();
        $this->username = $username;
        $this->pwd_hash = $hashMaker->hash($clearPwd);
    }

    public function toPdoArray(): array
    {
        return [
            'id' => $this->id,
            'public_id' => $this->public_id,
            'username' => $this->username,
            'password' => $this->pwd_hash
        ];
    }

    public function fromPdoArray(array $pdoArray)
    {
        $this->setId($pdoArray['id']);
        $this->setPublicId($pdoArray['public_id']);
        $this->username = $pdoArray['username'];
        $this->pwd_hash = $pdoArray['password'];
    }

}
