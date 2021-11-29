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
final class DateTimeHelper
{

    /**
     * Returns a string from a Date, accordingly to relative time between now and $date :
     * - `< 24h` and **today** : "H:i"
     * - `< 24h` and **yesterday** : "$yesterday H:i"
     * - `> 24h` : $dateFormat
     */
    public static function getRelativeInterval(
        DateTimeInterface $date,
        string $yesterday = 'Yesterday',
        string $dateFormat = 'Y-m-d'
    ): string {
        $now = new DateTime();
        $start = clone $now;
        $start->setTime(23, 59, 59);
        $diff = $date->diff($start);
        if ($diff->days > 1) {
            return $date->format($dateFormat);
        }

        $prefix = '';
        if ($now->format('Ymd') !== $date->format('Ymd')) {
            $prefix = "$yesterday ";
        }
        return $prefix . $date->format('H:i');
    }

}
