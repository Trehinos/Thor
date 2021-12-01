<?php

namespace Thor;

/**
 * Defines all valid Thor environments.
 *
 * @package          Thor
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
enum Env: string
{
    case DEV = 'DEV';
    case DEBUG = 'DEBUG';
    case PROD = 'PROD';
}
