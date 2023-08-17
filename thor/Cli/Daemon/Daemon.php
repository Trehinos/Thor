<?php

namespace Thor\Cli\Daemon;

use DateTime;
use Throwable;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Describes a Thor Daemon defined in `thor/res/static/daemons/*`.
 *
 * The daemon $name MUST match the filename.
 *
 * @package          Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
abstract class Daemon implements DaemonInterface
{

    /**
     * Construct a new Daemon.
     *
     * @param string $name
     * @param int    $periodicityInMinutes
     * @param string $startHi
     * @param string $endHi
     * @param bool   $enabled
     */
    public function __construct(
        protected string $name,
        protected int $periodicityInMinutes,
        protected string $startHi = '000000',
        protected string $endHi = '235959',
        protected bool $enabled = false
    ) {
    }

    /**
     * Instantiate a Daemon from an array.
     *
     * The array SHOULD be a Yaml::parseFile() result of a daemon YML file.
     *
     * @param array $info
     *
     * @return Daemon
     */
    final public static function instantiate(
        #[ArrayShape([
            'name'        => 'string',
            'class'       => 'string',
            'periodicity' => 'int',
            'start'       => 'string',
            'end'         => 'string',
            'enabled'     => 'bool',
        ])]
        array $info
    ): Daemon {
        return new ($info['class'])(
            $info['name'],
            $info['periodicity'],
            $info['start'],
            $info['end'],
            $info['enabled']
        );
    }

    /**
     * Returns the daemon name (MUST match the filename)
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get daemon periodicity.
     *
     * @return int in minutes
     */
    public function getPeriodicity(): int
    {
        return $this->periodicityInMinutes;
    }

    /**
     * Returns true if it is active.
     *
     * A daemon is active if it is enabled and during it's activation period.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isNowRunnable();
    }

    /**
     * Returns true if it is active and lastTime + periodicity > now.
     *
     * @param DateTime|null $nextTime
     *
     * @return bool
     */
    public function isNowRunnable(?DateTime $nextTime = null): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $now = new DateTime();
        $start = $this->getStartToday();
        $end = $this->getEndToday();

        if ($now < $start || $now > $end) {
            return false;
        }

        if (null === $nextTime) {
            return true;
        }

        return $nextTime <= $now;
    }

    /**
     * Returns true if the daemon is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Gets the start of the active period of the daemon today.
     *
     * @return DateTime
     */
    final public function getStartToday(): DateTime
    {
        $now = new DateTime();
        return DateTime::createFromFormat('YmdHis', "{$now->format('Ymd')}{$this->startHi}00");
    }

    /**
     * Gets the end of the active period of the daemon today.
     *
     * @return DateTime
     */
    final public function getEndToday(): DateTime
    {
        $now = new DateTime();
        return DateTime::createFromFormat('YmdHis', "{$now->format('Ymd')}{$this->endHi}59");
    }

    /**
     * Execute the daemon if it is runnable.
     *
     * In case of a Throwable is caught, it writes it in the state file
     * and throws it again.
     *
     * @param DaemonState $state
     * @param bool        $force
     *
     * @throws Throwable
     */
    final public function executeIfRunnable(DaemonState $state, bool $force = false): void
    {
        if (!$state->isRunning() && ($force || $this->isNowRunnable($state->getNextRun()))) {
            try {
                $state->setRunning(true);
                $state->setPid(getmypid());
                $state->error(null);
                $state->write();
                $this->execute();
            } catch (Throwable $e) {
                $state->error($e->getMessage());
                $state->setPid(null);
                $state->setRunning(false);
                $state->write();
                throw $e;
            }
            $state->setPid(null);
            $state->setRunning(false);
            $state->write();
        }
    }

}
