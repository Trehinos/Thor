<?php

namespace Thor\Cli;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Yaml\Yaml;
use Thor\FileSystem\Folder;
use Thor\Globals;

/**
 * Represents the current state of a Thor daemon.
 *
 * @package          Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class DaemonState
{

    public const DATE_FORMAT = 'YmdHis';

    private ?bool $isRunning = null;
    private ?DateTime $lastRun = null;
    private ?DateTime $nextRun = null;
    private ?string $error = null;
    private ?string $pid = null;

    /**
     * @param Daemon $daemon
     */
    public function __construct(private Daemon $daemon)
    {
    }

    /**
     * Gets the configuration filepath of the daemon bound with this state object.
     */
    #[Pure]
    public function getFileName(): string
    {
        return Globals::VAR_DIR . "daemons/{$this->daemon->getName()}.yml";
    }

    /**
     * Gets system PID of the daemon. Returns null if the daemon is not running.
     *
     * The PID is loaded from the state file at load(), this value can be outdated at the moment it is read.
     */
    public function getPid(): ?string
    {
        return $this->pid;
    }

    /**
     * Sets the system PID. It does not **change** the PID :
     * this method is used to update the daemon state.
     */
    public function setPid(?string $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * Loads the current state of the daemon from state file.
     */
    public function load(): void
    {
        if (!file_exists($this->getFileName())) {
            $this->write();
        }

        $data = Yaml::parseFile($this->getFileName());
        $this->isRunning = $data['isRunning'] ?? false;
        $lr = $data['lastRun'] ?? null;
        $nr = $data['nextRun'] ?? null;
        $this->error = $data['error'] ?? null;
        $this->pid = $data['pid'] ?? null;

        $this->lastRun = (($lr === null) ? null : DateTime::createFromFormat(self::DATE_FORMAT, $lr));
        $this->nextRun = (($nr === null) ? null : DateTime::createFromFormat(self::DATE_FORMAT, $nr));
    }

    /**
     * Writes the state of the daemon in the state file.
     */
    public function write(): void
    {
        Folder::createIfNotExists(dirname($this->getFileName()));
        file_put_contents(
            $this->getFileName(),
            Yaml::dump(
                [
                    'isRunning' => $this->isRunning(),
                    'lastRun'   => $this->getLastRun()?->format(self::DATE_FORMAT),
                    'nextRun'   => $this->getNextRun()?->format(self::DATE_FORMAT),
                    'error'     => $this->error,
                    'pid'       => $this->pid,
                ]
            )
        );
    }

    /**
     * Sets the error field. Set to null to disable error.
     */
    public function error(?string $errorMessage): void
    {
        $this->error = $errorMessage;
    }

    /**
     * Gets the last error of this daemon.
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Sets the running state of the daemon.
     *
     * If set to true, the lastRun field will also be set.
     */
    public function setRunning(bool $running): void
    {
        $now = new DateTimeImmutable();
        if ($running) {
            $lastRun = $now;
            $this->lastRun = DateTime::createFromFormat(
                self::DATE_FORMAT,
                $lastRun->format('YmdHi') . '00'
            );
        }
        if ($this->lastRun !== null) {
            $nextRun = clone $this->lastRun;
            $delta = $now->format('YmdHi') % $this->daemon->getPeriodicity();
            $nextRun->sub(new DateInterval("PT{$delta}M"));
            $nextRun->add(new DateInterval("PT{$this->daemon->getPeriodicity()}M"));
            if ($this->daemon->getStartToday() < $this->daemon->getEndToday()) {
                // TODO
            }

            $this->nextRun = DateTime::createFromFormat(
                self::DATE_FORMAT,
                $nextRun->format('YmdHi') . '00'
            );
            $this->isRunning = $running;
        }
    }

    /**
     * Returns true if the daemon is now running.
     */
    public function isRunning(): bool
    {
        return $this->isRunning ?? false;
    }

    /**
     * @return DateTime|null
     */
    public function getNextRun(): ?DateTime
    {
        return $this->nextRun;
    }

    /**
     * @return DateTime|null
     */
    public function getLastRun(): ?DateTime
    {
        return $this->lastRun;
    }

    /**
     * Sets the lastRun field.
     */
    public function setLastRun(?DateTime $lastRun): void
    {
        $this->lastRun = $lastRun;
    }

    /**
     * Sets the lastRun field.
     */
    public function setNextRun(?DateTime $nextRun): void
    {
        $this->nextRun = $nextRun;
    }

}
