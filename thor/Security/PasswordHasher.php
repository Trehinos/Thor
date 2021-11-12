<?php

/**
 * @package Trehinos/Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Security;

final class PasswordHasher
{

    private string $algo;
    private array $options;

    public function __construct(
        string $algo = PASSWORD_ARGON2ID,
        array $options = []
    ) {
        $this->algo = $algo;
        $this->options = $options;
    }

    public function hash(string $password): string
    {
        return password_hash($password, $this->algo, $this->options);
    }

    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

}
