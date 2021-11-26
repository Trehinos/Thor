<?php

/**
 * @package Thor/FileSystem
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\FileSystem;

final class File
{

    public static function deleteIfExists(string $name): void
    {
        if (file_exists($name)) {
            unlink($name);
        }
    }

}
