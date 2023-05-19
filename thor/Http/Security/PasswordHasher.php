<?php
namespace Thor\Http\Security;

/**
 * A utility class to generate and test hashed passwords.
 *
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class PasswordHasher
{

    /**
     * @param string $algo
     * @param array  $options
     */
    public function __construct(
        private string $algo = PASSWORD_ARGON2ID,
        private array $options = []
    ) {
    }

    /**
     * Hash a password and returns the hash.
     *
     * @param string $password
     * @param string $algo
     * @param array  $options
     *
     * @return string
     */
    public static function hashPassword(
        string $password,
        string $algo = PASSWORD_ARGON2ID,
        array $options = []
    ): string {
        return (new self($algo, $options))->hash($password);
    }

    /**
     * Verify if a password correspond a hash.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash a password and returns it.
     *
     * @param string $password
     *
     * @return string
     */
    public function hash(string $password): string
    {
        return password_hash($password, $this->algo, $this->options);
    }

}
