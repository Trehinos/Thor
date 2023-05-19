<?php

namespace Thor\Framework\Factories;

use DateTime;
use Twig\TwigFilter;
use Thor\Web\WebServer;
use Thor\Common\Types\Strings;
use Thor\Common\Types\DateTimes;

/**
 * A factory to create the framework's available twig Filters.
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

    public static function money(
        string $name,
        string $currency = '€',
        int    $decimals = 2,
        string $decimalSeparator = ',',
        string $thousandSeparator = "\u{202F}",
        bool   $symbolBefore = false
    ): TwigFilter {
        return new TwigFilter(
            $name,
            fn(
                $value,
                $c = null,
                $d = null,
                $ds = null,
                $ts = null,
                $sb = null,
            ) => (($sb ?? $symbolBefore) ? ($c ?? $currency) . ' ' : '') .
                number_format($value, $d ?? $decimals, $ds ?? $decimalSeparator, $ts ?? $thousandSeparator) .
                (($sb ?? $symbolBefore) ? '' : ' ' . ($c ?? $currency)),
            ['is_safe' => ['html', 'js']]
        );
    }

    public static function euro(): TwigFilter
    {
        return self::money("euro");
    }

    public static function dollar(): TwigFilter
    {
        return self::money("dollar", '$', 2, '.', "'", true);
    }

}
