<?php

/**
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Security;

final class PasswordHasher
{

    public function __construct(
        private string $algo = PASSWORD_ARGON2ID,
        private array $options = []
    ) {
    }

    public static function hashPassword(
        string $password,
        string $algo = PASSWORD_ARGON2ID,
        array $options = []
    ) {
        return (new self($algo, $options))->hash($password);
    }

    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function hash(string $password): string
    {
        return password_hash($password, $this->algo, $this->options);
    }

}
