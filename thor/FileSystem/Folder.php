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
     * Removes all elements in the corresponding $path.
     *
     * @param bool $removeFirst ignored if $removeDirs is false
     */
    public static function removeTree(
        string $path,
        string|false $mask = false,
        bool $removeDirs = true,
        bool $removeFirst = true
    ): array {
        $files = scandir($path);

        $ret = [];
        foreach ($files as $file) {
            if (self::isSpecial($file)) {
                continue;
            }
            if (is_dir("$path/$file")) {
                $ret = array_merge($ret, self::removeTree("$path/$file", $mask, $removeDirs, $removeDirs));
                continue;
            }
            if ($mask !== false && preg_match("#^$mask$#", $file) === 0) {
                continue;
            }
            $result = unlink("$path/$file");
            if ($result) {
                $ret[] = "$path/$file";
            }
        }

        if ($removeDirs && $removeFirst) {
            $result = rmdir("$path");
            if ($result) {
                $ret[] = "$path";
            }
        }
        return $ret;
    }

    private static function isSpecial(string $file): bool
    {
        return in_array($file, ['.', '..']);
    }

    /**
     * Copies the specified path to $dest.
     */
    public static function copyTree(string $path, string $dest): void
    {
        $files = scandir($path);
        foreach ($files as $file) {
            if (self::isSpecial($file)) {
                continue;
            }
            if (is_dir("$path/$file")) {
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
        $files = scandir($path);

        foreach ($files as $file) {
            if (self::isSpecial($file)) {
                continue;
            }
            if (is_dir("$path/$file")) {
                self::mapFiles("$path/$file", $mappedFunction, ...$functionArguments);
                continue;
            }
            $mappedFunction("$path/$file", ...$functionArguments);
        }
    }

    /**
     * Creates (a) folder(s) recursively if the path does not exist.
     */
    public static function createIfNotExists(string $name, int $permissions = File::ALL_ALL, ?string $user = null): void
    {
        if (!File::exists($name)) {
            mkdir($name, recursive: true);
            chmod($name, $permissions);
            if (null !== $user) {
                File::chown($name, $user);
            }
        }
    }

}
