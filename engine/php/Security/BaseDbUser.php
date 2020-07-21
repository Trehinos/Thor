<?php

namespace Thor\Security;

use Exception;

use Thor\Database\PdoExtension\PdoRowInterface;
use Thor\Database\PdoExtension\PdoRowTrait;

abstract class BaseDbUser implements PdoRowInterface
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

    protected static function getTableColumns(): array
    {
        return [
            'username' => 'VARCHAR(255) NOT NULL',
            'password' => 'VARCHAR(255) NOT NULL',
        ];
    }

    protected function toPdo(): array
    {
        return [
            'username' => $this->getUsername(),
            'password' => $this->getPwdHash()
        ];
    }

    protected function fromPdo(array $pdoArray)
    {
        $this->setUsername($pdoArray['username']);
        $this->setPwdHash($pdoArray['password']);
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
