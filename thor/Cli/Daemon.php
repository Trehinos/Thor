<?php

namespace Thor\Cli;

use DateInterval;
use DateTime;

abstract class Daemon extends Command
{

    public function __construct(
        protected int $periodicityInSeconds,
        protected string $startHis = '000000',
        protected string $endHis = '235959'
    ) {
        parent::__construct("", []);
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

    public function executeIfRunnable(): void
    {
        if ($this->isNowRunnable()) {
            $this->execute();
        }
    }

    public function getStartToday(): DateTime
    {
        $now = new DateTime();
        return DateTime::createFromFormat('YmdHis', "{$now->format('Ymd')}{$this->startHis}");
    }

    public function getEndToday(): DateTime
    {
        $now = new DateTime();
        return DateTime::createFromFormat('YmdHis', "{$now->format('Ymd')}{$this->endHis}");
    }

}
