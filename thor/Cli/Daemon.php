<?php

namespace Thor\Cli;

use DateInterval;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Thor\Debug\Logger;
use Thor\KernelInterface;
use Throwable;

abstract class Daemon implements KernelInterface
{

    public function __construct(
        protected string $name,
        protected int $periodicityInMinutes,
        protected string $startHis = '000000',
        protected string $endHis = '235959',
        protected bool $enabled = false
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int in minutes
     */
    public function getPeriodicity(): int
    {
        return $this->periodicityInMinutes;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isActive(): bool
    {
        return $this->isNowRunnable();
    }

    public function isNowRunnable(?DateTime $lastTime = null): bool
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

        if (null === $lastTime) {
            return true;
        }

        $next = clone $lastTime;
        $next->add(new DateInterval("PT{$this->periodicityInMinutes}M"));
        return $next <= $now;
    }

    final public function executeIfRunnable(DaemonState $state): void
    {
        if ($this->isNowRunnable($state->getLastRun())) {
            if (!$state->isRunning()) {
                $state->setRunning(true);
                $state->write();
                try {
                    $state->error(null);
                    $this->execute();
                } catch (Throwable $e) {
                    $state->error($e->getMessage());
                    Logger::logThrowable($e);
                }
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
            'end' => 'string',
            'enabled' => 'bool'
        ])]
        array $info
    ): Daemon {
        return new ($info['class'])(
            $info['name'], $info['periodicity'], $info['start'], $info['end'], $info['enabled']
        );
    }

}
