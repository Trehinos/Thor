<?php

namespace Thor\FileSystem;

/**
 * Static utilities to control filesystem.
 *
 * @package          Thor/FileSystem
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class FileSystem
{

    private function __construct()
    {
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isDir(string $path): bool
    {
        return self::exists($path) && is_dir($path);
    }

    /**
     * Deletes a file if it exists. Do nothing otherwise.
     * This method can NOT delete folders.
     *
     * @see Folders::removeTree()
     * @see Folders::removeIfEmpty()
     */
    public static function deleteIfExists(string $path): bool
    {
        if (self::exists($path) && !self::isDir($path)) {
            return unlink($path);
        }
        return false;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public static function readable(string $filename): bool
    {
        return self::exists($filename) && is_readable($filename);
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public static function writable(string $filename): bool
    {
        return self::exists($filename) && is_writable($filename);
    }

    /**
     * Returns if a file exists.
     */
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Appends in a file if it exists. Do nothing otherwise (no error thrown).
     */
    public static function appendIfExists(string $path, string $data): void
    {
        if (self::exists($path)) {
            self::write($path, $data, true);
        }
    }

    /**
     * Writes in a file.
     */
    public static function write(string $path, string $data, bool $append = false): void
    {
        file_put_contents($path, $data, $append ? FILE_APPEND : 0);
    }

    /**
     * Sets owner (and group) of a file.
     */
    public static function chown(string $path, string $user, ?string $group = null): bool
    {
        $result = chown($path, $user);
        if ($group !== null) {
            $result = $result && chgrp($path, $group);
        }

        return $result;
    }

    /**
     * Sets a file's permissions.
     */
    public static function chmod(string $path, int $permissions): bool
    {
        return chmod($path, $permissions);
    }

    /**
     * Reads from a file.
     * Returns null if the file doesn't exist.
     */
    public static function read(string $path): ?string
    {
        if (!self::exists($path)) {
            return null;
        }
        return file_get_contents($path);
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

}
