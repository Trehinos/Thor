<?php

namespace Thor\Tools;

use DateTime;
use DateTimeInterface;

final class DateTimeHelper
{

    public static function getRelativeInterval(DateTimeInterface $date): string
    {
        $now = new DateTime();
        $start = clone $now;
        $start->setTime(23, 59, 59);
        $diff = $date->diff($start);
        if ($diff->days > 1) {
            return $date->format('d/m/Y');
        }

        $prefix = '';
        if ($now->format('Ymd') !== $date->format('Ymd')) {
            $prefix = 'Hier ';
        }
        return $prefix . $date->format('H:i');
    }

}
