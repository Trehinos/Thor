<?php

namespace Thor\Tools;

/**
 * Provides static methods to operate on strings.
 *
 * @package          Thor/Tools
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Strings
{

    private function __construct()
    {
    }

    /**
     * Replaces all {key} in $message string by $context[key] value.
     *
     * Values in $context MUST not be arrays or objects (or they MUST define a __toString() method).
     */
    public static function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }

    /**
     *  - If `$str !== '' && $str !== null` :  `return $prefix . $str`,
     *  - otherwise : `return ''`.
     */
    public static function prefix(string $prefix, ?string $str): string
    {
        if ($str === null || $str === '') {
            return '';
        }
        return "$prefix$str";
    }

    /**
     *  - If `$str !== '' && $str !== null` :  `return $str . $suffix`,
     *  - otherwise : `return ''`.
     */
    public static function suffix(?string $str, string $suffix): string
    {
        if ($str === null || $str === '') {
            return '';
        }
        return "$str$suffix";
    }

}
