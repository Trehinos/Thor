<?php

namespace Thor\Security;

final class PasswordHash
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

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

}
