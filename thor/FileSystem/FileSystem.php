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
     * @param string $name
     *
     * @return bool
     */
    public static function isDir(string $name): bool
    {
        return self::exists($name) && is_dir($name);
    }

    /**
     * Deletes a file if it exists. Do nothing otherwise.
     */
    public static function deleteIfExists(string $name): bool
    {
        if (self::exists($name) && !self::isDir($name)) {
            return unlink($name);
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
