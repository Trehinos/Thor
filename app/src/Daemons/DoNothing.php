<?php

/**
 * @package Trehinos/Thor/Examples
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace App\Daemons;

use Thor\Cli\Daemon;

final class DoNothing extends Daemon
{

    public function execute(): void
    {
    }

}
