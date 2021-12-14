<?php

namespace Thor\Tools;

use DateTime;
use DateTimeInterface;

/**
 * Provides some Date and Time utilities.
 *
 * @package          Thor/Tools
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class DateTimes
{

    private function __construct()
    {
    }

    /**
     * Returns a string from a DateTimeInterface, accordingly to relative time
     * between $relativeTo (default is now) and $date :
     * - `< 24h` and **today** : "H:i"
     * - `< 24h` and **yesterday** : "$yesterday H:i"
     * - `> 24h` : $dateFormat
     */
    public static function getRelativeDateTime(
        DateTimeInterface $date,
        string $dateFormat = 'Y-m-d',
        string $yesterday = 'Yesterday',
        DateTimeInterface $relativeTo = new DateTime()
    ): string {
        $start = clone $relativeTo;
        $start->setTime(23, 59, 59);
        $diff = $date->diff($start);
        if ($diff->days > 1) {
            return $date->format($dateFormat);
        }

        $prefix = '';
        if ($relativeTo->format('Ymd') !== $date->format('Ymd')) {
            $prefix = "$yesterday ";
        }
        return $prefix . $date->format('H:i');
    }

}
