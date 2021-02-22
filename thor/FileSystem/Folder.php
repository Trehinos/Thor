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

}
