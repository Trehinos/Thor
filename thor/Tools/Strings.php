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
     * @param string $stringToSplit
     * @param string $delimiter
     * @param string &$head
     *
     * @return string tail
     */
    public static function split(string $stringToSplit, string $delimiter, string &$head): string
    {
        $parts = explode($delimiter, $stringToSplit);
        $head = $parts[0] ?? '';
        return implode($delimiter, array_slice($parts, 1));
    }

    public static function token(string $stringToSplit, string $delimiter, string &$tail): string
    {
        $parts = explode($delimiter, $stringToSplit);
        $tail = implode($delimiter, array_slice($parts, 1));
        return $parts[0] ?? '';
    }

    public static function trimOrPad(string $str, int $length, string $pad = ' ', int $padType = STR_PAD_LEFT): string
    {
        $str = trim($str, $pad);
        $padLen = $length - mb_strlen($str);
        if ($padType === STR_PAD_LEFT) {
            return str_repeat($pad, $padLen) . Strings::right($str, $length);
        }
        return Strings::left($str, $length) . str_repeat($pad, $padLen);
    }

    public static function left(string $str, string $length): string
    {
        return mb_substr($str, 0, $length);
    }

    public static function right(string $str, string $length): string
    {
        return mb_substr($str, -$length, mb_strlen($str));
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
