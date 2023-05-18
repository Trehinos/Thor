<?php

namespace Thor\Cli\Daemon;

use DateTime;
use Throwable;
use Thor\Process\Executable;

interface DaemonInterface extends Executable
{

    /**
     * Returns the daemon name (MUST match the filename)
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get daemon periodicity.
     *
     * @return int in minutes
     */
    public function getPeriodicity(): int;

    /**
     * Returns true if it is active.
     *
     * A daemon is active if it is enabled and during it's activation period.
     *
     * @return bool
     */
    public function isActive(): bool;

    public function isNowRunnable(?DateTime $nextTime = null): bool;

    /**
     * Returns true if the daemon is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Gets the start of the active period of the daemon today.
     *
     * @return DateTime
     */
    public function getStartToday(): DateTime;

    /**
     * Gets the end of the active period of the daemon today.
     *
     * @return DateTime
     */
    public function getEndToday(): DateTime;

    /**
     * Execute the daemon if it is runnable.
     *
     * In case of a Throwable is caught, it writes its message in the state file
     * and throws it again.
     *
     * @param DaemonState $state
     * @param bool        $force
     *
     * @throws Throwable
     */
    public function executeIfRunnable(DaemonState $state, bool $force = false): void;
}
