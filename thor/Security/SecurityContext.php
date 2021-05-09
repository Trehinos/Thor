<?php

/**
 * @package Trehinos/Thor/Security
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\Security;

use Exception;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\Criteria;
use Thor\Http\Server;

final class SecurityContext
{
    private bool $active;

    public string $userClass = '';
    public string $passwordMethod = '';

    public string $loginRoute = '';
    public string $logoutRoute = '';
    public string $checkRoute = '';

    public string $tokenKey = '';
    public int $tokenExpire = 0;

    private ?CrudHelper $userCrud = null;

    public function __construct(array $config, PdoCollection $pdoCollection)
    {
        $this->active = !empty($config);
        if ($this->active) {
            $this->userClass = $config['userPdoRow']['pdoRowClass'] ?? '';
            $this->passwordMethod = $config['userPdoRow']['passwordCompareMethod'] ?? '';
            $this->loginRoute = $config['configuration']['login-route'] ?? '';
            $this->logoutRoute = $config['configuration']['logout-route'] ?? '';
            $this->checkRoute = $config['configuration']['check-route'] ?? '';
            $this->tokenKey = $config['configuration']['session-key'] ?? '';
            $this->tokenExpire = $config['configuration']['token-expire'] ?? 60;
            $this->userCrud = new CrudHelper(
                $this->userClass,
                new PdoRequester(
                    $pdoCollection->get(
                        $config['configuration']['pdo-handler'] ?? 'default'
                    )
                )
            );
        }
    }

    public function isAuthenticated(?string $requestToken = null): bool
    {
        if (!$this->active) {
            return true;
        }

        $token = $this->getCurrentToken() ?? $requestToken;
        $tokenExpire = $this->getCurrentTokenExpire();
        $timeNow = $this->now();
        if (null === $token || $tokenExpire <= $timeNow) {
            return false;
        }

        return true;
    }

    private function now(int $addMinutes = 0): int
    {
        return time() + 60 * $addMinutes;
    }

    /**
     * @param string $username
     * @param string $clearPassword
     *
     * @return string|false the authentication token or false if username/password are not validated.
     *
     * @throws Exception
     */
    public function authenticate(string $username, string $clearPassword): string|false
    {
        if (!$this->active) {
            return '';
        }
        $user = $this->getUser($username);
        if (null === $user) {
            return false;
        }
        $passwordMethod = $this->passwordMethod;
        if (!$user->$passwordMethod($clearPassword)) {
            return false;
        }
        Server::writeSession($this->tokenKey, $token = self::generateToken());
        Server::writeSession($this->tokenKey . '.username', $username);
        Server::writeSession($this->tokenKey . '.expire', $this->now(intval($this->tokenExpire)));

        return $token;
    }

    public function deleteToken(): void
    {
        Server::writeSession($this->tokenKey, null);
        Server::writeSession($this->tokenKey . '.username', null);
        Server::writeSession($this->tokenKey . '.expire', null);
    }

    public function getUser(string $username): ?UserInterface
    {
        return $this->userCrud->readOneBy(new Criteria(['username' => $username]));
    }

    public function getCurrentUsername(): ?string
    {
        return Server::readSession($this->tokenKey . '.username');
    }

    public function getCurrentToken(): ?string
    {
        return Server::readSession($this->tokenKey);
    }

    public function getCurrentTokenExpire(): ?string
    {
        return Server::readSession($this->tokenKey . '.expire');
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return string 64 random hexadecimal characters
     *
     * @throws Exception
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

}
