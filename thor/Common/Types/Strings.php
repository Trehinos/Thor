<?php

namespace Thor\Common\Types;

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
     * Split a first element (head) and the rest of the string (tail) against a specified delimiter. And returns the tail.
     *
     * @param string  $stringToSplit
     * @param string  $delimiter
     * @param ?string &$head
     *
     * @return string tail
     */
    public static function tail(string $stringToSplit, string $delimiter, ?string &$head = null): string
    {
        $parts = explode($delimiter, $stringToSplit);
        $head = $parts[0] ?? '';
        return implode($delimiter, array_slice($parts, 1));
    }

    /**
     * Split a first element (head) and the rest of the string (tail) against a specified delimiter. And returns the head.
     *
     * @param string  $stringToSplit
     * @param string  $delimiter
     * @param ?string &$tail
     *
     * @return string head
     */
    public static function head(string $stringToSplit, string $delimiter, ?string &$tail = null): string
    {
        $parts = explode($delimiter, $stringToSplit);
        $tail = implode($delimiter, array_slice($parts, 1));
        return $parts[0] ?? '';
    }

    /**
     * @param array<string, scalar|Stringable> $context
     */
    public static function interpolate(
        string $string,
        array $context = [],
        PlaceholderFormat $placeholder = PlaceholderFormat::CURLY
    ): string {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_scalar($val) || $val instanceof Stringable) {
                $placeholder->setReplace($replace, $key, $val);
            }
        }
        return strtr($string, $replace);
    }

    /**
     *  - If `$str !== '' && $str !== null` :  returns `"$prefix$str"`,
     *  - otherwise : returns `''`.
     */
    public static function prefix(string $prefix, ?string $str): string
    {
        if ($str === null || $str === '') {
            return '';
        }
        return "$prefix$str";
    }

    /**
     *  - If `$str !== '' && $str !== null` :  returns `"$str$suffix"`,
     *  - otherwise : returns `''`.
     */
    public static function suffix(?string $str, string $suffix): string
    {
        if ($str === null || $str === '') {
            return '';
        }
        return "$str$suffix";
    }

}
