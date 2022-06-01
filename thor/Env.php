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
    /**
     * Developpement env. Everything is logged, all errors are dumped.
     */
    case DEV = 'DEV';

    /**
     * Debug env. Errors and debug info ares logged, all errors are dumped.
     */
    case DEBUG = 'DEBUG';


    /**
     * Production env. Errors are logged, errors are silenced.
     */
    case PROD = 'PROD';
}
