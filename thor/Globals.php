<?php

namespace Thor;

/**
 * Defines some of Thor's common paths.
 *
 * @package          Thor
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Globals
{
    /**
     * The project's folder.
     */
    const CODE_DIR = __DIR__ . '/../';

    /**
     * The folder where lies binary files and Cli scripts.
     */
    const BIN_DIR = self::CODE_DIR . 'bin/';

    /**
     * The folder of resource files.
     */
    const RESOURCES_DIR = self::CODE_DIR . 'app/res/';

    /**
     * The folder where lies the Thor's configuration.
     */
    const CONFIG_DIR = self::RESOURCES_DIR . 'config/';

    /**
     * This folder contains static data of Thor.
     */
    const STATIC_DIR = self::RESOURCES_DIR . 'static/';

    /**
     * The default folder where lies vendor libraries.
     */
    const VENDORS_DIR = self::CODE_DIR . 'vendors/';

    /**
     * The folder where lies var files.
     *
     * This files can be deleted without implication on Thor's or project's features.
     */
    const VAR_DIR = self::CODE_DIR . 'var/';
}
