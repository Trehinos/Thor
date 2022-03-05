<?php

namespace Thor\Tools;

use Stringable;

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
     * Split a first element (head) and the rest of the string (tail) against a specified delimiter.
     *
     * @param string  $stringToSplit
     * @param string  $delimiter
     * @param string& $head
     *
     * @return string tail
     */
    public static function split(string $stringToSplit, string $delimiter, string& $head): string
    {
        $parts = explode($delimiter, $stringToSplit);
        $head = $parts[0] ?? '';
        return implode($delimiter, array_slice($parts, 1));
    }

    /**
     * * If $phpStyle parameter is set false (default) : Replaces all {key} in $message string by $context[key] value.
     * * If $phpStyle parameter is set true : Replaces all $key in $message string by $context[key] value.
     *
     * Values in $context MUST not be arrays or objects (or they MUST define a __toString() method).
     */
    public static function interpolate(string $string, array $context = [], bool $phpStyle = false): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_string($val) || $val instanceof Stringable) {
                if ($phpStyle) {
                    $replace["\$$key"] = $val;
                    continue;
                }
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($string, $replace);
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
