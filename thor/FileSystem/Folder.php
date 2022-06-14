<?php

namespace Thor\FileSystem;

/**
 * Static utilities to perform operations on folders.
 *
 * @package          Thor/FileSystem
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Folder
{

    private function __construct()
    {
    }

    /**
     * Removes all elements in the corresponding $path and returns an array of all files effectively deleted.
     *
     * @param bool $removeFirst ignored if $removeDirs is false
     */
    public static function removeTree(
        string $path,
        string|false $mask = false,
        bool $removeDirs = true,
        bool $removeFirst = true,
        ?callable $removeCondition = null
    ): array {
        $files = self::fileList($path);

        $ret = [];
        foreach ($files as $file) {
            if (FileSystem::isDir("$path/$file")) {
                $ret = array_merge(
                    $ret,
                    self::removeTree("$path/$file", $mask, $removeDirs, $removeDirs, $removeCondition)
                );
                continue;
            }
            if ($mask !== false && preg_match("#^$mask$#", $file) === 0) {
                continue;
            }
            if ($removeCondition !== null && $removeCondition("$path/$file") !== true) {
                continue;
            }
            $result = unlink("$path/$file");
            if ($result) {
                $ret[] = "$path/$file";
            }
        }

        if ($removeDirs && $removeFirst) {
            $result = false;
            if ($removeCondition === null || $removeCondition("$path") === true) {
                $result = self::removeIfEmpty("$path");
            }
            if ($result) {
                $ret[] = "$path";
            }
        }
        return $ret;
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    private static function isSpecial(string $file): bool
    {
        return in_array($file, ['.', '..']);
    }

    /**
     * Copies the specified path to $dest.
     */
    public static function copyTree(string $path, string $dest): void
    {
        $files = self::fileList($path);
        foreach ($files as $file) {
            if (FileSystem::isDir("$path/$file")) {
                Folder::createIfNotExists("$dest/$file");
                self::copyTree("$path/$file", "$dest/$file");
                continue;
            }
            copy("$path/$file", "$dest/$file");
        }
    }

    /**
     * Performs an operation on each file in the $path folder. Recursively.
     */
    public static function mapFiles(string $path, callable $mappedFunction, mixed ...$functionArguments): void
    {
        $files = self::fileList($path);

        foreach ($files as $file) {
            if (FileSystem::isDir("$path/$file")) {
                self::mapFiles("$path/$file", $mappedFunction, ...$functionArguments);
                continue;
            }
            $mappedFunction("$path/$file", ...$functionArguments);
        }
    }

    /**
     * Creates (a) folder(s) recursively if the path does not exist.
     */
    public static function createIfNotExists(
        string $name,
        int $permissions = FileSystem::ALL_ALL,
        ?string $user = null,
        ?string $group = null,
    ): void {
        if (!FileSystem::exists($name)) {
            mkdir($name, recursive: true);
            chmod($name, $permissions);
            if (null !== $user) {
                FileSystem::chown($name, $user, $group);
            }
        }
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public static function fileList(string $path): array
    {
        $files = scandir($path);
        return array_filter($files, fn(string $filename) => !self::isSpecial($filename));
    }

    /**
     * Removes a folder if it is empty.
     *
     * Returns true if the folder is effectively deleted.
     */
    public static function removeIfEmpty(string $name): bool
    {
        if (FileSystem::exists($name) && FileSystem::isDir($name) && empty(self::fileList($name))) {
            return rmdir($name);
        }

        return false;
    }

}
