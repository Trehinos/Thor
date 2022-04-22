<?php

namespace Thor\FileSystem;

use JetBrains\PhpStorm\Pure;

/**
 * Static utilities to control filesystem.
 *
 * @package          Thor/FileSystem
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class FileSystem
{

    public const SET_UID = 04000;
    public const SET_GID = 02000;
    public const STICKY_BIT = 01000;

    /**
     *
     * 0140000 => 's', // Socket
     * 0120000 => 'l', // Symlink
     * 0100000 => '-', // Regular
     * 0060000 => 'b', // Special block
     * 0040000 => 'd', // Directory
     * 0020000 => 'c', // Special character
     * 0010000 => 'p', // Pipe
     * default => 'u', // Unknown
     */

    public const SOCKET = 0140000;
    public const SYMLINK = 0120000;
    public const REGULAR = 0100000;
    public const SPECIAL_BLOCK = 0060000;
    public const DIRECTORY = 0040000;
    public const SPECIAL_CHAR = 0020000;
    public const PIPE = 0010000;
    public const UNKNOWN = 0770000;

    public const OWNER_READ = 0400;
    public const OWNER_WRITE = 0200;
    public const OWNER_EXEC = 0100;
    public const GROUP_READ = 0040;
    public const GROUP_WRITE = 0020;
    public const GROUP_EXEC = 0010;
    public const OTHER_READ = 0004;
    public const OTHER_WRITE = 0002;
    public const OTHER_EXEC = 0001;

    public const ALL_READ = self::OWNER_READ | self::GROUP_READ | self::OTHER_READ;
    public const ALL_WRITE = self::OWNER_WRITE | self::GROUP_WRITE | self::OTHER_WRITE;
    public const ALL_EXEC = self::OWNER_EXEC | self::GROUP_EXEC | self::OTHER_EXEC;

    public const OWNER_READWRITE = self::OWNER_READ | self::OWNER_WRITE;
    public const OWNER_READEXEC = self::OWNER_READ | self::OWNER_EXEC;
    public const OWNER_ALL = self::OWNER_READWRITE | self::OWNER_EXEC;

    public const GROUP_READWRITE = self::GROUP_READ | self::GROUP_WRITE;
    public const GROUP_READEXEC = self::GROUP_READ | self::GROUP_EXEC;
    public const GROUP_ALL = self::GROUP_READWRITE | self::GROUP_EXEC;

    public const OTHER_READWRITE = self::OTHER_READ | self::OTHER_WRITE;
    public const OTHER_READEXEC = self::OTHER_READ | self::OTHER_EXEC;
    public const OTHER_ALL = self::OTHER_READWRITE | self::OTHER_EXEC;

    public const ALL_ALL = self::ALL_READ | self::ALL_WRITE | self::ALL_EXEC;

    private function __construct()
    {
    }

    public static function isDir(string $name): bool
    {
        return self::exists($name) && is_dir($name);
    }

    /**
     * Deletes a file if it exists. Do nothing otherwise (no error thrown).
     *
     * @param string $name
     */
    public static function deleteIfExists(string $name): void
    {
        if (self::exists($name)) {
            unlink($name);
        }
    }

    /**
     * Returns if a file exists.
     */
    public static function exists(string $name): bool
    {
        return file_exists($name);
    }

    /**
     * Appends in a file if it exists. Do nothing otherwise (no error thrown).
     */
    public static function appendIfExists(string $name, string $data): void
    {
        if (self::exists($name)) {
            self::write($name, $data, true);
        }
    }

    /**
     * Writes in a file.
     */
    public static function write(string $name, string $data, bool $append = false): void
    {
        file_put_contents($name, $data, $append ? FILE_APPEND : 0);
    }

    /**
     * Sets owner (and group) of a file.
     */
    public static function chown(string $name, string $user, ?string $group = null): bool
    {
        $result = chown($name, $user);
        if ($group !== null) {
            $result = $result && chgrp($name, $group);
        }

        return $result;
    }

    /**
     * Sets a file's permissions.
     */
    public static function chmod(string $name, int $permissions): bool
    {
        return chmod($name, $permissions);
    }

    /**
     * Reads from a file.
     * Returns null if the file doesn't exist.
     */
    public static function read(string $name): ?string
    {
        if (!FileSystem::exists($name)) {
            return null;
        }
        return file_get_contents($name);
    }

    /**
     * Returns true if the file as the exact specified permissions.
     */
    #[Pure]
    public static function hasPermission(string $name, int $permission): ?bool
    {
        $perm = self::permissions($name);
        if (!is_int($perm)) {
            return null;
        }

        return (0777 & $perm) === $permission;
    }


    /**
     * Returns the last part of a (*.{ext}) string.
     * Returns null if no '.' is found.
     */
    public static function getExtension(string $filename): ?string
    {
        if (!str_contains($filename, '.')) {
            return null;
        }
        $chunks = explode('.', $filename);

        return $chunks[count($chunks) - 1];
    }

    /**
     * Gets the file's permissions. If the file is not found, returns null. Returns false if an error occurs.
     */
    public static function permissions(string $name): int|false|null
    {
        if (!FileSystem::exists($name)) {
            return null;
        }
        return fileperms($name);
    }

    /**
     * Returns a permissions string like in a `ls -la` command.
     *
     * @example -rwxrw-r--
     *
     * @link    https://www.php.net/manual/fr/function.fileperms.php#example-2167
     */
    public static function permissionsString(string $name): ?string
    {
        $perms = FileSystem::permissions($name);

        if (!is_int($perms)) {
            return null;
        }

        return match ($perms & 0770000) {
                self::SOCKET        => 's', // Socket
                self::SYMLINK       => 'l', // Symlink
                self::REGULAR       => '-', // Regular
                self::SPECIAL_BLOCK => 'b', // Special block
                self::DIRECTORY     => 'd', // Directory
                self::SPECIAL_CHAR  => 'c', // Special character
                self::PIPE          => 'p', // Pipe
                default             => 'u', // Unknown
            } . self::permissionsStringFor(
                $perms,
                self::OWNER_READ,
                self::OWNER_WRITE,
                self::OWNER_EXEC,
                self::SET_UID,
                's',
                'S'
            ) . self::permissionsStringFor(
                $perms,
                self::GROUP_READ,
                self::GROUP_WRITE,
                self::GROUP_EXEC,
                self::SET_GID,
                's',
                'S'
            ) . self::permissionsStringFor(
                $perms,
                self::OTHER_READ,
                self::OTHER_WRITE,
                self::OTHER_EXEC,
                self::STICKY_BIT,
                't',
                'T'
            );
    }

    private static function permissionsStringFor(
        int $permission,
        int $read,
        int $write,
        int $exec,
        int $special,
        string $special_exec,
        string $special_notExec
    ): string {
        return (($permission & $read) ? 'r' : '-') .
            (($permission & $write) ? 'w' : '-') .
            (($permission & $exec)
                ? (($permission & $special) ? $special_exec : 'x')
                : (($permission & $special) ? $special_notExec : '-'));
    }

}
