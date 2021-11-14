<?php

/**
 * @package Trehinos/Thor
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor;

final class Globals
{
    const CODE_DIR = __DIR__ . '/../';
    const BIN_DIR = self::CODE_DIR . 'bin/';
    const RESOURCES_DIR = self::CODE_DIR . 'app/res/';
    const CONFIG_DIR = self::RESOURCES_DIR . 'config/';
    const STATIC_DIR = self::RESOURCES_DIR . 'static/';
    const VENDORS_DIR = self::CODE_DIR . 'vendors/';
    const VAR_DIR = self::CODE_DIR . 'var/';
}
