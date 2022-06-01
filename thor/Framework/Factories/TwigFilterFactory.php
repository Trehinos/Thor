<?php

namespace Thor\Framework\Factories;

use DateTime;
use Twig\TwigFilter;
use Thor\Tools\Strings;
use Thor\Web\WebServer;
use Thor\Tools\DateTimes;

/**
 * A factory to create twig Filters.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class TwigFilterFactory
{

    private function __construct()
    {
    }

    /**
     * @param WebServer $server
     *
     * @return TwigFilter
     */
    public static function _(WebServer $server): TwigFilter
    {
        return new TwigFilter(
            '_', // (lang) interpolate
            function (string $str, array $context = []) use ($server) {
                $foundStr = $server->getLanguage()[$str] ?? null;
                if ($foundStr && !empty($context)) {
                    $foundStr = Strings::interpolate($foundStr, $context);
                }
                return $foundStr ?? $str;
            },
            ['is_safe' => ['html']]
        );
    }

    /**
     * @return TwigFilter
     */
    public static function classname(): TwigFilter
    {
        return new TwigFilter(
            'classname',
            fn($value) => substr($value, strrpos($value, '\\') + 1)
        );
    }

    /**
     * @return TwigFilter
     */
    public static function datetimeRelative(): TwigFilter
    {
        return new TwigFilter(
            'datetimeRelative',
            function (string $value) {
                return DateTimes::getRelativeDateTime(DateTime::createFromFormat('YmdHis', $value));
            }
        );
    }

    /**
     * @return TwigFilter
     */
    public static function date2date(): TwigFilter
    {
        return new TwigFilter(
            'date2date',
            fn($valueA, $formatA, $formatB) => $valueA === null
                ? null
                : DateTime::createFromFormat($formatA, $valueA)->format($formatB)
        );
    }

    /**
     * @return TwigFilter
     */
    public static function toUtf8(): TwigFilter
    {
        return new TwigFilter(
            'toUtf8',
            fn($value) => utf8_encode($value)
        );
    }

    /**
     * @return TwigFilter
     */
    public static function fromUtf8(): TwigFilter
    {
        return new TwigFilter(
            'fromUtf8',
            fn($value) => utf8_decode($value)
        );
    }

    /**
     * @return TwigFilter
     */
    public static function format(): TwigFilter
    {
        return new TwigFilter(
            'format',
            fn($value, $format) => sprintf($format, $value)
        );
    }

}
