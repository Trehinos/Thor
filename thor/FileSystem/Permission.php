<?php

namespace Thor\FileSystem;

class Permission
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
     * @example drwxr-x--x
     *
     * @link    https://www.php.net/manual/fr/function.fileperms.php#example-2167
     */
    public static function permissionsString(string $name): ?string
    {
        $perms = self::permissions($name);

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

    /**
     * @param int    $permission
     * @param int    $read
     * @param int    $write
     * @param int    $exec
     * @param int    $special
     * @param string $special_exec
     * @param string $special_notExec
     *
     * @return string
     */
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