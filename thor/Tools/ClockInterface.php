<?php

namespace Thor\Tools;

use DateTimeImmutable;

/**
 * PSR-20 : ClockInterface.
 *
 * Used to provide a way to supply the current timestamp to the user.
 * This Interface exists in order to have a way to mock the current time with a specific specialization of this interface.
 */
interface ClockInterface
{

    public function now(): DateTimeImmutable;

}
