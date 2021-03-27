<?php

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
