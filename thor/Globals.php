<?php

namespace Thor;

use Thor\Tools\Strings;

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
     * The folder where lies web files.
     */
    const THOR_DIR = self::CODE_DIR . 'thor/';

    /**
     * The folder where lies web files.
     */
    const WEB_DIR = self::CODE_DIR . 'web/';

    /**
     * The default folder where lies vendor libraries.
     *
     * This files can be deleted without implication on project's features.
     */
    const VENDORS_DIR = self::CODE_DIR . 'vendors/';

    /**
     * The folder where lies var files.
     *
     * This files can be deleted without implication on Thor's or project's features.
     */
    const VAR_DIR = self::CODE_DIR . 'var/';

    /**
     * Substitutes `{CONST_NAME}_DIR` in **$pathString** with corresponding `Globals::CONST_NAME_DIR`.
     *
     * ### Example :
     * ```php
     * Globals::path('{VAR}exports/export.xlsx'); // === Globals::VAR_DIR . 'exports/export.xlsx'
     * ```
     *
     * @param string $pathString
     *
     * @return string
     *
     * @example Globals::path('{VAR}foo/bar.baz') === Globals::VAR_DIR . 'foo/bar.baz'
     *
     * @see     Strings::interpolate()
     *
     */
    public static function path(string $pathString): string
    {
        return Strings::interpolate($pathString, [
            'CODE'      => realpath(self::CODE_DIR),
            'BIN'       => realpath(self::BIN_DIR),
            'RESOURCES' => realpath(self::RESOURCES_DIR),
            'CONFIG'    => realpath(self::CONFIG_DIR),
            'STATIC'    => realpath(self::STATIC_DIR),
            'WEB'       => realpath(self::WEB_DIR),
            'THOR'      => realpath(self::THOR_DIR),
            'VENDORS'   => realpath(self::VENDORS_DIR),
            'VAR'       => realpath(self::VAR_DIR),
        ]);
    }

}
