<?php

namespace Thor\Security;

use Exception;
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

    public static function getPdoColumnsDefinitions(): array
    {
        return [
            'id' => 'INT PRIMARY KEY',
            'public_id' => 'VARCHAR(255)',
            'username' => 'VARCHAR(255)',
            'password' => 'VARCHAR(255)',
        ];
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

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPwdHash(): string
    {
        return $this->pwd_hash;
    }

    public function setPwdHash(string $pwd_hash): void
    {
        $this->pwd_hash = $pwd_hash;
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
