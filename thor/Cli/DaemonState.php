<?php

/**
 * @package Trehinos/Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Cli;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Yaml\Yaml;
use Thor\FileSystem\Folder;
use Thor\Globals;

final class DaemonState
{

    public const DATE_FORMAT = 'YmdHis';

    private ?bool $isRunning = null;
    private ?DateTime $lastRun = null;
    private ?string $error = null;
    private ?string $pid = null;

    public function __construct(private Daemon $daemon)
    {
    }

    #[Pure]
    public function getFileName(): string
    {
        return Globals::VAR_DIR . "daemons/{$this->daemon->getName()}.yml";
    }

    /**
     * @return string|null
     */
    public function getPid(): ?string
    {
        return $this->pid;
    }

    /**
     * @param string|null $pid
     */
    public function setPid(?string $pid): void
    {
        $this->pid = $pid;
    }


    public function load(): void
    {
        if (!file_exists($this->getFileName())) {
            $this->write();
        }

        $data = Yaml::parseFile($this->getFileName());
        $this->isRunning = $data['isRunning'] ?? false;
        $lr = $data['lastRun'] ?? null;
        $this->error = $data['error'] ?? null;
        $this->pid = $data['pid'] ?? null;

        $this->lastRun = (($lr === null) ? null : DateTime::createFromFormat(self::DATE_FORMAT, $lr));
    }

    public function write(): void
    {
        Folder::createIfNotExists(dirname($this->getFileName()));
        file_put_contents(
            $this->getFileName(),
            Yaml::dump(
                [
                    'isRunning' => $this->isRunning(),
                    'lastRun' => $this->getLastRun()?->format(self::DATE_FORMAT),
                    'error' => $this->error,
                    'pid' => $this->pid
                ]
            )
        );
    }

    public function error(?string $errorMessage): void
    {
        $this->error = $errorMessage;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setRunning(bool $running): void
    {
        if ($running) {
            $lr = new DateTime();
            $diff = intval(($lr->format('U') - $this->daemon->getStartToday()->format('U')) / 60);
            $delta = $diff % $this->daemon->getPeriodicity();
            $diff = $diff - $delta;
            $lastRun = $this->daemon->getStartToday();
            $lastRun->add(new \DateInterval("PT{$diff}M"));
            $this->lastRun = DateTime::createFromFormat(
                self::DATE_FORMAT,
                $lastRun->format('YmdHi') . '00'
            );
        }
        $this->isRunning = $running;
    }

    public function isRunning(): bool
    {
        return $this->isRunning ?? false;
    }

    public function getLastRun(): ?DateTime
    {
        return $this->lastRun;
    }

    public function setLastRun(?DateTime $lastRun): void
    {
        $this->lastRun = $lastRun;
    }

}
