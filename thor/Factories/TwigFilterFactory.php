<?php

namespace Thor\Factories;

use DateTime;
use Twig\TwigFilter;
use Thor\Tools\DateTimeHelper;
use Thor\Http\Server\WebServer;

final class TwigFilterFactory
{

    public static function _(WebServer $server): TwigFilter
    {
        return new TwigFilter(
            '_', // (lang) sprintf ->
            function (string $str, array $arguments = []) use ($server) {
                $foundStr = $server->getLanguage()[$str] ?? null;
                if ($foundStr && !empty($arguments)) {
                    $foundStr = sprintf($foundStr, ...$arguments);
                }
                return $foundStr ?? $str;
            },
            ['is_safe' => ['html'], 'is_variadic' => true]
        );
    }

    public static function classname(): TwigFilter
    {
        return new TwigFilter(
            'classname',
            fn($value) => substr($value, strrpos($value, '\\') + 1)
        );
    }

    public static function datetimeRelative(): TwigFilter
    {
        return new TwigFilter(
            'datetimeRelative',
            function (string $value) {
                return DateTimeHelper::getRelativeInterval(DateTime::createFromFormat('YmdHis', $value));
            }
        );
    }

    public static function date2date(): TwigFilter
    {
        return new TwigFilter(
            'date2date',
            fn($valueA, $formatA, $formatB) => $valueA === null
                ? null
                : \DateTime::createFromFormat($formatA, $valueA)->format($formatB)
        );
    }

    public static function toUtf8(): TwigFilter
    {
        return new TwigFilter(
            'toUtf8',
            fn($value) => utf8_encode($value)
        );
    }

    public static function fromUtf8(): TwigFilter
    {
        return new TwigFilter(
            'fromUtf8',
            fn($value) => utf8_decode($value)
        );
    }

    public static function format(): TwigFilter
    {
        return new TwigFilter(
            'format',
            fn($value, $format) => sprintf($format, $value)
        );
    }

}