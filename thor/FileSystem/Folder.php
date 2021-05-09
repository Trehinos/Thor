<?php

/**
 * @package Trehinos/Thor/FileSystem
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\FileSystem;

final class Folder
{

    public static function createIfNotExists(string $name): void
    {
        if (!file_exists($name)) {
            mkdir($name, recursive: true);
        }
    }

    public static function removeTree(string $path, string|false $mask = false, bool $removeDirs = true, bool $removeFirst = true): array
    {
        $files = scandir($path);

        $ret = [];
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            if (is_dir("$path/$file")) {
                $ret = array_merge($ret, self::removeTree("$path/$file", $mask, $removeDirs));
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

}
