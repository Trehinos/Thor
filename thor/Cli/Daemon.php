<?php

namespace Thor\Cli;

use DateInterval;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Thor\KernelInterface;

abstract class Daemon implements KernelInterface
{

    public function __construct(
        protected string $name,
        protected int $periodicityInSeconds,
        protected string $startHis = '000000',
        protected string $endHis = '235959'
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isNowRunnable(?DateTime $lastTime = null): bool
    {
        $now = new DateTime();
        $start = $this->getStartToday();
        $end = $this->getEndToday();

        if ($now < $start || $now > $end) {
            return false;
        }

        return null === $lastTime ||
            ($lastTime->add(new DateInterval("PT{$this->periodicityInSeconds}S"))) > $now;
    }

    final public function executeIfRunnable(DaemonState $state): void
    {
        if ($this->isNowRunnable($state->getLastRun())) {
            $state->load();
            if (!$state->isRunning()) {
                $state->setRunning(true);
                $state->write();
                $this->execute();
                $state->setRunning(false);
                $state->write();
            }
        }
    }

    final public function getStartToday(): DateTime
    {
        $now = new DateTime();
        return DateTime::createFromFormat('YmdHis', "{$now->format('Ymd')}{$this->startHis}");
    }

    final public function getEndToday(): DateTime
    {
        $now = new DateTime();
        return DateTime::createFromFormat('YmdHis', "{$now->format('Ymd')}{$this->endHis}");
    }

    final public static function instantiate(
        #[ArrayShape([
            'name' => 'string',
            'class' => 'string',
            'periodicity' => 'int',
            'start' => 'string',
            'end' => 'string'
        ])]
        array $info
    ): Daemon {
        return new ($info['class'])($info['name'], $info['periodicity'], $info['start'], $info['end']);
    }

}
