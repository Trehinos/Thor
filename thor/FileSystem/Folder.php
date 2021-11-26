<?php

/**
 * @package          Thor/FileSystem
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\FileSystem;

final class Folder
{

    /**
     * @param string       $path
     * @param string|false $mask
     * @param bool         $removeDirs
     * @param bool         $removeFirst ignored if $removeDirs is false
     *
     * @return array
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

    public static function isSpecial(string $file): bool
    {
        return in_array($file, ['.', '..']);
    }

    public static function copyTree(string $path, string $dest): void
    {
        self::mapFiles(
            $path,
            function (string $fileToCopy, string $destPath) {
                copy($fileToCopy, "$destPath/" . basename($fileToCopy));
            },
            $dest
        );
    }

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

    public static function createIfNotExists(string $name): void
    {
        if (!file_exists($name)) {
            mkdir($name, recursive: true);
        }
    }

}
