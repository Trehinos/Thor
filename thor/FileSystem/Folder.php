<?php

namespace Thor\FileSystem;

final class Folder
{

    public static function createIfNotExists(string $name): void
    {
        if (!file_exists($name)) {
            mkdir($name, recursive: true);
        }
    }

    public static function removeTree(string $path, string|false $mask = false, bool $removeFirst = true): bool
    {
        $files = scandir($path);

        $ret = true;
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            if (is_dir("$path/$file")) {
                $ret = $ret && self::removeTree("$path/$file", $mask, $removeFirst);
                continue;
            }
            if ($mask !== false && preg_match("#^$mask$#", $file) === 0) {
                continue;
            }
            $ret = $ret && unlink("$path/$file");
            echo "$path/$file deleted\n";
        }

        if ($removeFirst) {
            $ret = $ret && rmdir("$path");
            echo "$path deleted\n";
        }
        return $ret;
    }

}
