<?php

namespace Thor\Cli;

use DateTime;
use Symfony\Component\Yaml\Yaml;
use Thor\Debug\Logger;
use Thor\Globals;

final class DaemonState
{

    public const DATE_FORMAT = 'YmdHis';

    private ?bool $isRunning = null;
    private ?DateTime $lastRun = null;

    public function __construct(private Daemon $daemon)
    {
    }

    public function getFileName(): string
    {
        return Globals::VAR_DIR . "daemons/{$this->daemon->getName()}.yml";
    }

    public function load(): void
    {
        if (!file_exists($this->getFileName())) {
            $this->write();
        }

        $lr = null;
        ['isRunning' => $this->isRunning, 'lastRun' => $lr] = Yaml::parseFile($this->getFileName());
        $this->lastRun = (($lr === null) ? null : DateTime::createFromFormat(self::DATE_FORMAT, $lr));
    }

    public function write(): void
    {
        file_put_contents(
            $this->getFileName(),
            Yaml::dump(
                [
                    'isRunning' => $this->isRunning(),
                    'lastRun' => $this->getLastRun()?->format(self::DATE_FORMAT)
                ]
            )
        );
    }

    public function setRunning(bool $running): void
    {
        if ($running) {
            $lr = new DateTime();
            $diff = intval(($lr->format('U') - $this->daemon->getStartToday()->format('U')) / 60);
            Logger::write($diff);
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

}
