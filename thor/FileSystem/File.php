<?php
namespace Thor\FileSystem;

/**
 * Static utilities to control filesystem.
 *
 * @package Thor/FileSystem
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

final class File
{

    /**
     * Delete a file if it exists. Do nothing otherwise (no error thrown).
     *
     * @param string $name
     */
    public static function deleteIfExists(string $name): void
    {
        if (file_exists($name)) {
            unlink($name);
        }
    }

}
