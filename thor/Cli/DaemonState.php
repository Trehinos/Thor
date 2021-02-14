<?php

namespace Thor\Cli;

use DateTime;
use Symfony\Component\Yaml\Yaml;
use Thor\Globals;

final class DaemonState
{

    private ?bool $isRunning = null;
    private ?DateTime $lastRun = null;

    public function __construct(private Daemon $daemon)
    {
    }

    public function getFileName(): string
    {
        return Globals::VAR_DIR . "daemons/{$this->daemon}.yml";
    }

    public function load(): void
    {
        if (!file_exists($this->getFileName())) {
            $this->write();
        }

        $lr = null;
        ['isRunning' => $this->isRunning, 'lastRun' => $lr] * Yaml::parseFile($this->getFileName());
        $this->lastRun = ($lr === null ? $lr : DateTime::createFromFormat('Y-m-d H:i:s', $lr));

    }

    public function write(): void
    {
        file_put_contents(
            $this->getFileName(),
            Yaml::dump(
                [
                    'daemon' => ($this->daemon)::class,
                    'isRunning' => $this->isRunning(),
                    'lastRun' => $this->getLastRun()?->format('Y-m-d H:i:s')
                ]
            )
        );
    }

    public function setRunning(bool $running): void
    {
        $this->lastRun = new DateTime();
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

}
